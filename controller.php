<?php
/**
 * Page that is invoked through AJAX
 * @author Peter Cross
 * @version Nov 19, 2017
 */

// Include file with settings
include 'settings.php';
// Include file for establishing connection with database
include $DB_CONNECTION_FILE;

// Read step variable passed through AJAX
$step = $_REQUEST[$STEP];
		
// If step variable is top specified
if ( !isset( $step ) )
	// Exit with explaining message
    exit( "Step is not specified" );

// Variable for specifying next step
$nextStep = $step + 1;

// Default code that will be executed through JavaScript by AJAX callback function
$nextCode = "$.ajax({ data: { $STEP: $nextStep } });";

// Loop for each acceptable file extension
foreach ( $EXTENSIONS as $ext )
{
	// Get file name for the page to display
	$pageFile = "$PAGES_PATH$nextStep.$ext";
	
	// If such file exists
	if ( file_exists( $pageFile ) )
		// Exit from the loop
		break;
	// Otherwise
	else
		// Assign empty string in case if file with any extension does not exist
		$pageFile = "";
}

// If file for the page to display is specified
if ( $pageFile )
{		
	// Turn on output buffering
	ob_start();
	// Include into output buffer the file for page to display
	include $pageFile;
	// Get content of output buffer and clean it
	$page = ob_get_clean();
}
// Otherwise
else
{
	// Assign empty string for page to display
	$page = "";
	// Assign empty string for code to execute
	$nextCode = "";
}

// Get HTML page content to pass to callback function
$pageContent = "$page <input id='$NEXT_CODE' name='$NEXT_CODE' type='hidden' value=\"$nextCode\"/>"; 

// Encode page content and pass to callback function
print json_encode( $pageContent );
?>