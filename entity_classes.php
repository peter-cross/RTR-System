<?php
/**
 * File with database entity classes
 * @author Peter Cross
 * @version Nov 19, 2017
 */

// Class for Customer database entity
class Customer
{
	// Customer ID
	public $id;
	// Customer name
	public $name;
	// Customer phone number
	public $phone;
	// Customer email
	public $email;
	
	/**
	 * Customer class constructor
	 * var $n - name
	 * var $p - phone number
	 * var $e - email
	 */
	public function __construct( $n, $e, $p )
	{
		$name = trim( $n );
		$email = trim( $e );
		$phone = trim( $p );
		
		// Query text to find customer with specified name and either email or phone number
		$query = "SELECT customerID FROM Customer WHERE  name='$name' AND ( email='$email' OR phoneNumber='$phone' );";
		// Execute query and get results as array of Customer IDs
		$custID = getQueryResultsArray( $query );

		// If there is more that one element in the results array
		if ( count( $custID ) > 0 )
			// Get first array element as Customer ID
			$cstID = (int)$custID[0]->customerID;
		
		// Otherwise
		else
		{
			// Query text to find maximum Customer ID in the database
			$query = "SELECT MAX(customerID) AS maxID FROM Customer;";
			// Execute query and get results as array of Customer IDs
			$custID = getQueryResultsArray( $query );
			
			// If there is more that one element in the results array
			if ( count( $custID ) > 0 )
				// Get first array element as maximum Customer ID and increase it by 1
				$cstID = (int)$custID[0]->maxID + 1;
			// Otherwise
			else
				// Assign 1 as next Customer ID
				$cstID = 1;
		}
		
		// Assign object attributes
		$this->id = $cstID;
		$this->name = $name;
		$this->email = $email;
		$this->phone = $phone;
	}
}
	
// Class for Reservation database entity
class Reservation
{
	// Customer ID
	public $customerid;
	// Reservation date
	public $rdate;
	// Reservation time
	public $rtime;
	// Reservation period
	public $period;
	// Occasion for table reservation
	public $occasionid;
	// Additional instructions for reservation
	public $instructions;
	// Token to find reservation tuple
	public $token;
	// Access Code
	public $accessCode;
	
	/**
	 * Reservation class constructor
	 * var $c - Customer ID
	 * var $d - Reservation date
	 * var $t - Reservation time
	 * var $p - Reservation period
	 * var $o - Occasion ID
	 * var $i - Additional instructions
	 * var $tkn - Token
	 * var $ac - Access Code
	 */
	public function __construct( $c, $d, $t, $p, $o, $i, $tkn='', $ac='' )
	{
		// Remove spaces from both sides for occasion var
		$occn = trim($o);
		
		// If occasion is not specified
		if ( $occn == '' )
			// Assign Other occasion
			$occn = 'Other';
		
		// Query text to find occasion ID for specified occasion description
		$query = "SELECT occasionID FROM Occasion WHERE description = '$occn';";
		// Execute query and get results in the form of array of objects
		$occnID = getQueryResultsArray( $query );
		
		// If there is more than one element in the results array
		if ( count( $occnID ) > 0 )
			// Get 1st array element as occasion ID
			$oID = (int)$occnID[0]->occasionID;
		// Otherwise
		else
			// Assign 1st occasion ID 
			$oID = 1;

		// Assign values to object attributes
		$this->customerid = $c;
		$this->rdate = $d;
		$this->rtime = $t;
		$this->period = $p;
		$this->occasionid = $oID;
		$this->instructions = $i;
		$this->token = $tkn;
		$this->accessCode = $ac;
	}	
}

// Class for ReservedTable database entity
class ReservedTable
{
	// Table ID
	public $id;
	// Customer ID
	public $customerid;
	// Reservation date
	public $rdate;
	
	/**
	 * ReservedTable class constructor
	 * var $i - Table ID
	 * var $c - Customer ID
	 * var $d - Reservation date
	 */
	public function __construct( $i, $c, $d )
	{
		// Assign values to object attributes
		$this->id = $i;
		$this->customerid = $c;
		$this->rdate = $d;
	}
}

// Class for ReservedParkingSpace database entity
class ReservedParkingSpace
{
	// Space ID
	public $id;
	// Customer ID
	public $customerid;
	// Reservation date
	public $rdate;
	
	/**
	 * ReservedParkingSpace class constructor
	 * var $i - Space ID
	 * var $c - Customer ID
	 * var $d - Reservation date
	 */
	public function __construct( $i, $c, $d )
	{
		// Assign values to object attributes
		$this->id = $i;
		$this->customerid = $c;
		$this->rdate = $d;
	}
}	
?>