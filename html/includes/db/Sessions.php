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

	public function Generate( $user_id )
	{
		$cookie_id	= sha1( session_id() );
		$token		= sha1( uniqid( rand(), TRUE ) );
		$session	= array( 'token' => $token, 'cookieid' => $cookie_id, 'userid' => $user_id );

		setcookie( 'session', $cookie_id, time() + 60 * 60 * 24 * 30, INDEX, '', true, true );

		return $this->Insert( $session );
	}

	public function Load( $cookie_id, &$session )
	{
		return $this->single( 'SELECT * FROM sessions WHERE cookieid = ?', $session, $cookie_id );
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

	public function Delete_Cookie( $cookie_id )
	{
		return $this->query( 'DELETE FROM sessions WHERE cookieid = ?', $cookie_id );
	}

	public function Update_Cookie_LastActive( $cookieid )
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
