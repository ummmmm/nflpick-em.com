<?php

class Sessions
{
	private $_db;
	private $_error;

	public function __construct( Database &$db )
	{
		$this->_db = $db;
	}

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

		return $this->_db->query( $sql );
	}

	public function Generate( $user_id )
	{
		$cookie_id	= sha1( session_id() );
		$token		= sha1( uniqid( rand(), TRUE ) );
		$session	= array( 'token' => $token, 'cookie_id' => $cookie_id, 'user_id' => $user_id );

		setcookie( 'session', $cookie_id, time() + 60 * 60 * 24 * 30, '/' );

		return $this->Insert( $session );
	}

	private function _Insert( $session )
	{
		return $this->_db->insert( 'sessions', $session );
	}

	public function Load( $cookie_id, &$session )
	{
		return $this->_db->single( 'SELECT * FROM sessions WHERE cookieid = ?', $session, $cookie_id );
	}

	public function Delete_User( $user_id )
	{
		return $this->_db->query( 'DELETE FROM sessions WHERE userid = ?', $user_id );
	}

	/*public function Delete( $cookie_id )
	{
		return $this->_db->query( 'DELETE FROM Sessions WHERE cookie_id = ?', $cookie_id );
	}

	public function Delete_UserID( $user_id )
	{
		return $this->_db->query( 'DELETE FROM Sessions WHERE user_id = ?', $user_id );
	}*/

	private function _Error( $message )
	{
		$this->_error = $message;

		return false;
	}

	public function Get_Error()
	{
		return $this->_error;
	}



	public static function Insert( &$db, $session )
	{
		$session[ 'date' ] 			= time();
		$session[ 'last_active' ]	= time();

		return $db->insert( 'sessions', $session );
	}

	public static function Delete( &$db, $token )
	{
		return $db->query( 'DELETE FROM sessions WHERE token = ?', $token );
	}

	public static function Delete_Cookie( &$db, $cookie_id )
	{
		return $db->query( 'DELETE FROM sessions WHERE cookieid = ?', $cookie_id );
	}

	public static function Update( &$db, $session )
	{
		return $db->query( 'UPDATE sessions SET token = ?, userid = ?, date = ? WHERE cookieid = ?', $session[ 'token' ], $session[ 'userid' ], $session[ 'date' ], $session[ 'cookieid' ] );
	}

	public static function Update_Cookie_LastActive( &$db, $cookieid )
	{
		$date = time();

		return $db->query( 'UPDATE sessions SET last_active = ? WHERE cookieid = ?', $date, $cookieid );
	}

	public static function Validate( &$db, $userid, $token )
	{
		$cookieid = Functions::Cookie( 'session' );

		$count = $db->single( 'SELECT * FROM sessions WHERE token = ? AND cookieid = ? AND userid = ?', $null, $token, $cookieid, $userid );

		return $count ? true : false;
	}

	public static function Validate_Admin( &$db, $userid, $token )
	{
		$cookieid = Functions::Cookie( 'session' );

		$count = $db->single( 'SELECT
								u.id
							   FROM
								sessions s,
								users u
							   WHERE
								s.token 	= ? 	AND
								s.cookieid 	= ? 	AND
								s.userid 	= ? 	AND
								s.userid 	= u.id 	AND
								u.admin 	= 1', $null, $token, $cookieid, $userid );

		return $count ? true : false;
	}
}
