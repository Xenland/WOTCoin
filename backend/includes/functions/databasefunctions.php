<?php
/*
	Dev Author: Shane B. (Xenland)
	Contributors: ...
	
	[Database Functions]
	********************
*/

//Connect to a database (Not generally used Directly -- Look towards wot_doQuery or wot_doQuery_returnId)
function wot_connectToDB(){
	global $db;

	//Define local variables
	$output = 0;
	
	//Connect to the mysql database, then select which database we'd like to use (by default for now)	
	$dbh = new PDO('mysql:host='.$db["host"].';dbname='.$db["database"], $db["username"], $db["password"]);
	$dbh->setAttribute (PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //Set the error mode handling
	
	//Return connection
	$output = $dbh;
	return ($output);
}


//Execute a query
function wot_doQuery(){
	//Define local variables
	$argument		= func_get_args();
	$prepared_query	= $argument[0]; //Define prepared query (for simplicity)
	
	$sql_statement		= '';
	$sql_execute_array;
	
	
	//Connect to the database's pipe (Fire ze missiles!)
	$db_pipe = wot_connectToDB();
	
	
	//Prepare SQL statement (DB engine cache/verify syntax process)
	$sql_statement = $db_pipe->prepare($prepared_query);
	
		//Prepare SQL statement (insert values/data process)
		$argument_current = 0; /* We count the current arugment we are looking at so we don't use the prepared_query as an argument by accident/purpose/deviant */
		foreach($argument as $arg){
			$argument_current++;
			if($argument_current > 1){
				//Add argument to prepared statment (scince its not $prepared_query)
				$sql_execute_array[] = $arg;
			}
		}
		
	//Execute SQL statement
	$sql_statement->execute($sql_execute_array);
	
	//Output                                                                                      (But, I am Le tired.....)
	return $sql_statement;
}


//Execute a query that inserts into the database return an Integer of the id number
function wot_doQuery_returnId(){
	//Define local variables
	$output = 0;
	$argument		= func_get_args();
	$prepared_query	= $argument[0]; //Define prepared query (for simplicity)
	
	$sql_statement	= '';
	$sql_execute_array;
	
	
	//Connect to the database's pipe (Fire ze missiles!)
	$db_pipe = wot_connectToDB();
	
	
	//Prepare SQL statement (DB engine cache/verify syntax process)
	$sql_statement = $db_pipe->prepare($prepared_query);
	
		//Prepare SQL statement (insert values/data process)
		$argument_current = 0; /* We count the current arugment we are looking at so we don't use the prepared_query as an argument by accident/purpose/deviant */
		foreach($argument as $arg){
			$argument_current++;
			if($argument_current > 1){
				//Add argument to prepared statment (scince its not $prepared_query)
				$sql_execute_array[] = $arg;
			}
		}
		
	//Execute SQL statement
	$sql_statement->execute($sql_execute_array);
	
	//output == return id
	$output = $db_pipe->lastInsertId();
	
	return $output;
}
?>