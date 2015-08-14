<?php

class JSON_LockWeek implements iJSON
{
	public function __construct( Database &$db, Authentication &$auth, JSON &$json )
	{
		$this->_db		= $db;
		$this->_auth	= $auth;
		$this->_json	= $json;
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
			return $this->_json->DB_Error();
		}

		if ( $count === 0 )
		{
			return $this->_json->setError( array( "#Error#", "Failed to load week" ) );
		}

		$week[ 'locked' ] = ( $week[ 'locked' ] === 1 ) ? 0 : 1;

		if ( !$db_weeks->Update( $week ) )
		{
			return $this->_json->DB_Error();
		}

		return true;
	}
}
