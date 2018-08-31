<?php
/**
 * First page of Restaurant Table Reservation Wizard
 * @author Peter Cross
 * @version Nov 19, 2017
 */
?>
<h2>Restaurant Table Reservation</h2>
<br/>
<p class='date_time_title'><b>Select a day and time you are planning to visit us: </b></p>
<br/>
<br/>

<?php
// JavaScript code to execute when the user hits Next button
$nextCode = "$.ajax({ data: { $STEP: $nextStep,
							  month: $('#month option:selected').val(),
							  day: $('#day option:selected').val(),
							  hour: $('#hour option:selected').val(),
							  min: $('#min option:selected').val(),
							  ampm : $('#ampm option:selected').val() } });";

// Create HTML for selecting date and time of table reservation and display it
print dateAndTimeHTML();
		

/**
 * Creates HTML code for selecting date and time of table reservation
 */ 
function dateAndTimeHTML()
{
	// Get HTML for Date info
	$date = getDateHTML();
	// Get HTML for Time info
	$time = getTimeHTML();
	
	return "<div class='date_time'>$date</div>
			<div class='date_time'>$time</div>";
}							  

/**
 * Creates HTML for selecting date of table reservation
 */ 
function getDateHTML()
{
	// Get HTML for month info
	$month = getMonthHTML();
	// Get HTML for day info
	$day = getDayHTML();
	
	return "<br>
			<fieldset class='date_time_fieldset'>
				<legend>Date</legend>
			
					$month $day
			</fieldset>";
}

/**
 * Creates HTML for selecting time
 */
function getTimeHTML()
{
	// Get HTML for hour info
	$hour = getHourHTML();
	// Get HTML for min info
	$min = getMinHTML();
	// Get HTML for am/pm info
	$ampm = getAmPmHTML();
	
	return "<fieldset class='date_time_fieldset'>
				<legend>Time</legend>
			
				$hour $min $ampm
			</fieldset>";
}

/**
 * Creates HTML for selecting month
 */
function getMonthHTML()
{
	// Get access to global variable $MONTHS
	global $MONTHS;
	
	// Get current month number
	$curMonth = date( "n" );
	
	// HTML for selecting month
	$html = "<div class='date_item'> Month: <select id='month'>";
	
	// Loop for each month
	for ( $m = 0; $m < count( $MONTHS ); $m++ )
	{
		// Get string expression for month
		$mnth = $MONTHS[$m];
		// If it's current months - mark it to display as selected
		$selected = $m == $curMonth-1 ? ' selected' : '';
		
		// Add HTML for selecting the month
		$html .= "<option value='$mnth' $selected>$mnth</option>";
	}
	
	return "$html</select></div>";
}

/**
 * Creates HTML for selecting day
 */
function getDayHTML()
{
	// Array for displaying days
	$day = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31'];
	
	// Set default time zone to Vancouver
	date_default_timezone_set( 'America/Vancouver' );
	
	// Get current day as a number
	$curDay = date( "j" );
	
	// HTML for selecting a day of month
	$html = "<div class='date_item'> Day: <select id='day'>";
	
	for ( $d = 0; $d < count( $day ); $d++ )
	{
		// Get string representation of day
		$dd = $day[$d];
		// If it's current day - mark it to display as selected
		$selected = ($d+1) == $curDay ? " selected" : "";
		
		// Add HTML for selecting the day 
		$html .= "<option value='$dd' $selected>$dd</option>";
	}
	
	return "$html</select></div>";
}

/**
 * Creates HTML for selecting hour
 */
function getHourHTML()
{
	// HTML for selecting hour
	$html = "<div class='time_item'>Hour:&nbsp;<select id='hour'>";
	
	// Loop for each hour from 1 to 12
	for ( $h = 1; $h <= 12; $h++ )
		// Add HTML for selecting the hour
		$html .= "<option value='$h'>$h</option>";
	
	return "$html</select></div>";
}

/**
 * Creates HTML for selecting minute of the hour
 */
function getMinHTML()
{
	// HTML for selecting minute
	$html = "<div class='time_item'>Min:&nbsp;<select id='min'>";
	
	// Loop for each time slot with interval 15 min
	for ( $m = 0; $m < 60; $m += 15 )
	{
		// If hour just started - assign double zero string to display
		$mm = $m ? $m : '00';
				
		// Add HTML for selecting minute
		$html .= "<option value='$mm'>$mm</option>";
	}
	
	return "$html</select></div>";
}

/**
 * Creates HTML for selecting AM/PM time of the day
 */
function getAmPmHTML()
{
	return "<div class='time_item'>
				<select id='ampm'>
					<option value='PM'>PM</option>
					<option value='AM'>AM</option>
				</select>
			</div>";
}
?>