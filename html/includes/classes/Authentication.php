<?php

require_once( "Database.php" );
require_once( "functions.php" );

class Authentication
{
	private $_db;
	private $_user;
	private $_userID;
	private $_token;

	public function __construct()
	{
		$this->_db		= new Database();
		$this->_user	= array();
		$this->_userID	= 0;
		$this->_token	= 0;

		$this->_initialize();
	}

	private function _initialize()
	{
		$cookie_id		= Functions::Cookie( 'session' );
		$db_users		= new Users( $this->_db );
		$db_sessions	= new Sessions( $this->_db );

		if ( $db_sessions->Load( $cookie_id, $session ) && $db_users->Load( $session[ 'userid' ], $user ) )
		{
			$this->_user 	= $user;
			$this->_userID	= $user[ 'id' ];
			$this->_token	= $session[ 'token' ];
		}
	}

	public function getUserID()
	{
		return $this->_userID;
	}

	public function getUser()
	{
		return $this->_user;
	}

	public function getToken()
	{
		return $this->_token;
	}

	public function isUser()
	{
		return $this->_userID ? true : false;
	}

	public function isAdmin()
	{
		return $this->_userID && $this->_user[ 'admin' ];
	}

	public function isValidToken( $token )
	{
		$db_sessions	= new Sessions( $this->_db );
		$count 			= $db_sessions->Load_User_Token( $this->_userID, $token, $null );

		return $count ? true : false;
	}
}
