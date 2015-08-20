<?php

class Reset_Passwords
{
	private $_db;

	public function __construct( Database &$db )
	{
		$this->_db = $db;
	}

	public function Create()
	{
		$sql = "CREATE TABLE reset_password
				(
					userid 		int( 11 ),
					password 	varchar( 255 ),
					date 		int( 11 ),
					UNIQUE KEY reset_password_1 ( password )
				)";

		return $this->_db->query( $sql );
	}

	public function Insert( &$insert )
	{
		$insert[ 'date' ] = time();

		return $this->_db->insert( 'reset_password', $insert );
	}

	public function Delete_User( $user_id )
	{
		return $this->_db->query( 'DELETE FROM reset_password WHERE userid = ?', $user_id );
	}

	public function Load_User( $user_id, &$record )
	{
		return $this->_db->single( "SELECT * FROM reset_password WHERE userid = ?", $record, $user_id );
	}
}
