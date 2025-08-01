<?php

class Screen_Schedule extends Screen
{
	public function content()
	{
		$db_games	= $this->db()->games();
		$db_teams	= $this->db()->teams();
		$db_weeks	= $this->db()->weeks();
		$week_id 	= Functions::Get( 'week' );

		if ( $week_id == '' )
		{
			$count = $db_weeks->List_Load( $weeks );

			if ( $count === false )
			{
				return false;
			}

			print '<h1>Weeks</h1>';

			foreach( $weeks as $loaded_week )
			{
				printf( '<p><a href="?screen=schedule&week=%d">Week %d</a></p>', $loaded_week[ 'id' ], $loaded_week[ 'id' ] );
			}

			return true;
		}

		if ( !$db_weeks->Load( $week_id, $loaded_week ) )
		{
			return $this->setError( array( '#Error#', 'Invalid week' ) );
		}

		if ( !$db_games->List_Load_Week( $week_id, $games ) )
		{
			return $this->setDBError();
		}

		$count = $db_teams->List_Load_Byes( $week_id, $teams );

		if ( $count === false )
		{
			return false;
		}

		printf( '<h1>Week %d</h1>', $week_id );

		foreach( $games as $game )
		{
			printf( "<p>%s (%d - %d%s) <b>vs.</b> %s (%d - %d%s)</p>", htmlentities( $game[ 'awayTeam' ] ), $game[ 'awayWins' ], $game[ 'awayLosses' ], ( $game[ 'awayTies' ] ? sprintf( ' - %d', $game[ 'awayTies' ] ) : '' ), htmlentities( $game[ 'homeTeam' ] ), $game[ 'homeWins' ], $game[ 'homeLosses' ], ( $game[ 'homeTies' ] ? sprintf( ' - %d', $game[ 'homeTies' ] ) : '' ) );
		}

		if ( count( $teams ) > 0 )
		{
			printf( '<p><b>Bye Teams:</b> %s</p>', htmlentities( join( ', ', array_map( function( $team ) { return $team[ 'team' ]; }, $teams ) ) ) );
		}

		return true;
	}
}
