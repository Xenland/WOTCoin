<?php
/*
	Dev Author: Shane B. (Xenland)
	Contributors: ...
	Notes: This page should be always in the ./backend folder and shouldn't ever be moved from that folder.
*/
//Website Config
$wot_site_config["title"] = "Web of Trust";


//Base directory
$base_directory = "/home/websites/wotcoin/"; //Don't for get to have the slash at the end.

$include_header = $base_directory."backend/includes/general/header.php";
$include_footer = $base_directory."backend/includes/general/footer.php";

//Cookie Names
$cookie_session["name"]		= "WOTCoin";
$cookie_session["domain"]	= "127.0.0.1";
$cookie_session["folder"]		= "/";
$cookie_session["ssl_only"]	= false;

//Bitcoin Daemon 
$bitcoin_client["host"]		= "127.0.0.1";
$bitcoin_client["https"]		= "http";
$bitcoin_client["username"]	= "username";
$bitcoin_client["password"]	= "password";	
$bitcoin_client["port"]		= 4367;

//Database connection information
$db["host"]			= "localhost";
$db["username"]		= "root";
$db["password"]		= "fuckthat";
$db["database"]		= "wotcoin";


?>
