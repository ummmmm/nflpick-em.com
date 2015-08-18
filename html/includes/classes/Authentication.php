<?php

require_once( "Database.php" );
require_once( "functions.php" );

class Authentication
{
	private $_db;
	public $user;
	public $userID;

	public function __construct()
	{
		$this->_db		= new Database();
		$this->user		= array();
		$this->userID	= 0;

		$this->_initialize();
	}

	private function _initialize()
	{
		$cookie_id		= Functions::Cookie( 'session' );
		$db_users		= new Users( $this->_db );
		$db_sessions	= new Sessions( $this->_db );

		if ( $db_sessions->Load( $cookie_id, $session ) && $db_users->Load( $session[ 'userid' ], $user ) )
		{
			$this->user 	= $user;
			$this->userID	= $user[ 'id' ];
		}
	}

	public function getUserID()
	{
		return $this->userID;
	}

	public function getUser()
	{
		return $this->user;
	}

	public function isAdmin()
	{
		return $this->userID && $this->user[ 'admin' ];
	}

	public function isUser()
	{
		return $this->userID ? true : false;
	}

	public function isValidToken( $token )
	{
		$db_sessions	= new Sessions( $this->_db );
		$count 			= $db_sessions->Load_User_Token( $this->userID, $token, $null );

		return $count ? true : false;
	}

	public function __get( $property )
	{
		if ( property_exists( $this, $property ) )
		{
			return $this->$property;
		}
	}

	public function __set( $property, $value )
	{
		if ( property_exists( $this, $property ) )
		{
			$this->$property = $value;
		}

		return $this;
	}
}
