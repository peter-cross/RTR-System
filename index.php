<!DOCTYPE html>

<?php
	require( 'check_url.php' );
?>
<html>

<head>
<?php
	// Add file with head tags
	require( 'head.php' );
	// Add file with css settings
	require( 'style.php' );
?>
</head>

<body id='wrapper' class='col-md-12'>
	<div id='insert-div'></div>
	<br>
	<input id='next' name='next' type='submit' value='Next >>'/>
</body>  

<?php
	// Add file with JavaScript 
	require( 'script.php' );
?>    
</html>
