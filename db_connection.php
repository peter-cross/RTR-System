<?php
/**
 * File for establishing connection with database
 * @author Peter Cross
 * @version Nov 19, 2017
 */

// Add file with settings for database
require_once $DB_INFO_FILE; 			
// Add file for SQL interaction with database
require_once $SQL_INTERACTION_FILE;
// Add file with database entity classes
require_once $ENTITY_CLASSES_FILE;
	
// Create object for established database connection
$dbLink = new mysqli( $host, $user, $password, $dbname );

// If object for database connection is created
if ( $dbLink )
	// Message for successful connection
	$dbMsg = "Connection established!";	

// Otherwise
else
{
	// Display error message
	print "Can not establish connection to database: " . $dbLink->connect_error;
	
	// Message for unsuccessful connection
	$dbMsg = "No connection with database";
}

// If XML file with settings for restaurant exists
if ( file_exists( $XML_FILE ) )
	// Read XML file and create object with available information
	$xmlObj = simplexml_load_file( $XML_FILE );
	
// Otherwise
else
	// Assign null as object for XML file
	$xmlObj = null;
?>