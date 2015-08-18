<?php

require_once( "Database.php" );
require_once( "functions.php" );

class Authentication
{
	private $_sessions;
	private $_users;
	private $_db;

	public $user;
	public $userID;

	public function __construct()
	{
		$this->_db				= new Database();
		$this->_sessions 		= new Sessions( $this->_db );
		$this->_users			= new Users( $this->_db );

		$this->user				= array();
		$this->userID			= 0;

		$this->_initialize();
	}

	private function _initialize()
	{
		$cookie_id = Functions::Cookie( 'session' );

		if ( $this->_sessions->Load( $cookie_id, $session ) )
		{
			if ( $this->_users->Load( $session[ 'userid' ], $user ) )
			{
				$this->session	= $session;
				$this->user 	= $user;
				$this->userID	= $user[ 'id' ];
			}
		}
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
		$count = $this->_sessions->Load_User_Token( $this->userID, $token, $null );

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
