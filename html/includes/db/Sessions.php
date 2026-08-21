<?php

class DatabaseTableSessions extends DatabaseTable
{
	public function Create()
	{
		$sql = "CREATE TABLE sessions
				(
					cookieid 	char( 64 ),
					token 		char( 64 ),
					userid 		int( 11 ),
					expires 	int( 11 ),
					last_active int( 11 ),
					UNIQUE KEY cookieid_1( cookieid ),
					UNIQUE KEY sessions_1 ( token )
				)";

		return $this->query( $sql );
	}

	public function Load( $cookieid, &$session )
	{
		return $this->single( 'SELECT * FROM sessions WHERE cookieid = ? AND expires >= ?', $session, $cookieid, time() );
	}

	public function Delete_User( $user_id )
	{
		return $this->query( 'DELETE FROM sessions WHERE userid = ?', $user_id );
	}

	public function Load_User_Token( $user_id, $token, &$session )
	{
		return $this->single( 'SELECT * FROM sessions WHERE userid = ? AND token = ?', $session, $user_id, $token );
	}

	public function Insert( $session )
	{
		return $this->query( 'INSERT INTO sessions ( token, cookieid, userid, expires, last_active ) VALUES ( ?, ?, ?, ?, ? )', $session[ 'token' ], $session[ 'cookieid' ], $session[ 'userid' ], $session[ 'expires' ], $session[ 'last_active' ] );
	}

	public function Delete( $token )
	{
		return $this->query( 'DELETE FROM sessions WHERE token = ?', $token );
	}

	public function Delete_Cookie( $cookieid )
	{
		return $this->query( 'DELETE FROM sessions WHERE cookieid = ?', $cookieid );
	}

	public function Delete_All_Expired()
	{
		return $this->query( 'DELETE FROM sessions WHERE expires < ?', time() );
	}

	public function Update_Cookie_Last_Active( $cookieid )
	{
		$date = time();

		return $this->query( 'UPDATE sessions SET last_active = ? WHERE cookieid = ?', $date, $cookieid );
	}
}
