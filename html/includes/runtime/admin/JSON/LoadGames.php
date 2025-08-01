<?php

class JSON_LoadGames extends JSONAdmin
{
	public function requirements()
	{
		return array( 'admin' => true, 'token' => true );
	}

	public function execute()
	{
		$db_games	= $this->db()->games();
		$db_weeks	= $this->db()->weeks();
		$count 		= $db_weeks->List_Load( $weeks );
		$current	= $db_weeks->Current();
		
		if ( $count === false )
		{
			return $this->setDBError();
		}
		
		foreach( $weeks as &$week )
		{
			$count = $db_games->List_Load_Week( $week[ 'id' ], $week[ 'games' ] );
			
			if ( $count === false )
			{
				return $this->setDBError();
			}

			foreach( $week[ 'games' ] as &$game )
			{
				$date = new DateTime();
				$date->setTimestamp( $game[ 'date' ] );
				$date->setTimezone( new DateTimeZone( 'America/Los_Angeles' ) );
				$game[ 'date' ] = $date->format( DATE_ISO8601 );
			}
		}
		
		return $this->setData( array( 'current_week' => $current, 'weeks' => $weeks ) );
	}
}
