<?php

class DatabaseTableFailedLogins extends DatabaseTable
{
	public function Create()
	{
		$sql = "CREATE TABLE failed_logins
				(
			  		id 			int( 11 ) AUTO_INCREMENT,
			  		email 		varchar( 50 ),
			  		date 		int( 11 ),
			  		ip 			varchar( 255 ),
			  		PRIMARY KEY ( id )
			  	)";

		return $this->query( $sql );
	}

	public function Insert( $email )
	{
		$values = array( 'email' => $email, 'date' => time(), 'ip' => $_SERVER[ 'REMOTE_ADDR' ] );

		return $this->query( 'INSERT INTO failed_logins ( email, date, ip ) VALUES ( ?, ?, ? )', $values[ 'email' ], $values[ 'date' ], $values[ 'ip' ] );
	}

	public function List_Load( &$logins )
	{
		return $this->select( 'SELECT * FROM failed_logins ORDER BY id DESC', $logins );
	}
}
