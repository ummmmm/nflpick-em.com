<?php

class JSON_LoadPicks extends JSONUser
{
	public function execute()
	{
		$db_games	= $this->db()->games();
		$db_picks	= $this->db()->picks();
		$db_weeks	= $this->db()->weeks();
		$week_id	= $this->input()->value_int( 'week_id' );

		if ( !$db_weeks->Load( $week_id, $week ) )
		{
			throw new NFLPickEmException( 'Week does not exist' );
		}

		$db_games->List_Load_Week( $week_id, $week[ 'games' ] );

		foreach( $week[ 'games' ] as &$game )
		{
			if ( !$db_picks->Load_User_Game( $this->auth()->getUserID(), $game[ 'id' ], $pick ) )
			{
				$pick = null;
			}

			$now	= new DateTime();
			$date 	= new DateTime();
			$date->setTimestamp( $game[ 'date' ] );

			$game[ 'past' ] 		 	= ( $now > $date ) ? true : false;
			$game[ 'time_formatted' ] 	= $date->format( 'h:i a' );
			$game[ 'date_formatted' ] 	= $date->format( 'F d, Y' );
			$game[ 'date_javascript' ]	= $date->format( 'F d, Y h:i:s' );
			$game[ 'pick' ] 			= $pick;
		}

		return $this->setData( $week );
	}
}
