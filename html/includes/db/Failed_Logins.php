<?php

class Failed_Logins
{
	private $_db;

	public function __construct( Database &$db )
	{
		$this->_db = $db;
	}

	public function Create()
	{
		$sql = "CREATE TABLE failed_logins
				(
			  		id 			int( 11 ) AUTO_INCREMENT,
			  		email 		varchar( 50 ),
			  		dt 			int( 11 ),
			  		ip 			varchar( 255 ),
			  		PRIMARY KEY ( id )
			  	)";

		return $this->_db->query( $sql );
	}

	public function Insert( $email )
	{
		$values = array( 'email' => $email, 'dt' => time(), 'ip' => $_SERVER[ 'REMOTE_ADDR' ] );

		return $this->_db->insert( 'failed_logins', $values );
	}
}
