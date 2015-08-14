<?php

class JSON_LoadWeeks implements iJSON
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
		return array( 'user' => true, 'admin' => true );
	}

	public function execute()
	{
		$db_weeks 	= new Weeks( $this->_db );
		$count 		= $db_weeks->List_Load( $weeks );
		
		if ( $count === false )
		{
			return $this->_setError( $this->_db->Get_Error() );
		}
		
		foreach( $weeks as &$week )
		{
			$week[ 'formatted_date' ] = Functions::FormatDate( $week[ 'date' ] );
		}
		
		return $this->_setData( $weeks );
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
