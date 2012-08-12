<?php
/*
	Dev Author: Shane B. (Xenland)
	Contributors: ...
	Notes: This page should be always in the ./backend folder and shouldn't ever be moved from that folder
	Notes: Remeber to keep in mind of order of operations counts as in, Variables go first(configuration.php), then functions....
*/

//Include configuration file
require_once("configuration.php");

//Include Bitcoin Library
require_once("includes/bitcoin-php/bitcoin.php");

//Include global/universal functions
require_once("includes/functions/databasefunctions.php"); // (DB Functions should probubly always go first(besides configuration.php)
require_once("includes/functions/global_functions.php");
require_once("includes/functions/bitcoinfunctions.php");
?>