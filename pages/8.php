<?php
/**
 * Cancellation Result Page 
 * @author Peter Cross
 * @version Nov 21, 2017
 */

// Get tokens passed through AJAX from previous step
$token = trim( $_REQUEST['token'] );
// Get comment passed through AJAX from previous step
$comment = trim( $_REQUEST['comment'] );

// If there is at least one token passed from previous step
if ( $token )
{
	// Split string into array
	$args = explode( " ", $token );
	
	// If there is no comment passed
	if ( !$comment )
		// Assign default cooment for cancellation
		$comment = "Cancelled through website";
	
	// Loop for each token
	foreach ( $args as $tkn )
		// Cancel reservation identified by token
		cancelReservation( $tkn , $comment );
}	

// Otherwise
else
	print "<br> Reservation not found in the system";

// JavaScript code to execute when the user hits Next button
$nextCode = "$.ajax({ data: { step: 8 } });
			 $(NEXT_BTN).prop( 'type', 'hidden' );";
?>