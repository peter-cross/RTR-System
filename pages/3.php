<?php
/**
 * Third page of Restaurant Table Reservation Wizard
 * @author Peter Cross
 * @version Nov 19, 2017
 */

// Get info from previous steps passed through AJAX
$info = $_REQUEST['info'];
// Get reserved table IDs passed through AJAX
$reserved = trim( $_REQUEST['reserved'] );

// JavaScript code to execute when the user hits Next button
$nextCode = "req = '';
			 if ( !( name = $('#name').val() ) ) req += 'Name ';
			 if ( !( email = $('#email').val() ) ) req += 'E-mail ';
			 if ( !( phone = $('#phone').val() ) ) req += 'Phone Number ';
			 if ( req ) { alert( 'Not entered: ' + req ); exit(); }
			 $.ajax({ data: { $STEP: $nextStep,
							  info: '$info',
							  name: name,
							  email: email,
							  phone: phone,
							  period: $('#period').val(),
							  parking_space: $('#parking_space').is(':checked'),
							  vehicles: $('#vehicles').val(),
							  size: $('#vehicle_size option:selected').val(),
							  occasion: $('#occasion option:selected').val(),
							  instructions: $('#instructions').val(),
							  reserved: '$reserved' } });
			 $(NEXT_BTN).prop( 'value', 'Submit' );";	

// Display HTML for entering required for reservation info			 
print requiredInfoHTML();


/**
 * Creates HTML for entering required for reservation info
 */
function requiredInfoHTML()
{
	// Get HTML for selecting occasion
	$occasion = getOccasionHTML();
	// Get HTML for selecting vehicle size
	$size = getVehicleSizeHTML();
	
	return "
		<fieldset  class='required_info_fieldset'>
			<legend>Information required to complete table(s) reservation</legend>
			
			<div class='required_info_item_div'>
				<label for='name'>Name*</label><br>
				<input id='name' name='name' type='text' value='' class='required_info_input_item' required/>
			</div>
			
			<div class='required_info_item_div'>
				<label for='email'>Email*</label><br>
				<input id='email' name='email' type='email' value='' class='required_info_input_item' required/>
			</div>
			
			<div class='required_info_item_div'>
				<label for='phone'>Phone*</label><br>
				<input id='phone' name='phone' type='text' value='' class='required_info_input_item' required/>
			</div>
			
			<div class='required_info_item_div'>
				<span>Time period &nbsp;</span>
				<input id='period' name='period' type='number' value='2' min='2' max='5' class='required_info_number' required/>
				<span>hrs</span>
			</div>
			
			<div class='required_info_item_div'>
				<input id='parking_space' name='parking_space' type='checkbox' value='' class='required_info_checkbox'/>
				<label for='parking_space' style='display: inline-block;'>Parking space required</label>
				
				<span>&nbsp; for &nbsp;</span>
				<input id='vehicles' name='vehicles' type='number' value='1' min='1' max='8' class='required_info_number'/>
				<span>vehicle(s) size</span>
				$size
			</div>
			
			<div class='required_info_item_div'>
				<label for='occasion'>Occasion</label><br>
				$occasion
			</div>
			
			<div class='required_info_item_div'>
				<label for='instructions'>Additional Instructions</label><br>
				<textarea id='instructions' name='instructions' value='' class='required_info_textarea'/></textarea>
			</div>
		</fieldset>";
}
	
/**
 * Creates HTML for selecting occasion
 */
function getOccasionHTML()
{
	// Get access to global variable for XML file object
	global $xmlObj;
	
	// Get array of occasions as XML file object attribute
	$occasions = $xmlObj->occasion;
	
	// If there is more that one occasion in the array
	if ( count( $occasions ) > 1 )
		// Loop for each occasions array element
		foreach( $occasions as $occn )
			// Add occasion description to the array of occasion descriptions
			$occasion[] = $occn->description;
	
	// Otherwise
	else
		// Add occasion description to the array of occasion descriptions
		$occasion[] = $occasions->description;
	
	// HTML for selecting occasion
	$html = "<div><select id='occasion'>";
	// Add 1st display option to explain what to select
	$html .= "<option value=''>Select occasion...</option>";
	
	// Loop for each occasion
	foreach( $occasion as $occn )
		// Add HTML for current occasion
		$html .= "<option value='$occn'>$occn</option>";
	
	return "$html</select></div>";
}

/**
 * Creates HTML for selecting vehicle size
 */
function getVehicleSizeHTML()
{
	// Array for storing vehicle sizes
	$size = [ 'S', 'M', 'L', 'XL' ];
	
	// HTML for selecting vehicle size
	$html = "<div style='display: inline-block;'><select id='vehicle_size'>";
	// Add 1st display option as imitation of select button
	$html .= "<option value=''>...</option>";
	
	// Loop for each vehicle size
	foreach( $size as $sz )
		// Add HTML for current vehicle size
		$html .= "<option value='$sz'>$sz</option>";
	
	return "$html</select></div>";
}
?>