<?php
/**
 * Checks if path URL contains token
 * @author Peter Cross
 * @version Nov 20, 2017
 */

$token = tokenFromURL();

if ( $token )
{
	// Include file with settings
	include 'settings.php';
	// Include file for establishing connection with database
	include $DB_CONNECTION_FILE;
	
	cancelReservation( $token );
	exit();
}

/**
 * Gets token appended to URL and returns it 
 */
function tokenFromURL()
{
	$url = $_SERVER['REQUEST_URI'];
    $str = parse_url( $url , PHP_URL_PATH );
    
	if ( $str )
    {
        $pos = strrpos( $str, '/' );
            
        if ( $pos !== FALSE )
        {
            $token = trim( substr( $str, $pos+1 ) );
            
            if ( $token )
               	return $token;
            else
				return false;
		}
	}
		
	return false;
}
?>