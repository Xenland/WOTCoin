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
									
									}else if(count($address_awaiting) == 0){
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
					
					if($_COOKIE[$cookie_session["name"]] != NULL){
						//Split cookie
						$splitCookie = explode("|", $_COOKIE[$cookie_session["name"]]);
						
						//Declare and recast variables
						$address_id		= (int) $splitCookie[0];
						$session_expiration	= (int) $splitCookie[1];
						$hash			= $splitCookie[2];
						
						//Query for address session salt
						$address_info_q = wot_doQuery("SELECT `session_salt` FROM `address_index` WHERE `id` = ? LIMIT 0,1", $address_id);
						$address_info	= $address_info_q->fetch();
						
						if($address_info["session_salt"] != ''){
						
							$severside_session_key = hash("sha512", $address_id.$address_info["session_salt"].$session_expiration.session_salt);
							$severside_session_hash = hash("sha512", $address_id.$address_info["session_salt"].$session_expiration.session_salt.$severside_session_key);
							/*$severside_session_key =  $address_id.$address_info["session_salt"].$session_expiration.session_salt;
							$severside_session_hash = $address_id.$address_info["session_salt"].$session_expiration.session_salt.$severside_session_key;*/
							
							if($severside_session_hash == $hash){
								$output["return_status"] = 1;
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
?>