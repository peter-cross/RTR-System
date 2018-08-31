<?php
/**
 * File with functions for interacting with database
 * @author Peter Cross
 * @version Nov 19, 2017
 */

/**
 * Executes SQL query and returns result of execution
 * var $query - SQL query text
 */
function doQuery( $query )
{
	// Link for established DB connection
	global $dbLink;
	
	// If DB connection is not established
	if ( !$dbLink )
		return false;
	
	// Max # of times to try to execute the query
	$limit = 1000;
	// Variable for storing results of execution
	$queryResult = false;
	
	// Loop until there is result of successful execution of query or reach limit of # of times
	while ( !$queryResult && $limit--> 0 )
		// Execute SQL query and get results of execution
		$queryResult = $dbLink->query( $query );
	
	// If there are no results of successful execution
	if ( !$queryResult )
		return false;
	
	return $queryResult;
}

/**
 * Executes SQL query and resturns result in the form of array of objects
 * var $query - Text of SQL query
 */
function getQueryResultsArray( $query )
{
	// Execute query and get results
	$result = doQuery( $query );

	// Array for storing results of execution 
	$resArr = [];
	
	// If there is result of successful execution of the query
	if ( $result )
		// Loop while we can fetch the next row of execution results
		while( $row = $result->fetch_object() )
			// Add to array fetched object decoded into array element
			$resArr[] = json_decode( json_encode( $row, true ) );
		
	// Return result of execution as decoded array of objects
	return json_decode( json_encode( $resArr ) );
}

/**
 * Starts SQL transaction
 */
function startTransaction()
{
	global $dbLink;
	$dbLink->begin_transaction();
}

/**
 * Commits results of SQL transaction and returns result if commitment was successful
 */
function commitTransaction()
{
	global $dbLink;
	return $dbLink->commit();
}	

/**
 * Rolls back SQL transaction
 */
function rollbackTransaction()
{
	global $dbLink;
	$dbLink->rollback();
}

/**
 * Closes database connection
 */
function closeConnection()
{
	global $dbLink;
	$dbLink->close();
}

/**
 * Creates query text for array of values into one INSERT SQL query 
 * var $entity - Array of database entities or single database entity
 * var $entityName - Name of database entity
 * var $entityFunc - Function to process database entity array element
 * var $addArg - Additional argument for the function
 */
function addQueryTxt( $entity, $entityName, $entityFunc, $addArg="" )
{
	// Variable to store entity values for SQL query
	$values = "";
	// Get number of entity array elements
	$num = count( $entity );
	
	// If number of elements is more than one
	if ( $num > 1 )
		// Loop for each entity array element
		for ( $i=0; $i < $num; $i++ ) 
		{
			// Execute provided function for processing database entity array element 
			// and add result of execution to text of SQL query
			$values .= $entityFunc( $entity[$i], $addArg );
			
			// If it's not the last array element
			if ( $i < $num-1 )
				// Add comma separator to text of SQL query
				$values .= ", ";
		}
	// Otherwise
	else
		// Execute provided function for processing single entity array element 
		// and assign result of execution to text of SQL query
		$values = $entityFunc( $entity, $addArg );
		
	// Create text of SQL query and return it
	return "INSERT INTO $entityName VALUES $values ; ";
}

/**
 * Creates Insert SQL query for provided location or locations
 * var $location - array of elements or single object for location
 */
function addLocationQueryTxt( $location )
{
	/**
	 * Function that will be executed to process location object
	 * var $l - location object
	 */
	$func = function( $l )
	{
		$locationID = trim( $l->id );
		$name = trim( $l->name );
		$description = trim( $l->description );
		
		return "( $locationID, \"$name\", \"$description\" )";
	};
	
	// Create query text for inserting privided location entity objects
	return addQueryTxt( $location, "Location", $func );
}

/**
 * Creates Insert SQL query for provided occasion or occasions
 * var $occasion - array of elements or single object for occasion
 */
function addOccasionQueryTxt( $occasion )
{
	/**
	 * Function that will be executed to process occasion object
	 * var $o - occasion object
	 */
	$func = function( $o )
	{
		$occasionID = trim( $o->id );
		$description = trim( $o->description );

		return "( $occasionID, \"$description\" )";
	};
	
	// Create query text for inserting privided occasion entity objects
	return addQueryTxt( $occasion, "Occasion", $func );
}

/**
 * Creates Insert SQL query for provided parking space or spaces
 * var $parkingSpace - array of elements or single object for parking space
 */
function addParkingSpaceQueryTxt( $parkingSpace )
{
	/**
	 * Function that will be executed to process parking space object
	 * var $p - parking space object
	 */
	$func = function( $p )
	{
		$spaceID = trim( $p->id );
		$lotNumber = trim( $p->lotnumber );
		$vehicleSize = trim( $p->vehiclesize );
		
		return "( $spaceID, $lotNumber, \"$vehicleSize\" )";
	};
	
	// Create query text for inserting privided parking space entity objects
	return addQueryTxt( $parkingSpace, "ParkingSpace", $func );
}

/**
 * Creates Insert SQL query for provided restaurant tables
 * var $table - array of elements or single object for restaurant tables
 * var $location - array of elements or single object for locations
 */
function addRestaurantTableQueryTxt( $table, $location )
{
	/**
	 * Function that will be executed to process restaurant table object
	 * var $t - parking space object
	 * var $l - location object
	 */
	$func = function( $t, $l )
	{
		$tableID = trim( $t->id );
		$row = trim( $t->row );
		$spot = trim( $t->spot );
		$locationID = trim( $l->id );
		
		return "( $tableID, $row, $spot, $locationID )";
	};
	
	// Create query text for inserting privided restaurant table entity objects
	return addQueryTxt( $table, "RestaurantTable", $func, $location );
}

/**
 * Creates Insert SQL query for provided customers
 * var $customer - array of elements or single object for customers
 */
function addCustomerQueryTxt( $customer )
{
	/**
	 * Function that will be executed to process customer object
	 * var $c - customer object
	 */
	$func = function( $c )
	{
		$customerID = trim( $c->id );
		$name = trim( $c->name );
		$phone = trim( $c->phone );
		$email = trim( $c->email );
		
		return "( $customerID, \"$name\", \"$phone\", \"$email\" )";
	};
	
	// Create query text for inserting privided customer entity objects
	return addQueryTxt( $customer, "Customer", $func );
}

/**
 * Creates Insert SQL query for provided reservations
 * var $reservation - array of elements or single object for reservations
 */
function addReservationQueryTxt( $reservation )
{
	/**
	 * Function that will be executed to process reservation object
	 * var $r - reservation object
	 */
	$func = function( $r )
	{
		$customerID = trim( $r->customerid );
		$rdate = trim( $r->rdate );
		$rtime = trim( $r->rtime );
		$period = trim( $r->period );
		$occasionID = trim( $r->occasionid );
		$instructions = trim( $r->instructions );
		$token = trim( $r->token );
		$accessCode = trim( $r->accessCode );
		
		return "( $customerID, \"$rdate\", \"$rtime\", $period, $occasionID, \"$instructions\", \"$token\", \"$accessCode\" )";
	};
	
	// Create query text for inserting privided customer reservation objects
	return addQueryTxt( $reservation, "Reservation", $func );
}

/**
 * Creates Insert SQL query for provided reserved tables
 * var $table - array of elements or single object for reserved tables
 */
function addReservedTableQueryTxt( $table )
{
	/**
	 * Function that will be executed to reserved table object
	 * var $t - reserved table object
	 */
	$func = function( $t )
	{
		$tableID = trim( $t->id );
		$customerID = trim( $t->customerid );
		$rdate = trim( $t->rdate);
		
		return "( $tableID, $customerID, \"$rdate\" )";
	};
	
	// Create query text for inserting privided reserved table objects
	return addQueryTxt( $table, "ReservedTable", $func );
}

/**
 * Creates Insert SQL query for provided reserved parking spaces
 * var $space - array of elements or single object for parking spaces
 */
function addReservedParkingSpaceQueryTxt( $space )
{
	/**
	 * Function that will be executed to reserved parking space object
	 * var $s - reserved parking space object
	 */
	$func = function( $s )
	{
		$spaceID = trim( $s->id );
		$customerID = trim( $s->customerid );
		$rdate = trim( $s->rdate );
		
		return "( $spaceID, $customerID, \"$rdate\" )";
	};
	
	// Create query text for inserting privided reserved parking space objects
	return addQueryTxt( $space, "ReservedParkingSpace", $func );
}

/**
 * Creates Insert SQL query for cancelled reservations
 * var $reservation - Array of elements or single object for table reservation
 * var $comment - Comment explaining the reason of cancellation
 */
function addCancellationQueryTxt( $reservation, $comment )
{
	/**
	 * Function that will be executed to process cancelled reservation object
	 * var $r - table reservation object
	 * var $cmnt - Comment for explanation
	 */
	$func = function( $r, $cmnt )
	{
		$customerID = trim( $r->customerid);
		$rdate = trim( $r->rdate );
		$comment = trim( $cmnt );
		
		return "( $customerID, \"$rdate\", \"$comment\" )";
	};
	
	// Create query text for inserting privided cancelled reservation objects
	return addQueryTxt( $reservation, "Cancellation", $func, $comment );
}

/**
 * Creates text of clause for SQL query for selecting unavailable tables or parking spaces
 * var $hour - Hour
 * var $min - Minute
 * var $ampm - AM/PM
 */
function reservationTimeClause( $hour, $min, $ampm )
{
	// Min and Max for period of reservation
	$minPeriod = 2;
	$maxPeriod = 5;
	
	// Get time slots for min period of reservation
	$timeSlots = getTimeSlots( $hour, $min, $ampm, $minPeriod );
	// Create clause text for selecting time slots for min period of reservation
	$clause = "( period = $minPeriod AND r.reservationTime IN ($timeSlots) )";
	
	// Loop for each hour after the min period of reservation
	for ( $i = $minPeriod+1; $i <= $maxPeriod; $i++ )
	{
		// Get time slots for specified period of reservation
		$timeSlots = getTimeSlots( $hour, $min, $ampm, $i );
		// Add clause text for selecting time slots for specified period of reservation
		$clause .= " OR ( period = $i AND r.reservationTime IN ($timeSlots) )";
	}
	
	// Return created for SQL querues text of clause statement
	return $clause;
}
	
/**
 * Gets time slots for specified time and period of reservation
 * var $hour - Hour
 * var $min - Minute
 * var $ampm - AM/PM
 * var $period - Period of reservation
 */
function getTimeSlots( $hour, $min, $ampm, $period )
{
	// Increase specified initial minutes by 15 min
	$initMin = $min + 15;
	
	// If it became full hour
	if ( $initMin == 60 )
	{
		// Assign hour as next hour
		$hour = $hour + 1;
		// Assign 0 to initial minutes
		$initMin = 0;
	}
	
	// Loop for each hour within time period
	for ( $h = $hour-$period; $h < $hour; $h++ )
	{
		// Loop for each time slot 15 min
		for ( $m = $initMin; $m < 60; $m += 15 )
		{
			// If minutes are zero - assign 00 to display with leading zero
			$mm = $m ? $m : '00';
			
			// If provided hour is before 12
			if ( $h < 12 )
				// Keep provided AM/PM
				$apm = $ampm;
				
			// Otherwise, if hour is after noon
			else
				// Flip AM/PM
				$apm = $ampm == 'AM' ? 'PM' : 'AM';
			
			// Add to the array of time slots created with loops time slot
			$times[] =  "'$h:$mm $apm'";
		}
		
		// After 1st hour start min with zero
		$initMin = 0;
	}
	
	// If initial minutes are zero - assign min with leading zeroes
	$mm = $min ? $min : '00';	
	// Add to the array of time slots initial provided time
	$times[] =  "'$hour:$mm $ampm'";	

	// Join array elements with comma and return as a string
	return join( ', ', $times );
}

/**
 * Cancels reservation through specified token
 */
function cancelReservation( $token, $cmt='' )
{
	// Query text to find reservation by specified token
	$query = "SELECT *
			  FROM Reservation
			  WHERE token = '$token' ;";
			  
	$res = getQueryResultsArray( $query );
	
	if ( count( $res ) > 0 )
	{
		// Create object for reservation event
		$r = new Reservation( $res[0]->CustomerID, $res[0]->ReservationDate, $res[0]->ReservationTime, $res[0]->Period, $res[0]->OccasionID, $res[0]->Instructions, $res[0]->Token, $res[0]->AccessCode );
		
		$reservationDate = $res[0]->ReservationDate;
		$reservationTime = $res[0]->ReservationTime;
		
		if ( !$cmt )
			$cmt = "Cancelled through email link";
		
		$query = addCancellationQueryTxt( $r, $cmt );
		doQuery( $query );
		
		print "<br>Reservation for $reservationDate  $reservationTime";
		print "<br>has been successfully cancelled";
	}	
}
?>