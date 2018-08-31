<?php
/**
 * File with different constants and setttings
 * @author Peter Cross
 * @version Nov 21, 2017
 */

// AJAX data var name for Step #
$STEP = 'step';

// HTML input tag id name for Next Code
$NEXT_CODE = 'next_code';

// Restaurant settings file
$XML_FILE = "restaurant_settings.xml";	

// Path to AJAX pages
$PAGES_PATH = "pages/";					

// Extensions of pages that can be displayed
$EXTENSIONS = [ 'php', 'php5' , 'phtml', 'html', 'htm' ];

// Months of the year for displaying
$MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

// File for establishing connection with database
$DB_CONNECTION_FILE = 'db_connection.php'; 

// File with information about database settings
$DB_INFO_FILE = 'db_info.php';

// File for performing SQL interaction with database
$SQL_INTERACTION_FILE = 'sql_interaction.php';

// File with databse entity classes
$ENTITY_CLASSES_FILE = 'entity_classes.php';
?>