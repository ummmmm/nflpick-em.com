<?php

class JSON_LoadWeeks implements iJSON
{
	public function __construct( Database &$db, Authentication &$auth, JSON &$json )
	{
		$this->_db		= $db;
		$this->_auth	= $auth;
		$this->_json	= $json;
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
			return $this->_json->DB_Error();
		}
		
		foreach( $weeks as &$week )
		{
			$week[ 'formatted_date' ] = Functions::FormatDate( $week[ 'date' ] );
		}
		
		return $this->_json->setData( $weeks );
	}
}
