<?php

require_once( "Database.php" );
require_once( "functions.php" );

class Authentication
{
	private $_db_manager;
	private $_user;
	private $_userID;
	private $_token;
	private $_reload;

	public function __construct( $db_manager )
	{
		$this->_db_manager	= $db_manager;
		$this->_user		= array();
		$this->_userID		= 0;
		$this->_token		= 0;
		$this->_reload		= false;
	}

	public function initialize()
	{
		$cookie_id = Functions::Cookie( 'session' );

		if ( $this->_db_manager->sessions()->Load( $cookie_id, $session ) && $this->_db_manager->users()->Load( $session[ 'userid' ], $user ) )
		{
			$this->_user 	= $user;
			$this->_userID	= $user[ 'id' ];
			$this->_token	= $session[ 'token' ];
		}

		return true;
	}

	public function forceUserReload()
	{
		$this->_reload = true;
	}

	public function getUserID()
	{
		return $this->_userID;
	}

	public function getUser()
	{
		if ( $this->_reload )
		{
			$this->_reload = false;
			$this->_db_manager->users()->Load( $this->_userID, $this->_user );
		}
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
		$db_sessions	= $this->_db_manager->sessions();
		$count 			= $db_sessions->Load_User_Token( $this->_userID, $token, $null );

		return $count ? true : false;
	}
}
