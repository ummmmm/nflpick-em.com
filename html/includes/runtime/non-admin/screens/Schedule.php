<?php

class Screen_Schedule extends Screen
{
	public function content()
	{
		$db_games	= new Games( $this->_db );
		$db_teams	= new Teams( $this->_db );
		$db_weeks	= new Weeks( $this->_db );
		$week_id 	= Functions::Get( 'week' );

		if ( !Validation::Week( $week_id ) )
		{
			$count = $db_weeks->List_Load( $weeks );

			if ( $count === false )
			{
				return false;
			}

			print '<h1>Weeks</h1>';

			foreach( $weeks as $loaded_week )
			{
				print '<p><a href="?screen=schedule&week=' . $loaded_week[ 'id' ] . '">Week ' . $loaded_week[ 'id' ]. '</a></p>';
			}

			return true;
		}

		if ( !$db_weeks->Load( $week_id, $loaded_week ) )
		{
			return false;
		}

		$count = $db_games->List_Load_Week( $week_id, $games );

		if ( $count === false )
		{
			return false;
		}

		$count = $db_teams->Byes( $week_id, $teams );

		if ( $count === false )
		{
			return false;
		}

		print '<h1>Week ' . $week_id . '</h1>';

		foreach( $games as $game )
		{
			print "<p>{$game[ 'awayTeam' ]} ({$game[ 'awayWins' ]} - {$game[ 'awayLosses' ]}) <b>vs.</b> {$game[ 'homeTeam' ]} ({$game[ 'homeWins' ]} - {$game[ 'homeLosses' ]})</p>";
		}

		if ( is_null( $teams[ 'bye_teams' ] ) === false )
		{
			print '<p><b>Bye Teams:</b> ' . $teams[ 'bye_teams' ] . '</p>';
		}

		return true;
	}
}
