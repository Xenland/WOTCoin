<?php
/*
	Dev Author: Shane B. (Xenland)
	Contributors: ...
	Purpose: Manages the main functions required to run WOTCoin
	Notes; ...
*/


/*
	[Assistive Functions]
	********************
*/







/*
					[Misc. Functions]
					********************
*/

					//Generates a random alphanumerical string at the set length
					function wot_generateRandomString($random_string_length){
						$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
						$string = '';
						for ($i = 0; $i < $random_string_length; $i++) {
							$string .= $characters[rand(0, strlen($characters) - 1)];
						}
						return $string;
					}


					//Figures out who we are awaiting on
					function wot_awaitingOnMeOrSwim($me_status, $swim_status){
						/*
							Status Listing
								Me Status
								1 = "me" has signed the message; 
								2 = "me" has declared they have completed their end of the bargin
								3 = "me" has declared "swim" has completed their end of the bargin
								
								Swim Status
								0 = Awaiting on Swim to approve of the transaction message and sign it
								1 = Swim signed the transaction;
								2 = "Swim" has declared they have completed their end of the barin
								3 = "Swim" has declared "me" has completed their endd of the bargin
								
						*/
						$output = -1; //-1 = Somethings not correct; 0 = nuetrual; 1 = me;  2 = swim;
						
						//Figure out "me" status then evaluate "swim" status to determine who we are awaiting on.
						
						
						if($me_status == 0){
							if($swim_status == 1){
								//Me is 0 and Swim is 1 = awating on me
								$output = 1;
							}
						}
						
						
						
						if($me_status == 1){
							//What is swim status?
							if($swim_status == 0){
								$output = 2;
							}else if($swim_status == 1){
								$output = 0;
							}
						}
						
						
						return $output;
					}
					
					
					//Output, figure out which one is me or swim and output both data
					function wot_distinct_me_and_swim($address_a, $address_a_status, $address_b, $address_b_status){
						global $wot_session;
						/*
							Return Status List (Reference | Update as needed)
							0 = Nothing Executed;
							1= Success
						*/

						//Declare default variables (Sanatize after Declaration)
						$output 					= Array();
						$output["return_status"]		= 0;
						$output["me_address"]		= '';
						$output["me_status"]		= 0;
						$output["swim_address"]		= '';
						$output["swim_status"]		= 0;
						
						$address_a	= trim($address_a);
						$address_a_status	= (int) $address_a_status;
						$address_b		= trim($address_b);
						$address_b_status	= (int) $address_b_status;
						
						if($address_a == $wot_session["address"] && $address_b != $wot_session["address"]){
							$output["me_status"] = $address_a_status;
							
							$output["swim_address"] = $address_b;
							$output["swim_status"] = $address_b_status;
						}else if($address_b == $wot_session["address"] && $address_a != $wot_session["address"]){
							$output["me_status"] = $address_b_status;
							
							$output["swim_address"] = $address_a;
							$output["swim_status"] = $address_a_status;
						}
						
						return $output;
					}






/*
					[Header Functions]
					********************
*/
					//Display Header
					function wot_header(){
						/*
							Global Variables Explaination
							$wot_site_config , Trickles down to the sub functions found in the header.php file mainly the wot_header_title() function
							$wot_header_config , Trickles down to the sub functions found in the header.php file mainly the wot_header_title() function
						*/
						global $_POST, $_GET, $_COOKIE, $include_header, $wot_site_config, $wot_header_config, $wot_session;
						
						//Include Header
						include($include_header);
						
						//Always return true.
						return true;
					}

					//Display a suffix of <title></title> if nessecary
					function wot_header_title(){
						global $wot_site_config, $wot_header_config;
						
						//Display the Prefix of the title
						echo $wot_site_config["title"];
						
						if($wot_header_config["title"] != ''){
							echo " | ".$wot_header_config["title"];
						}
						
						return true;
					}
					
					
					
					
/*
					[Footer Functions]
					********************
*/
					//Display Header
					function wot_footer(){
						global $_POST, $_GET, $_COOKIE, $include_footer;
						
						//Include Header
						include($include_footer);
						
						//Always return true.
						return true;
					}	




/*
				[Session Functions]
*/

				function wot_login_step1($address){
					/*
						Return Status List (Reference | Update as needed)
						0 = Nothing Executed;
						1= Success
					*/

					//Declare default variables (Sanatize after Declaration)
					$output 					= Array();
					$output["return_status"]		= 0;
					$output["return_status_message"] = '';
					$output["authentication_message"] = '';
					
					//Sanatize variables
					$address = trim($address);
					
						//Connect to Bitcoin
						$Bitcoin_connection = OpenBitcoinClient_noConnection();
						
						if($Bitcoin_connection["return_status"] == 1){
							//Check if the address is a valid address...
							$address_valid = $Bitcoin_connection["connection_tunnel"]->checkAddress($address);
							
							if($address_valid == 1){
								//Generate message
								$generated_message = $address."-".wot_generateRandomString(512);
					
								//Update the generated message for the "supposed" owner to sign and send back to us verifications
									//Insert or update?
									$address_awaiting_q = wot_doQuery("SELECT `id` FROM `address_authentication_awaiting_index` WHERE `address_to_register` = ? LIMIT 0,1", $address);
									$address_awaiting = $address_awaiting_q->fetch();

									if($address_awaiting["id"] > 0){
										//Address IS in the database, Generate a message for the "supposed" owner to sign and send back to us verifications
										$message_id_q = wot_doQuery("UPDATE `address_authentication_awaiting_index` SET `message` = ? WHERE `id` = ? AND `address_to_register` = ? LIMIT 1", $generated_message, $address_awaiting["id"], $address);
									
									}else if($address_awaiting["id"] == 0){
										//Address is not in the database, Generate a message for the "supposed" owner to sign and send back to us verifications
										$message_id_q = wot_doQuery_returnId("INSERT INTO `address_authentication_awaiting_index` (
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
				

				function wot_login_step2($address, $signature){
					/*
						Return Status List (Reference | Update as needed)
						0 = Nothing Executed;
						1= Success
						100 = Connection failed with Bitcoin
						101 =  Signature didn't match
						102 = IDK?
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
							$message_valid = 102; //Invoke a 102
							print_r($e);
						}
						
						
						if($message_valid == true){
							//Add address to database
							$address_exists_q	= wot_doQuery("SELECT `id` FROM `address_index` WHERE `address` = ? LIMIT 0,1", $address);
							$address_exists	= $address_exists_q->fetch();
							
							if($address_exists["id"] == 0){
								$session_salt = wot_generateRandomString(1000);
								wot_doQuery_returnId("INSERT INTO `address_index` (`address`, `timestamp_added`, `session_salt`) VALUE(?, ?, ?)", $address, time(), $session_salt);
								wot_createSession($address);
							
								$output["return_status"] = 1;
								$output["return_status_message"] = '';
							}else if($address_exists["id"] > 0){
								
								
								wot_createSession($address);
								
								$output["return_status"] = 1;
								$output["return_status_message"] = '';
							}
							
							//Randomize the message so noone else can use the previouslyed used signemessage to sign in them selves (By means of javascript injection, maybe clipboard scanner,etc)
							wot_doQuery("UPDATE `address_authentication_awaiting_index` SET `message` = ? WHERE `address_to_register` = ? LIMIT 1", wot_generateRandomString(1000), $address);
							
							
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
				
				
				
				
				function wot_createSession($address){
					global $cookie_session;
					
					/*
						Return Status List (Reference | Update as needed)
						0 = Nothing Executed;
						1= Success
					*/
					
					//Declare default variables (Sanatize after Declaration)
					$output				= Array();
					$output["return_status"]	= 0;
					
					
					//Query for if address exists in database
					$address_exists_q  = wot_doQuery("SELECT `id`, `session_salt` FROM `address_index` WHERE `address` = ? LIMIT 0,1", $address);
					$address_exists	= $address_exists_q->fetch();
					
					if($address_exists["id"] > 0){
						//Create a key/secret for the session/cookie
						$session_expiration = time() + 3600;
						
						$session_key = hash('sha512', $address_exists["id"] .$address_exists["session_salt"].$session_expiration.session_salt);
						$session_hash = hash('sha512',$address_exists["id"].$address_exists["session_salt"].$session_expiration.session_salt.$session_key);
						/*$session_key =$address_exists["id"] .$address_exists["session_salt"].$session_expiration.session_salt;
						$session_hash = $address_exists["id"].$address_exists["session_salt"].$session_expiration.session_salt.$session_key;*/
						
						//Now ship the hash into the cookie for sessioning....
						$session_cookie_data = $address_exists["id"]."|".$session_expiration."|".$session_hash;
						if(setcookie($cookie_session["name"], $session_cookie_data, $session_expiration, $cookie_session["folder"], $cookie_session["domain"], $cookie_session["ssl_only"])){
							//Do something here upon succesfull cookie setting (if nessecary)
								//Define the $_COOKIE variables becuase it prevents use from having to refresh the page for use to continue with code
								$_COOKIE[$cookie_session["name"]] = $session_cookie_data;
								$output["return_status"] = 1;
						}else{
							//Cookie failed to set, consider this whole operation a fail-URE!!!
							$output["return_status"] = 100;
							$output["return_status_message"] = "Please set your browser cookie settings to 'Accept' to continue";
						}
					}
					
					return $output;
				}
				
				
				function wot_verifySession(){
					global $_COOKIE, $cookie_session;
					
					/*
						Return Status List (Reference | Update as needed)
						0 = Nothing Executed;
						1= Success
					*/
					
					//Declare default variables (Sanatize after Declaration)
					$output				= Array();
					$output["return_status"]	= 0;
					$output["return_status_message"] = '';
					$output["address_id"] = 0;
					$output["address"] = '';
					
					if($_COOKIE[$cookie_session["name"]] != NULL){
						//Split cookie
						$splitCookie = explode("|", $_COOKIE[$cookie_session["name"]]);
						
						//Declare and recast variables
						$address_id		= (int) $splitCookie[0];
						$session_expiration	= (int) $splitCookie[1];
						$hash			= $splitCookie[2];
						
						//Query for address session salt
						$address_info_q = wot_doQuery("SELECT `address`, `session_salt` FROM `address_index` WHERE `id` = ? LIMIT 0,1", $address_id);
						$address_info	= $address_info_q->fetch();
						
						if($address_info["session_salt"] != ''){
						
							$severside_session_key = hash("sha512", $address_id.$address_info["session_salt"].$session_expiration.session_salt);
							$severside_session_hash = hash("sha512", $address_id.$address_info["session_salt"].$session_expiration.session_salt.$severside_session_key);
							/*$severside_session_key =  $address_id.$address_info["session_salt"].$session_expiration.session_salt;
							$severside_session_hash = $address_id.$address_info["session_salt"].$session_expiration.session_salt.$severside_session_key;*/
							
							if($severside_session_hash == $hash){
								$output["return_status"] = 1;
								
								$output["address"] = $address_info["address"];
								$output["address_id"] = $address_id;
							}else{
								$output["return_status"] = 102;
								$output["return_status_message"] = "Hash didn't match";
							}
							
						}else{
							$output["return_status"] = 101;
							$output["return_stauts_message"] = "No session found";
						}
					}else{
						$output["return_status"] = 100;
						$output["return_status_message"] = "No cookie";
					}
					
					return $output;
				}
				
				
				function wot_detect_session_ended(){
					global $wot_session;
					
					if($wot_session["return_status"] != 1){
						header("Location:/sessionended");
						exit;
					}
					
					return true;
				}
				
				
				
				
				/*
					[ Transaction Functions ]
					************************
				*/
				function wot_initiate_tx($address_to_initiate_with, $message_to_sign, $signature){
					global $wot_session;
					
					/*
						Return Status List (Reference | Update as needed)
						0 = Nothing Executed;
						1= Success
					*/
					
					//Declare default variables (Sanatize after Declaration)
					$output				= Array();
					$output["return_status"]	= 0;
					$output["return_status_message"] = '';
					
					//validate the user has a valid session
					if($wot_session["return_status"] == 1){
						//Validate this address is... just that
						$Bitcoin = OpenBitcoinClient_noconnection();
						$address_valid = $Bitcoin["connection_tunnel"]->checkAddress($address_to_initiate_with);
						
						if($address_valid == 1){
							//The address is valid, initiate transaction
								//Check if the address we are initiating with has a id with us to import into the database with for more data
								$address_b_id = 0;
								$address_b_id_q = wot_doQuery("SELECT `id` FROM `address_index` WHERE `address` = ? LIMIT 0,1", $address_to_initiate_with);
								$address_b_id_a = $address_b_id_q->fetch();
								
								if($address_b_id_a["id"] > 0){
									$address_b_id = $address_b_id_a["id"];
								}
								
								$initiation_id = wot_doQuery_returnId("INSERT INTO `feedback_queue_index` (`timestamp_initiated`, `address_a_id`, `address_a`, `address_a_signature`, `address_a_status`, `address_b`, `address_b_id`, `message_to_sign`)
																			VALUES(
																				?,
																				?,
																				?,
																				?,
																				?,
																				?,
																				?,
																				?)",
																				time(),
																				$wot_session["address_id"],
																				$wot_session["address"],
																				$signature,
																				'1',
																				$address_to_initiate_with,
																				$address_b_id,
																				$message_to_sign);
								if($initiation_id > 0){
									$output["return_status"] = 1;
									
								}else{
									$output["return_status"] = 102;
									$output["return_status_message"] = "We were unable to add the transaction details to the database. Please report this issue if it is not fixed in the next 24 hours.";
								}
						}else{
							$output["return_status"] = 100;
							$output["return_status_message"] = 'That address dosen\' appear to be valid';
						}
						
					}
					
					return $output;
				}
				
				
				
				
				
				/* 
					[ Transaction Functions ]
				*/
				
				function wot_tx_queue_information($tx_id){
					global $wot_session;
					
					/*
						Return Status List (Reference | Update as needed)
						0 = Nothing Executed;
						1= Success
					*/
					
					//Declare default variables (Sanatize after Declaration)
					$output				= Array();
					$output["return_status"]	= 0;
					$output["return_status_message"] = '';
					$output["db_data"] = Array();
					
					//Query for transaction
					$transaction_info_q = wot_doQuery("SELECT `id`, `timestamp_initiated`, `address_a_id`, `address_a`, `address_a_signature`, `address_a_status`, `address_b_id`, `address_b`, `address_b_status`, `address_b_signature`, `message_to_sign` FROM `feedback_queue_index` WHERE `id` = ? AND (`address_a` = ? OR `address_b` = ?) LIMIT 0,1", $tx_id, $wot_session["address"], $wot_session["address"]);
					$transaction_info	= $transaction_info_q->fetch();
					
					if($transaction_info["id"] == $tx_id){
						$output["db_data"] = $transaction_info;
						$output["return_status"] = 1;
					}else{
						$output["return_status"] = 100;
						$output["return_status_message"] = "Unable to find that transaction within your permission levels.";
					}
					
					return $output;
				}
?>