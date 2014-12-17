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
					date 		datetime,
					UNIQUE KEY reset_password_1 ( password )
				)";

		return $this->_db->query( $sql );
	}

	public function Insert( &$insert )
	{
		$insert[ 'date' ] = Functions::Timestamp();

		return $this->_db->insert( 'reset_password', $insert );
	}

	public function Delete_User( $user_id )
	{
		return $this->_db->query( 'DELETE FROM reset_password WHERE userid = ?', $user_id );
	}
}
