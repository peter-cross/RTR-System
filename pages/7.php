<?php
/**
 * Reservation Cancellation Page 
 * @author Peter Cross
 * @version Nov 21, 2017
 */

// Login email passed through AJAX
$email = trim( $_REQUEST['email'] );
// Password or Access Code passed through AJAX
$accessCode = trim( $_REQUEST['password'] );

// Variable for output message with default message assigned
$output = "User not found";

// HTML for comment input text area
$comment = "<div class='required_info_item_div'>
				<label for='comment'>Comment (explanation)*</label><br>
				<textarea id='comment' name='comment' value='' class='required_info_textarea' style=''/></textarea>
			</div>";

// SQL query text for selecting customer and reservation with specified customer email and access code
// By default - assume it's customer logs in
$query = "SELECT Reservation.CustomerID, Name, PhoneNumber, Email, ReservationDate, ReservationTime, Period, Token 
		  FROM Reservation, Customer
		  WHERE Reservation.CustomerID = Customer.CustomerID
		  AND Customer.Email = '$email'
		  AND Reservation.AccessCode = '$accessCode';";

// Execute query and get results in the form of object array
$res = getQueryResultsArray( $query );

// If there are results of query execution, i.e. we found customer with specified email who has reservation with specified access code
if ( count( $res ) > 0)
	getCustomerOutput();

// Otherwise, if we did not find customer with specified email and access code - assume it's restaurant employee
else
	getEmployeeOutput();

// Display reservation cancellation information			  
print   "<div>
			<h3>Reservation cancellation</h3>
			<br><br>
			$output
		</div>";
	

/**
 * Gets output HTML for customer user
 */
function getCustomerOutput()
{
	// Get access to global variables
	global $token, $output, $nextCode;
	
	// Variable for found token for reservation
	$token = "";
	// Get customer reservation information and get output in the form of HTML
	$output = customerInfo();
	
	// JavaScript code to execute when the user hits Next button
	$nextCode = "cmnt = $('#comment').val().trim();
				 if ( !cmnt ) { alert ( 'You need to provide a comment!' ); exit(); }
				 $.ajax({ data: { step: 7,
								  token: '$token',
								  comment: cmnt } }); 
				 $(NEXT_BTN).prop( 'value', 'OK' );";
}	

/**
 * Gets output HTML for employee user
 */
function getEmployeeOutput()
{
	// Get access to global variables
	global $email, $accessCode, $output, $nextCode;
	
	// SQL query text to find employee with specified email and password
	$query = "SELECT *
			  FROM Employee
			  WHERE Email = '$email' AND Password = '$accessCode';";
			  
	// Execute query and get results in the form of object array
	$res = getQueryResultsArray( $query );
	
	// If there are results in array, i.e. we found such employee
	if ( count( $res ) > 0)
		// Get all reservation information starting with current date and get output in the form of HTML
		$output = allReservationsInfo();
	
	// JavaScript code to execute when the user hits Next button
	$nextCode = "if ( !( rsrvn = $(NEXT_CODE).prop('alt') ) ) { alert ( 'Select at least one reservation!' ); exit(); }
				 cmnt = $('#comment').val().trim();
				 if ( !cmnt ) { alert ( 'You need to provide a comment!' ); exit(); }
				 $.ajax({ data: { step: 7,
								  token: rsrvn,
								  comment: cmnt } }); 
				 $(NEXT_BTN).prop( 'value', 'OK' );";	
}

/**
 * Get customer reservation information and returns output in the form of HTML
 */ 
function customerInfo()
{
	// Get access to global variables
	global $res, $token, $comment;
	
	// Get all available information about customer and reservation
	$cstID = $res[0]->CustomerID;
	$resDate = $res[0]->ReservationDate;
	$token = $res[0]->Token;
	$name = $res[0]->Name;
	$phone = $res[0]->PhoneNumber;
	$email = $res[0]->Email;
	$resTime = $res[0]->ReservationTime;
	$period = $res[0]->Period;
	
	// SQL query text to get all table IDs for specified customer and reservation date
	$query = "SELECT TableID
			  FROM ReservedTable
			  WHERE CustomerID = $cstID  AND ReservationDate = '$resDate';";
			  
	// Execute query and get results in the form of array of objects
	$tbl = getQueryResultsArray( $query );
	
	// Variable to store table IDs in the string
	$tbls = "";
	
	// If we found at least one table ID for specified customer and reservation date
	if ( count( $tbl ) > 0 )
		// Loop for each table ID
		foreach ( $tbl  as $t )
		{
			// Get table ID
			$tID = $t->TableID;
			// Append table ID to the result string
			$tbls .= "$tID ";
		}
	
	// Return HTML with all available reservation information
	return "<b>Reservation details:</b>
			   <div style='text-align: left;'>
				   <br>Customer: $name
				   <br>Phone: $phone
				   <br>Email: $email
				   <br>Date: $resDate
				   <br>Time: $resTime
				   <br>Period: $period hours
				   <br>Table #: $tbls
			   </div>" . $comment;
}
	
/*
 * Get all reservation information starting with current date and return result in the form of HTML
 */
function allReservationsInfo()
{
	// Get accesss to global variable for comment input text field HTML
	global $comment;
	
	// Get current date in format YYYY-mm-dd
	$curDate = date( 'Y-m-d' );
		
	// SQL query text for selecting all reservations in the system starting with current date
	$query = "SELECT Reservation.CustomerID, Name, PhoneNumber, Email, ReservationDate, ReservationTime, Period, Token 
			  FROM Reservation, Customer
			  WHERE Reservation.CustomerID = Customer.CustomerID
			  AND ReservationDate >= '$curDate';";
	
	// Execute query and get results in form of array of objects
	$res = getQueryResultsArray( $query );
	// Get number of array elements
	$num = count( $res );
	
	// JavaScript for function that will be executed on checking checkbox for reservation
	$script = "
		<script>
			function onReservationSelect( chkBox, token )
			{
				tokens = $(NEXT_CODE).prop( 'alt' );
				
				if ( chkBox.is( ':checked' ) )
					$(NEXT_CODE).prop( 'alt', tokens + ' ' + token );
				else
					$(NEXT_CODE).prop( 'alt', tokens.replace( ' ' + token, '' ) );
			}
		</script>";
	
	// Variable for output HTML
	$output = "<table class='table-reservations'>
				<tr >
					<th>&nbsp;</th>
					<th>Name</th>
					<th>Phone Number</th>
					<th>Email</th>
					<th>Date</th>
					<th>Time</th>
					<th>Period, hrs</th>
				</tr>";
	
	// Loop for each reservation
	foreach ( $res as $r )
	{
		// Get all available information about reservation and customer
		$cstID = $r->CustomerID;
		$resDate = $r->ReservationDate;
		$token = $r->Token;
		$name = $r->Name;
		$phone = $r->PhoneNumber;
		$email = $r->Email;
		$resTime = $r->ReservationTime;
		$period = $r->Period;
		
		// HTML for checkbox for 1st column
		$chkBox = "<input id='$token' type='checkbox' onclick='onReservationSelect( $(this), \"$token\" )'>";
		
		// Add to output HTML info about current reservation
		$output .=  "<tr>
						<td>$chkBox</td>
						<td>$name</td>
						<td>$phone</td>
						<td>$email</td>
						<td>$resDate</td>
						<td>$resTime</td>
						<td>$period</td>
					</tr>";
	}
	
	// Add to output HTML for comment input text field and JavaScript and return as ouput string
	return $output . "</table>" . $comment. $script;
}
?>