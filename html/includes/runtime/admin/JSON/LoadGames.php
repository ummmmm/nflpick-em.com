<?php

class JSON_LoadGames implements iJSON
{
	public function __construct( Database &$db, Authentication &$auth, JSON &$json )
	{
		$this->_db		= $db;
		$this->_auth	= $auth;
		$this->_json	= $json;
	}

	public function requirements()
	{
		return array( 'admin' => true, 'token' => true );
	}

	public function execute()
	{
		$db_games	= new Games( $this->_db );
		$db_weeks	= new Weeks( $this->_db );
		$count 		= $db_weeks->List_Load( $weeks );
		
		if ( $count === false )
		{
			return $this->_json->DB_Error();
		}
		
		foreach( $weeks as &$week )
		{
			$count = $db_games->List_Load_Week( $week[ 'id' ], $week[ 'games' ] );
			
			if ( $count === false )
			{
				return $this->_json->DB_Error();
			}

			foreach( $week[ 'games' ] as &$game )
			{
				$date = new DateTime();
				$date->setTimestamp( $game[ 'date' ] );
				$date->setTimezone( new DateTimeZone( 'America/Los_Angeles' ) );
				$game[ 'date' ] = $date->format( DATE_ISO8601 );
			}
		}
		
		return $this->_json->setData( $weeks );
	}
}
