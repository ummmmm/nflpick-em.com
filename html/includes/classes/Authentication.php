<?php
include_once( 'functions.php' );
include_once( 'Sessions.php' );

class Authentication
{
	private $_sessions;
	private $_users;

	public $user;
	public $authenticated;

	public function __construct()
	{
		$this->_sessions 		= new Sessions();
		$this->_users			= new Users();
		$this->user				= array();
		$this->authenticated	= false;
	}

	public function __destruct()
	{
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
