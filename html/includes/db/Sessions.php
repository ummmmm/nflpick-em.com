<?php

class DatabaseTableSessions extends DatabaseTable
{
	public function Create()
	{
		$sql = "CREATE TABLE sessions
				(
					token 		varchar( 40 ),
					cookieid 	varchar( 40 ),
					userid 		int( 11 ),
					date 		int( 11 ),
					last_active int( 11 ),
					UNIQUE KEY sessions_1 ( token )
				)";

		return $this->query( $sql );
	}

	public function Load( $cookieid, &$session )
	{
		return $this->single( 'SELECT * FROM sessions WHERE cookieid = ?', $session, $cookieid );
	}

	public function Delete_User( $user_id )
	{
		return $this->query( 'DELETE FROM sessions WHERE userid = ?', $user_id );
	}

	private function _Error( $message )
	{
		$this->_error = $message;

		return false;
	}

	public function Get_Error()
	{
		return $this->_error;
	}

	public function Load_User_Token( $user_id, $token, &$session )
	{
		return $this->single( 'SELECT * FROM sessions WHERE userid = ? AND token = ?', $session, $user_id, $token );
	}

	public function Insert( $session )
	{
		$session[ 'date' ] 			= time();
		$session[ 'last_active' ]	= time();

		return $this->query( 'INSERT INTO sessions ( token, cookieid, userid, date, last_active ) VALUES ( ?, ?, ?, ?, ? )', $session[ 'token' ], $session[ 'cookieid' ], $session[ 'userid' ], $session[ 'date' ], $session[ 'last_active' ] );
	}

	public function Delete( $token )
	{
		return $this->query( 'DELETE FROM sessions WHERE token = ?', $token );
	}

	public function Delete_Cookie( $cookieid )
	{
		return $this->query( 'DELETE FROM sessions WHERE cookieid = ?', $cookieid );
	}

	public function Update_Cookie_Last_Active( $cookieid )
	{
		$date = time();

		return $this->query( 'UPDATE sessions SET last_active = ? WHERE cookieid = ?', $date, $cookieid );
	}

	public static function Validate( &$db, $userid, $token )
	{
		$cookieid = Functions::Cookie( 'session' );

		$count = $db->single( 'SELECT * FROM sessions WHERE token = ? AND cookieid = ? AND userid = ?', $null, $token, $cookieid, $userid );

		return $count ? true : false;
	}
}
