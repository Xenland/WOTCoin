<?php
/*
	Dev Author: Shane B. (Xenland)
	Contributors: ...
	
	Connect to Bitcoin Client
		Not required to be used to connect to Bitcoin but it is sure convient
*/
function OpenBitcoinClient(){
	global $bitcoin_client;
	
	/*
		Return Status List (Reference | Update as needed)
		0 = Nothing Executed;
		1= Success
		100 = Connection Failed
	*/
	
	//Declare default variables (Sanatize after Declaration)
	$output 					= Array();
	$output["return_status"]		= 0;
	$output["connection_tunnel"]	= '';
	
	//Attempt to make a connection with Bitcoin Client
	try{
		$bitcoin_connection = new BitcoinClient($bitcoin_client["https"], $bitcoin_client["username"], $bitcoin_client["password"], $bitcoin_client["host"], $bitcoin_client["port"]);

	}catch(Exception $e){
		$bitcoin_connection = false;
	}
	
	if($bitcoin_connection != false){
		//Connection success
		$output["return_status"] = 1;
		$output["connection_tunnel"] = $bitcoin_connection;
		
	}else{
		//Connection failed
		$output["return_status"] = 100;
		
	}
	
	return $output;
}


/*
	There is a difference Between our Bitcoin-php libraries Bitcoin() and BitcoinClient() functions check out the documentation to see the difference: http://code.gogulski.com/bitcoin-php/
	
*/
function OpenBitcoinClient_noconnection(){	
	/*
		Return Status List (Reference | Update as needed)
		0 = Nothing Executed;
		1= Success
		100 = Connection Failed
	*/
	
	//Declare default variables (Sanatize after Declaration)
	$output 					= Array();
	$output["return_status"]		= 0;
	$output["connection_tunnel"]	= '';

	$bitcoin_connection = new Bitcoin();
	
	if($bitcoin_connection != false){
		//Connection success
		$output["return_status"] = 1;
		$output["connection_tunnel"] = $bitcoin_connection;
		
	}else{
		//Connection Failed
		$output["return_status"] = 100;
	}
	
	return $output;
}


/*
	Verify address/signature/message group
*/
function verifyMessage($address, $signature, $message){	
	/*
		Return Status List (Reference | Update as needed)
		0 = Nothing Executed;
		1= Success
		100 = Connection failed with Bitcoin client.
		101 = Query failure? (Not sure what happens to create this error, But it is atleast acknolwedged it can happen, Please report how to create this error if you know)
	*/
	
	//Declare default variables (Sanatize after Declaration)
	$output				= Array();
	$output["return_status"]	= 0;
	
	
	//Connect to Bitcoin
	$Bitcoin_connection = OpenBitcoinClient();
	if($Bitcoin_connection["return_status"] == 1){
	
		//Connection success, Now verify the message.
		$message_matches_signature_query = $bitcoin_connection->query("verifymessage", $address, $signature, $message);
		if($message_matches_signature_query == true){
			//That message/address/signature pair is valid
			$output["return_status"] = 1;
			
		}else if($message_matches_signature_query == false){
			//That message/address/signature pair is NOT valid
			$output["return_status"] = 100;
		}else{
			
			//Not sure what happened, But it wasen't good, Failure
			$output["return_status"] = 101;
		}
	}else{
		$output["return_status"] = 100;
	}
	
	return $output;
}


function register_address_step1($address){
	/*
		Return Status List (Reference | Update as needed)
		0 = Nothing Executed;
		1= Success
		100 = Connection failed with Bitcoin
		101 = Address was invalid
	*/
	
	//Declare default variables (Sanatize after Declaration)
	$output				= Array();
	$output["return_status"]	= 0;
	$output["return_status_message"] = 'Something went awry';
	$output["authentication_message"] = '';
	
	//Sanatize
	$address = trim($address);
	
	//Connect to Bitcoin
	$Bitcoin_connection = OpenBitcoinClient_noConnection();
	
	if($Bitcoin_connection["return_status"] == 1){
		//Check if the address is a valid address...
		$address_valid = $Bitcoin_connection["connection_tunnel"]->checkAddress($address);
		
		if($address_valid == 1){
			//Check if the address is already registered
			$address_registered_q = wot_doQuery("SELECT `id` FROM `address_index` WHERE `address` = ? LIMIT 0,1", $address);
			$address_registered = $address_registered_q->fetch();
			
			if($address_registered["id"] == 0){
				//Address is a valid Bitcoin address
					//Generate message
					$generated_message = $address."-".wot_generateRandomString(512);
					
					//check if address is currently already in the register db
					$address_exists_q = wot_doQuery("SELECT `id` FROM `address_authentication_awaiting_index` WHERE `address_to_register` = ? LIMIT 0,1", $address);
					$address_exists	= $address_exists_q->fetch();
					
					if($address_exists["id"] > 0){
						//Address is in the database, Update the generated message for the "supposed" owner to sign and send back to us verifications
						$message_id_q = wot_doQuery("UPDATE `address_authentication_awaiting_index` SET `message` = ? WHERE `id` = ? AND `address_to_register` = ? LIMIT 1", $generated_message, $address_exists["id"], $address);
					
					}else{
						//Address is not in the database, Generate a message for the "supposed" owner to sign and send back to us verifications
						$message_id = wot_doQuery_returnId("INSERT INTO `address_authentication_awaiting_index` (
																				`timestamp_attempt_to_register`,
																				`address_to_register`,
																				`message`
																			)
																			
																		VALUES(
																				?,
																				?,
																				?
																			)", 
																			time(),
																			$address,
																			$generated_message);
					}
					
					//Assuming the queries above worked
					$output["return_status"] = 1;
					$output["authentication_message"] = $generated_message;
			}else if($address_registered["id"] > 0){
				$output["return_status"] = 102;
				$output["return_status_message"] = "That address is already registered with us";
			}
				
		}else{
			$output["return_status"] = 101;
			$output["return_status_message"] = "That address dosen't appear to be valid.";
		}
	}else{
		//Connection failed
		$output["return_status"] = 100;
		$output["return_status_message"] = "Unable to connect to the Bitcoin network, we are under going matience. Please report this issue if it persists longer than 24 hours.";
	}
	
	return $output;
}



function register_address_step2($address, $signature){
	/*
		Return Status List (Reference | Update as needed)
		0 = Nothing Executed;
		1= Success
		100 = Connection failed with Bitcoin
		101 =  Signature didn't match
	*/
	
	//Declare default variables (Sanatize after Declaration)
	$output				= Array();
	$output["return_status"]	= 0;
	$output["return_status_message"] = 'Something went awry';
	
	//Sanatize
	$address = trim($address);
	$signature = trim($signature);
	$message = trim($message);
	
	
	//Connect to Bitcoin
	$Bitcoin_connection = OpenBitcoinClient();
	if($Bitcoin_connection["return_status"] == 1){
		//Verify that the message was valid
			//Query for Message
			$message_q = wot_doQuery("SELECT `message` FROM `address_authentication_awaiting_index` WHERE `address_to_register` = ? LIMIT 0,1", $address);
			$message = $message_q->fetch();
			
		try{
			$message_valid = $Bitcoin_connection["connection_tunnel"]->query("verifymessage", $address, $signature, $message["message"]);

		}catch(Exception $e){
			$message_valid = 0; //Invoke a 102
		}
		
		
		if($message_valid == true){
			//Add address to database
			$address_exists_q	= wot_doQuery("SELECT `id` FROM `address_index` WHERE `address` = ? LIMIT 0,1", $address);
			$address_exists		= $address_exists_q->fetch();
			
			if($address_exists["id"] == 0){
				$session_salt = wot_generateRandomString(1000);
				wot_doQuery_returnId("INSERT INTO `address_index` (`address`, `timestamp_added`, `session_salt`) VALUE(?, ?, ?)", $address, time(), $session_salt);
				
				wot_createsession();
			
				$output["return_status"] = 1;
				$output["return_status_message"] = '';
			}else if($address_exists["id"] > 0){
				$output["return_status"] = 102;
				$output["return_status_message"] = "That address is already registered with this service.";
			}
			
		}else if($message_valid == false){
			$output["return_status"] = 101;
			$output["return_status_message"] = 'That Signature did not match the message and Bitcoin address that was inputted';
			
		}else{
			$output["return_status"] = 102;
			$output["return_status_message"] = 'Unable to connect to the Bitcoin network, we are under going matience. Please report this issue if it persists longer than 24 hours.';
		}
	}else{
		//Connection failed
		$output["return_status"] = 100;
		$output["return_status_message"] = "Unable to connect to the Bitcoin network, we are under going matience. Please report this issue if it persists longer than 24 hours.";
	}
	
	return $output;
	
}
?>