<?php
include_once( 'functions.php' );

class Authentication
{
	private $_sessions;
	private $_users;
	private $_db;

	public $user;
	public $authenticated;
	public $userID;

	public function __construct()
	{
		$this->_db				= new Database();
		$this->_sessions 		= new Sessions( $this->_db );
		$this->_users			= new Users( $this->_db );
		$this->user				= array();
		$this->authenticated	= false;
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
				$this->user 			= $user;
				$this->session			= $session;
				$this->userID			= $user[ 'id' ];
				$this->authenticated	= true;
			}
		}
	}

	public function User()
	{
		$cookie_id = Functions::Cookie( 'session' );

		if ( $this->_sessions->Load( $cookie_id, $session ) )
		{
			if ( $this->_users->Load( $session[ 'user_id' ], $user ) )
			{
				$this->user 			= $user;
				$this->authenticated 	= true;
				return true;
			}
		}

		return false;
	}

	public function Admin()
	{
		if ( !$this->User() )
		{
			return false;
		}

		if ( !$this->user[ 'admin' ] )
		{
			$this->user = array();
			return false;
		}

		return true;
	}

	public function isAdmin()
	{
		if ( $this->authenticated && $user[ 'admin' ] )
		{
			return true;
		}

		return false;
	}

	public function isUser()
	{
		return $this->authenticated;
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
?>
