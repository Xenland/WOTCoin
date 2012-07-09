<?php
/*
	Dev Author: Shane B. (Xenland)
	Contributors: ...
	Notes: This page should be always in the ./backend folder and shouldn't ever be moved from that folder.
*/
//Website Config
$wot_site_config["title"] = "Web of Trust";

define("session_salt", "BqQ72CJ1BQeGXabt1WDTfaOXTcqn2TBtaiq2L0TcGXIuYKNPxhyDidq1fHe7qGrqOHjpy3r4R0oFB2lZ9KshNJ9TgdQxJ8OoFXD4QVYxMd3e5e35PldtUdc1gToQR24nQyhwk6UX9N15SU0x54QQ7TIeCWUkPPywdFToBDbBh2w0NnnIh4pfOXjhK4rqJPMNlv1NZ3e6VBXyOb7W5n1KabRK690GPDj0ZbEO4JLQayfOzcAvps5quN1rMSXrl7ia8NP3mqKmPP1eSsA8Lwo59fnMXa497d96QP035AfKf7PXpgV0CaWBg9e49837b34RIUKEkQfpNUd21ZTt0FV6EZ0DYTBZNvHlfhQqYVFBFJuxzdQpICldsbHgV85ztCLzKrPycl0IVl5kpLzXeK1wMyCywyXQ0ygBPWZS7QqT1l3gXs413VoGjR4GfSm6hsxXfnFc3VWV7P1V8WN2I1ySItoOcBKjU77ZkC2doOZmtR7sDKkcBJUa38O5zofjmc8w");

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
