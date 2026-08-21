<?php

class DatabaseTableResetPasswords extends DatabaseTable
{
	public function Create()
	{
		$sql = "CREATE TABLE reset_password
				(
					userid 		int( 11 ),
					password 	varchar( 255 ),
					expires 	int( 11 ),
					UNIQUE KEY reset_password_1 ( userid )
				)";

		return $this->query( $sql );
	}

	public function Insert( &$insert )
	{
		return $this->query( 'INSERT INTO reset_password ( userid, password, expires ) VALUES ( ?, ?, ? )', $insert[ 'userid' ], $insert[ 'password' ], $insert[ 'expires' ] );
	}

	public function Delete_User( $user_id )
	{
		return $this->query( 'DELETE FROM reset_password WHERE userid = ?', $user_id );
	}

	public function Load_User( $user_id, &$record )
	{
		return $this->single( "SELECT * FROM reset_password WHERE userid = ? AND expires >= ?", $record, $user_id, time() );
	}

	public function Delete_All_Expired()
	{
		return $this->query( 'DELETE FROM reset_password WHERE expires < ?', time() );
	}
}
