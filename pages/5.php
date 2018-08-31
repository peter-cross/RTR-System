<?php
/**
 * Fifth page of Restaurant Table Reservation Wizard
 * @author Peter Cross
 * @version Nov 20, 2017
 */

// Add file for PHP mailer class
require_once( 'php/PHPMailerAutoload.php' );
// Add file with settings for sending emails
require_once( 'php/mail_info.php' );

// Get info from previous steps passed through AJAX
$info = $_REQUEST['info'];

// Split info passed from previous steps into array
$args = explode( "|", $info );

// Extract all info passed from previous steps from the array
$month = trim( $args[0] );
$day = trim( $args[1] );
$hour = trim( $args[2] );
$min = trim( $args[3] );
$ampm = trim( $args[4] );
$name = trim( $args[5] );
$email = trim( $args[6] );
$phone = trim( $args[7] );
$period = trim( $args[8] );
$parking_space = trim( $args[9] );
$vehicles = trim( $args[10] );
$size = trim( $args[11] );
$occasion = trim( $args[12] );
$instructions = trim( $args[13] );
$reserved = trim( $args[14] );

// Combine month and day and store it as date
$date = "$month $day";
// Combine hour, min and am/pm and store it as time
$time = "$hour:$min $ampm";

// Remove from from string with reserved tables text 'table' to have only table IDs
$reserved = trim( str_replace( "table", "", $reserved ) );

// Get token for current reservation
$token = randomString();
// Get access code for customer
$accessCode = accessCode();

// Invoke interaction with database
interactionWithDB();


/**
 * Launches transcations for interacting with database
 */
function interactionWithDB()
{
	// Get access to global variables
	global $name, $email, $phone, $spaceReservation;
	
	startTransaction();
	
	// Create customer object based on provided info
	$c = new Customer( $name, $email, $phone );
	// Create SQL query text for adding new customer
	$query = addCustomerQueryTxt( $c );
	// Execute SQL query for adding new customer
	doQuery( $query );

	// Do all reservations for the customer
	doReservation( $c );
	
	// Try to commit the transaction to database
	if (  commitTransaction() )
	{
		// If committed successfully - display from HTML and invoke sending email for the customer
		print formHTML();
		sendEmail();
	}
	// Otherwise
	else
	{
		// Display message that transaction could not commit to database
		print '<br>DB transaction did not commit properly';
		// Rollback changes
		rollbackTransaction();
	}
	
	// Close connection with database
	closeConnection();
}	

/**
 * Does all reservations for the customer
 * var $c - Customer object
 */
function doReservation( $c )
{
	// Get access to necessary global variables
	global $month, $day, $time, $hour, $min, $ampm, $period, $occasion, $instructions, $reserved, 
		   $parking_space, $vehicles, $size, $MONTHS, $token, $accessCode;
	
	// Get current year number
	$year = date('Y'); 
	// Get current month number
	$curMonth = date( "n" );
	// Get selected month number through array search
	$mnth = array_search( $month, $MONTHS ) + 1;

	// If selected month is before current month - assume that reservation is for next year
	if ( $mnth - $curMonth < 0 )
		// Increase year number by 1
		$year += 1;

	// Get day number
	$dd = (int) $day;

	// If day number is less than 10
	if ( $dd < 10 )
		// Append leading zero to display string
		$dd = '0' . $dd;

	// Combine month and day and store it as date
	$date = "$year-$mnth-$dd";	
	
	// Reserve tables for customer on specified date
	reserveTables( $c, $date );
	
	// If checkbox for parking space was selected
	if ( $parking_space == 'true' )
		reserveParkingSpace( $c, $date );		
}

/**
 * Reserves tables for customer on specified date
 * var $c - Customer object
 * var $date - Date in format 'YYYY-mm-dddd'
 */
function reserveTables( $c, $date )
{
	global $time, $period, $reserved, $occasion, $instructions, $token, $accessCode;
	
	// If there is more than one table ID separated with space symbol
	if ( strpos( $reserved, ' ' ) )
		// Extract reserved table IDs
		$reservedTblID = explode( ' ', $reserved );
	// Otherwise
	else
		// Save reserved table ID as array element
		$reservedTblID = [$reserved];
	
	// Create object for reservation event
	$r = new Reservation( $c->id, $date, $time, $period, $occasion, $instructions, $token, $accessCode );
	// Create SQL query text for adding reservation to database
	$query = addReservationQueryTxt( $r );
	// Execute SQL query for adding reservation to database
	doQuery( $query );

	// Loop for each reserved table ID
	for ( $i = 0; $i < count( $reservedTblID ); $i++ )
	{
		// Get table ID #
		$tID = $reservedTblID[$i];
		
		// If table ID # is non-zero
		if ( (int)$tID > 0 )
		{
			// Create object for reserved table
			$rt = new ReservedTable( $tID, $c->id, $date );
			// Create SQL query text for adding reserved table to database
			$query = addReservedTableQueryTxt( $rt );
			// Execute SQL query for adding reserved table to database
			doQuery( $query );
		}	
	}
}

/**
 * Reserves parking space for customer on specified date
 * var $c - Customer object
 * var $date - Date in format 'YYYY-mm-dddd'
 */
function reserveParkingSpace( $c, $date )
{
	global $hour, $min, $ampm, $vehicles;
	
	// Create text of time clause for query condition
	$resTimeClause = reservationTimeClause( $hour, $min, $ampm );

	// SQL query for selecting max # for reserved parking spaces for reservation time
	$query = "SELECT MAX(spaceID) AS maxID
			  FROM ReservedParkingSpace rs, Reservation r
			  WHERE rs.reservationDate =  r.reservationDate 
			  AND  r.reservationDate = '$date' 
			  AND  ($resTimeClause);";	

	// Execute SQL query and get results in the form of array of objects
	$spaceID = getQueryResultsArray( $query );
	
	// If there are reserved parking spaces for specified time slot
	if ( count( $spaceID ) > 0 )
		// Get available space ID as max reserved space ID increased by 1
		$spID = (int)$spaceID[0]->maxID + 1;
	
	// Otherwise - if no parking spaces are reserved for this time slot
	else
		// Assign very first space ID
		$spID = 1;
	
	// Loop for each vehicle to reserve a parking space
	for ( $i = $spID; $i < $spID + (int)$vehicles; $i++  )
	{
		// Create object for reserved parking space
		$rs = new ReservedParkingSpace( $i, $c->id, $date );
		// Create SQL query text for adding reserved parking space to database
		$query = addReservedParkingSpaceQueryTxt( $rs );	
		// Execute SQL query for adding reserved parking space to database
		doQuery( $query );
	}
}

/**
 * Creates form HTML to display
 */
function formHTML()
{
	// Get access to required global variables
	global $name, $reserved, $date, $time, $period;
	
	// Reservation details string
	$resDetails = "Table(s) $reserved reserved for $name on $date $time for $period hours";

	return "
		<fieldset  id='reservation_order_fieldset'>
			<legend>Table Reservation has been complete</legend>
			<br>
			<div>You will receive reservation confirmation by email ...</div>
			<br>
			<div>Reservation details:</div>
			<div>$resDetails</div>
		</fieldset>";
}

/**
 * Sends email to the customer
 */
function sendEmail()
{
	// Get access to required global variables
	global $systemEmail, $name, $email, $phone, $reserved, $month, $day, $time, $period, $occasion, $instructions, $cancelURL, $token, $accessCode;
	
	$htmlMsg = "<h3>Table Reservation Confirmation</h3>
				<br>
				<div>
					Table(s) $reserved reserved for $name on $month $day at $time for $period hours
					<br>
					Access Code: $accessCode
					<br>
					<p>In case you need to cancel the reservation click on the link:<br>
						<a href='$cancelURL/$token'>$cancelURL/$token</a>
					</p>
				</div>";
	
	phpEmailer( $systemEmail, "Restaurant Table Reservation System", $email, $name, "Table Reservation Confirmation", $htmlMsg, "" );
}

/**
 * Creates PHP Mailer object and sends email through SMTP provider
 * var $emailFrom - Email of sender
 * var $nameFrom - Name of sender
 * var $emailTo - Email of receiver
 * var $nameTo - Name of receiver
 * var $subject - Email subject
 * var $message - Email message text
 * var $attachment - Attachment to enclose
 */
function phpEmailer( $emailFrom, $nameFrom, $emailTo, $nameTo, $subject, $message, $attachment )
{
    // Get access to required global variables
	global $systemEmail, $mailHost, $mailPort, $mailUsername, $mailPassword;

    //Create a new PHPMailer instance
    $mail = new PHPMailer;
    //Tell PHPMailer to use SMTP
    $mail->isSMTP();
    //Enable SMTP debugging
    // 0 = off (for production use)
    // 1 = client messages
    // 2 = client and server messages
    $mail->SMTPDebug = 0;
    //Ask for HTML-friendly debug output
    $mail->Debugoutput = 'html';
    //Set the hostname of the mail server
    $mail->Host = $mailHost;
    //Set the SMTP port number - likely to be 25, 465 or 587
    $mail->Port = $mailPort;
    //Whether to use SMTP authentication
    $mail->SMTPAuth = true;
    //Username to use for SMTP authentication
    $mail->Username = $mailUsername;
    //Password to use for SMTP authentication
    $mail->Password = $mailPassword;
    //Set who the message is to be sent from
    $mail->setFrom( $systemEmail, "Table Reservation" );
    //Set an alternative reply-to address
    $mail->addReplyTo( $emailFrom, $nameFrom );
    //Set who the message is to be sent to
    $mail->addAddress( $emailTo, $nameTo );
    //Set the subject line
    $mail->Subject = $subject;

    $mail->Body = $message;
    //Read an HTML message body from an external file, convert referenced images to embedded,
    //convert HTML into a basic plain-text alternative body
    $mail->msgHTML( $message );
    //Replace the plain text body with one created manually
    $mail->AltBody = $message;

    //Attach a file
    $mail->addAttachment( $attachment );

    //send the message, check for errors
    if ( !$mail->send() )
	{
		print "<br>Mailer Error: " . $mail->ErrorInfo; 
		print "<br>Email message: <br>" . $message;
	}      
}

/** 
 * Generates a 36 symbol random string 
 */
function randomString()
{
    // Valid characters for a random string
    $char = "0123456789abcdefghijklmnopqrstuvwxyz";
    // Random string length
    $length = strlen($char);

    // Variable for a result string
    $str = "";
    
    // Loop for each character of random string
    for ( $i = 0; $i < $length; $i++ )
    {
        // Get a random number in a range from 0 to string length - 1
        $num = rand( 0, $length-1 ); 
        // Get the corresponding character and add it to the result string
        $str .= $char[$num];
    
    } // end Loop for each character

    // Return the result string
    return $str;   
}

/** 
 * Generates 3-symbol appointment code
 */
function accessCode ()
{
    global $name;
	
	// Valid numbers
    $num  = "0123456789";
    // Valid characters
    $char = "ABCDEFGHIJKLMNOPQRSTUVXYZ";

    // 1st symbol - random character
    $c1 = $char[rand(0, strlen($char)-1)];
    // 2nd symvol - random number from 0 to 9
    $c2 = $num[rand(0, strlen($num)-1)];
    // 3rd symbol - 1st letter of the client's name
    $c3 = strtoupper( substr( $name, 0, 1) );

    // Return concatenated 3 symbols for appointment code
    return $c1 . $c2 . $c3;   
}
?>