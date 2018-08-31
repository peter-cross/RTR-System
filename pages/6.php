<?php
/**
 * Login Page 
 * @author Peter Cross
 * @version Nov 21, 2017
 */

// JavaScript code to execute when the user hits Next button
$nextCode = "req = '';
			 if ( !( email = $('#email').val() ) ) req += 'E-mail ';
			 if ( !( password = $('#password').val() ) ) req += 'Password ';
			 if ( req ) { alert( 'Required to enter: ' + req ); exit(); }
			 $.ajax({ data: { step: 6,
							  email: $('#email').val(),
							  password: $('#password').val() } }); 
			 $(NEXT_BTN).prop( 'value', 'Cancel Reservation' );";

?>

<fieldset id='login_fieldset'>
	<legend>Login</legend>
	
	<div class='login_div'>
		<label for='email'>Email:</label><br>
		<input id='email' name='email' type='text' value='' class='login_input' required/>
	</div>
	<br>
	<div class='login_div'>
		<label for='password'>Password:</label><br>
		<input id='password' name='password' type='password' value='' class='login_input' required/>
	</div>
</fieldset>