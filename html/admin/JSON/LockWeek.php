<?php

class JSON_LockWeek implements iJSON
{
	private $_db;
	private $_auth;
	private $_error;
	private $_data;

	public function __construct( Database &$db, Authentication &$auth )
	{
		$this->_db		= $db;
		$this->_auth	= $auth;
		$this->_data	= null;
		$this->_error	= array();
	}

	public function requirements()
	{
		return array( 'user' => true, 'admin' => true, 'token' => true );
	}

	public function execute()
	{
		$db_weeks	= new Weeks( $this->_db );
		$week_id 	= Functions::Post( 'week_id' );
		$count 		= $db_weeks->Load( $week_id, $week );

		if ( $count === false )
		{
			return $this->_setError( $this->_db->Get_Error() );
		}

		if ( $count === 0 )
		{
			return $this->_setError( array( "#Error#", "Failed to load week" ) );
		}

		$week[ 'locked' ] = ( $week[ 'locked' ] === 1 ) ? 0 : 1;

		if ( !$db_weeks->Update( $week ) )
		{
			return $this->_setError( $this->_db->Get_Error() );
		}

		return true;
	}

	public function getData()
	{
		return $this->_data;
	}

	public function getError()
	{
		return $this->_error;
	}

	public function _setData( $data )
	{
		$this->_data = $data;
		return true;
	}

	private function _setError( $error )
	{
		$this->_error = $error;
		return false;
	}
}
