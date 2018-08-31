<?php
/**
 * Fourth page of Restaurant Table Reservation Wizard
 * @author Peter Cross
 * @version Nov 19, 2017
 */

// Get info from previous steps passed through AJAX
$info = $_REQUEST['info'];
// Get customer name passed through AJAX
$name = $_REQUEST['name'];
// Get customer email passed through AJAX
$email = $_REQUEST['email'];
// Get customer phone passed through AJAX
$phone = $_REQUEST['phone'];
// Get period passed through AJAX
$period = $_REQUEST['period'];
// Get parking space checkbox value passed through AJAX
$parking_space = $_REQUEST['parking_space'];
// Get number of vehicles passed through AJAX
$vehicles = $_REQUEST['vehicles'];
// Get vehicle size passed through AJAX
$size = $_REQUEST['size'];
// Get occasion passed through AJAX
$occasion = $_REQUEST['occasion'];
// Get additional instructions passed through AJAX
$instructions = $_REQUEST['instructions'];
// Get reserved table IDs passed through AJAX
$reserved = $_REQUEST['reserved'];

// Split info passed from previous steps into array
$args = explode( "|", $info );
// Extract month, day, hour, min and am/pm from the array
$month = trim( $args[0] );
$day = trim( $args[1] );
$hour = trim( $args[2] );
$min = trim( $args[3] );
$ampm = trim( $args[4] );

// Combine month and day and store it as date
$date = "$month $day";
// Combine hour, min and am/pm and store it as time
$time = "$hour:$min $ampm";
// Remove from from string with reserved tables text 'table' to have only table IDs
$reserved = trim( str_replace( "table", "", $reserved ) );

// Combine obtained through the form and extracted from previous steps info and add it to string with all info for table reservation
$info .= "| $name| $email| $phone| $period| $parking_space| $vehicles| $size| $occasion| $instructions| $reserved";

// JavaScript code to execute when the user hits Next button
$nextCode = "$.ajax({ data: { $STEP: $nextStep,
							  info: '$info' } }); 
			 $(NEXT_BTN).prop( 'type', 'hidden' );";

// Create form HTML and display it
print formHTML();
	
/**
 * Creates form HTML
 */
function formHTML()
{
	// Get access to all required for the form global variables
	global $reserved, $date, $time, $name, $email, $phone, $period, $parking_space, $vehicles, $size, $occasion, $instructions;
	
	// If parking space checkbox was selected - mark it as checked
	$checked = ( $parking_space == 'true' ? ' checked' : '' );
	
	return "
		<fieldset id='reservation_order_fieldset'>
			<legend>Table Reservation Order</legend>
			
			<div class='required_info_item_div'>
				<label for='selected_table'>Selected Table(s)</label>
				<textarea id='selected_table' name='selected_table' class='required_info_textarea readonly' readonly>$reserved</textarea>
			</div>
			
			<div class='required_info_item_div'>
				<label for='selected_date'>Selected Date and Time</label><br>
				<input id='selected_date' name='selected_date' type='text' value='$date, $time' class='required_info_input_item readonly' readonly/>
			</div>
			
			<div class='required_info_item_div'>
				<label for='name'>Name*</label><br>
				<input id='name' name='name' type='text' value='$name' class='required_info_input_item readonly' readonly/>
			</div>
			
			<div class='required_info_item_div'>
				<label for='email'>Email*</label><br>
				<input id='email' name='email' type='email' value='$email' class='required_info_input_item readonly' readonly/>
			</div>
			
			<div class='required_info_item_div'>
				<label for='phone'>Phone*</label><br>
				<input id='phone' name='phone' type='text' value='$phone' class='required_info_input_item readonly' readonly/>
			</div>
			
			<div class='required_info_item_div'>
				<span>Time period &nbsp;</span>
				<input id='period' name='period' type='number' value='$period' min='2' max='5' class='required_info_number readonly' readonly/>
				<span>hrs</span>
			</div>
			
			<div class='required_info_item_div'>
				<input id='parking_space' name='parking_space' type='checkbox' value='' class='required_info_checkbox readonly' $checked disabled/>
				<label for='parking_space' style='display: inline-block;'>Parking space required</label>
				
				<span>&nbsp; for &nbsp;</span>
				<input id='vehicles' name='vehicles' type='number' value='$vehicles' min='1' max='8' class='required_info_number readonly' readonly/>
				<span>vehicle(s) size</span>
				<input id='vehicle_size' name='vehicle_size' type='text' value='$size' class='required_info_number readonly' readonly/>
			</div>
			
			<div class='required_info_item_div'>
				<label for='occasion'>Occasion</label><br>
				<input id='occasion' name='occasion' type='text' value='$occasion' class='required_info_input_item readonly' readonly/>
			</div>
			
			<div class='required_info_item_div'>
				<label for='instructions'>Additional Instructions</label><br>
				<textarea id='instructions' name='instructions' class='required_info_textarea readonly' readonly>$instructions</textarea>
			</div>
		</fieldset>";
}
?>