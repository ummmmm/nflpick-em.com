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
		$current	= $db_weeks->Current();
		
		$db_weeks->List_Load( $weeks );
		
		foreach( $weeks as &$week )
		{
			$db_games->List_Load_Week( $week[ 'id' ], $week[ 'games' ] );

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
