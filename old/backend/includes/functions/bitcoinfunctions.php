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
		try{
			$message_matches_signature_query = $Bitcoin_connection["connection_tunnel"]->query("verifymessage", $address, $signature, $message);
		}catch(Exception $e){
			$message_matches_signature_query = false;
		}
		
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
?>