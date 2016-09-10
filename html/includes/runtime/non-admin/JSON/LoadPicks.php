<?php

class JSON_LoadPicks extends JSONUser
{
	public function execute()
	{
		$db_games	= new Games( $this->_db );
		$db_picks	= new Picks( $this->_db );
		$db_weeks	= new Weeks( $this->_db );
		$week_id	= Functions::Post( 'week_id' );

		$count = $db_weeks->Load( $week_id, $week );

		if ( $count === false )
		{
			return $this->setDBError();
		}

		if ( $count === 0 )
		{
			return $this->setError( array( '#Error#', 'Failed to load week' ) );
		}

		$db_games->List_Load_Week( $week_id, $week[ 'games' ] );

		foreach( $week[ 'games' ] as &$game )
		{
			$count = $db_picks->Load_User_Game( $this->_auth->getUserID(), $game[ 'id' ], $pick );

			if ( $count === false )
			{
				return $this->setDBError();
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
