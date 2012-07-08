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
						global $_POST, $_GET, $_COOKIE, $include_header, $wot_site_config, $wot_header_config;
						
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