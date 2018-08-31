<?php
/**
 * Second page of Restaurant Table Reservation Wizard
 * @author Peter Cross
 * @version Nov 19, 2017
 */
?>
<h3>Restaurant Tables Map</h3>

<?php
// Get month passed through AJAX
$month = $_REQUEST['month'];
// Get day passed through AJAX
$day = $_REQUEST['day'];
// Get hour passed through AJAX
$hour = $_REQUEST['hour'];
// Get min passed through AJAX
$min = $_REQUEST['min'];
// Get am/pm passed through AJAX
$ampm = $_REQUEST['ampm'];

// Combine passed info into one string separated by symbol |
$info = "$month| $day| $hour| $min| $ampm";

// Load restaurant settings from XML file, if they are not loaded yet
loadRestaurantSettings();

// Get current year number
$year = date('Y'); 
// Find number of selected month in the array of months
$mnth = array_search( $month, $MONTHS )+1;

// If selected month is before current month - assume that it's next year
if ( $mnth - date( "n" ) < 0 )
	// Increase year number by 1 for next year
	$year += 1;

// Get passed day as a number
$dd = (int) $day;

// If day number is before 10
if ( $dd < 10 )
	// Append leading zero to the day number
	$dd = '0' . $dd;

// Create string for the date combined of year, month and day
$date = "$year-$mnth-$dd";	
	
// Create time clause text for SQL query condition
$resTimeClause = reservationTimeClause( $hour, $min, $ampm );

// Text of SQL query for selecting table IDs that are unavailable for reservation	
$query = "SELECT tableID
		  FROM ReservedTable rt, Reservation r
		  WHERE rt.reservationDate =  r.reservationDate 
          AND  r.reservationDate = '$date' 
          AND  ($resTimeClause);";	
		   
// Execute SQL query and get results as array of objects
$tbles = getQueryResultsArray( $query );

// Text of SQL query for selecting tables for which reservation is cancelled
$query = "SELECT tableID
		  FROM Cancellation c, ReservedTable rt, Reservation r
		  WHERE c.reservationDate = rt.reservationDate AND rt.reservationDate =  r.reservationDate
          AND  rt.reservationDate = '$date' 
          AND  ($resTimeClause);";	

// Execute SQL query and get array of objects for tables with cancelled reservation 
// (2nd query because MINUS operator is not available in MySQL)
$cancl = getQueryResultsArray( $query );
		   
// Variable to store unavailable table IDs
$tableIDs = "";

// Loop for each array element for unavailable tables
foreach( $tbles as $tblObj  )
{
	// Get table ID as object attribute of array element
	$tID = $tblObj->tableID;
	// Append table ID to the string for storing available table IDs
	$tableIDs .= "$tID "; 
}

// If there are tables for which reservation is cancelled
if ( count( $cancl ) > 0 )
	// Loop for array with objects for cancelled tables
	foreach ( $cancl as $cnlObj )
	{
		$tID = $cnlObj->tableID;
		// Remove tables for which reservation is cancelled
		$tableIDs = str_replace( "$tID ", "", $tableIDs );
	}

// If there is object created based on XML file for reastaurant settings
if ( $xmlObj )
{
	// JavaScript code to execute when the user hits Next button
	$nextCode = "if ( !( rt = $(NEXT_CODE).prop('alt') ) ) { alert ( 'Select a table!' ); exit(); }
				 $.ajax({ data: { $STEP: $nextStep,
								  info: '$info', 
								  reserved: rt } });";
		
	// Display table selection form HTML
	print formHTML( $xmlObj->location ); //. '<br>' . json_encode( $tbles );
}
// Otherwise
else
	// Display that info with restaurant settings is not found
	print "Couldn't find Restaurant Settings XML file ";		
	

	
/**
 * Loads restaurant settings from object created based on XML file
 */
function loadRestaurantSettings()
{
	global $xmlObj;
	
	if ( $xmlObj == null )
		return;
	
	// Text of query to find the number of locations
	$query = "SELECT COUNT(*) AS numCount  FROM Location;";
	$l = getQueryResultsArray( $query );
	if ( count( $l ) == 0 || $l[0]->numCount == 0 )
	{
		$query = addLocationQueryTxt( $xmlObj->location );
		doQuery( $query );
	}
	
	// Text of query to find the number of occasions
	$query = "SELECT COUNT(*) AS numCount FROM Occasion;";
	$o = getQueryResultsArray( $query );
	if ( count( $o ) == 0 || $o[0]->numCount == 0 )
	{
		$query = addOccasionQueryTxt( $xmlObj->occasion );
		doQuery( $query );
	}
	
	// Text of query to find the number of parking spaces
	$query = "SELECT COUNT(*) AS numCount FROM ParkingSpace;";
	$p = getQueryResultsArray( $query );
	if ( count( $p ) == 0  || $p[0]->numCount == 0 )
	{
		$query = addParkingSpaceQueryTxt( $xmlObj->parkingspace );
		doQuery( $query );
	}
	
	// Text of query to find the number of restaurant tables
	$query = "SELECT COUNT(*) AS numCount FROM RestaurantTable;";
	$r = getQueryResultsArray( $query );
	if ( count( $r ) == 0 || $r[0]->numCount == 0 )
		foreach ( $xmlObj->location as $location )
		{
			$query = addRestaurantTableQueryTxt( $location->table, $location );
			doQuery( $query );
		}	
}
	
/**
 * Creates form HTML for all locations of the restaurant
 */
function formHTML( $locations )
{
	// Get HTML for locations and JavaScript for manipulating displaying HTML for different locations 
	// and return it
	return getLocationsHTML( $locations ) . locationSelectScript( $locations );
}
	
/**
 * Creates HTML for displaying restaurant locations 
 */
function getLocationsHTML( $location )
{
	// HTML for selecting restaurant locations
	$html = "<div><select id='location' onChange='onLocationSelect()'>";
	
	// To mark 1st location as selected by default
	$selected = " selected";	
	
	// Loop for each location
	foreach( $location as $lcn )
	{
		// Get name as array element attribute
		$name = $lcn->name;
		
		// Add HTML for slecting location
		$html .= "<option value='$name' $selected>$name</option>";
		// Mark next location as not selected
		$selected = "";
	}
	
	// Add closing tags to HTML
	$html .= "</select></div>";
	
	return "<div class='location_div'>
				<label for='location'>Location</label><br>
				$html
			</div>
			<div id='location-info'></div>";
}
	
/**
 * Creates JavaScript for manipulating selecting locations on the form
 * var $locations - array of locations
 */ 
function locationSelectScript( $locations )
{
	// To store JavaScript for manipulating selecting locations
	$script = "<script> var locationHTML = [ ";
	
	// Get HTML for selecting each location in the form of array for each location
	$locHTML = getAllLocationsHTML( $locations );
	// Get number of locations
	$numLocations = count( $locHTML );
		
	// Loop for each location
	for ( $idx = 0; $idx < $numLocations; $idx++ )
	{
		// Get location HTML
		$loc = $locHTML[$idx];
		// Add location HTML to the script to store it as array element
		$script .= "\"$loc\"";
		
		// If it's not the last location
		if ( $idx < $numLocations-1 )
			// Add comma separator to the script
			$script .= ", ";
	}
		
	// Add to the script the code for function that will be invoked on location selection 
	// and on checking checkbox for table
	$script .= " ];
	
		function onLocationSelect()
		{
			var insDiv = \$( '#location-info' );
			var idx = \$( '#location option:selected' ).index();
			
			if ( idx != null && idx >= 0 )
				insDiv.html( locationHTML[idx] );
			else
				insDiv.html( locationHTML[0] );
		}
		
		function onTableSelect( chkBox, tblID )
		{
			rt = $(NEXT_CODE).prop( 'alt' );
			
			if ( chkBox.is( ':checked' ) )
				$(NEXT_CODE).prop( 'alt', rt + ' ' + tblID );
			else
				$(NEXT_CODE).prop( 'alt', rt.replace( ' ' + tblID, '' ) );
		}
		
		onLocationSelect();
	</script>";
	
	return $script;
}

/**
 * Creates HTML for selecting each location in the form of array for each location
 * var $locations - array of location objects
 */
function getAllLocationsHTML( $locations )
{
	// Loop for each location
	for ( $idx = 0; $idx < count( $locations ); $idx++ )
	{
		// Get array of tables for current location
		$tables = $locations[$idx]->table;
		// Create HTML for location info and store it in the array
		$locHTML[$idx] = "<br>". htmlLocationInfo( $locations[$idx] );
			
		// If in the array of tables number of tables more than one
		if ( count( $tables ) > 1 )
		{
			// Get row number for very first table
			$row = $tables[0]->row;
			
			// Loop for each table in the array of tables
			foreach( $tables as $table )
			{
				// If current table is on the next row
				if ( ($table->row - $row) > 0 )
				{
					// Add HTML tag to go to next line
					$locHTML[$idx] .= "<br>";
					// Store currrent table row number
					$row = $table->row;
				}
				
				// Add to location HTML info about current table in the form of HTML
				$locHTML[$idx] .= htmlTableInfo( $table );
			}
		}
		
		// Otherwise
		else
			// Add to location HTML info about the only table in the form of HTML
			$locHTML[$idx] .= htmlTableInfo( $tables );
	}
	
	return $locHTML ;
}

/**
 * Creates HTML for location info
 * var $l - location object
 */
function htmlLocationInfo( $l )
{
	$html = '<b>' . $l->name . '</b>';
	$html .= '<br>(' . $l->description . ')';
	$html .= '<br>';
	
	return $html;
}

/**
 * Creates HTML for table info
 * var $t - table object
 */
function htmlTableInfo( $t )
{
	// Get access to variable with available table IDs
	global $tableIDs;
	
	// HTML for displaying table info
	$html =  "<div class='table_info'>";
	
	// Get table ID #
	$id = trim( $t->id );
	// Get type of table
	$type = trim( $t->type );
	// Get table capacity
	$capacity = $t->capacity;
	
	// String for displaying table capacity in HTML
	$tbl = "$capacity&nbsp;ppl";
	
	// Variable to mark table as unavailable
	$dsbld = "";
	// Find position of current table ID in the string with unavailable table IDs
	$pos = strpos( $tableIDs, $id );
	
	// If current table ID is not found
	if ( $pos !== false )
	{
		// Mark table as unavailable for selection
		$dsbld = " disabled";
		// String for displaying that table is reserved
		$tbl = "RESERVED";
	}
	
	return "$html <div class='table $type'><span style='display: inline-block; vertical-align: middle;'># $id<br/>$tbl</span></div><br/> \
			<input id='table$id' class='tablecheckbox' type='checkbox' onclick='onTableSelect($(this), $id)' $dsbld/></div>";
}
?>