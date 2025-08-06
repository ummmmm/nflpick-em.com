<?php

class Screen_UpdateScores extends Screen_Admin
{
	public function content()
	{
		print '<h1>Update Scores</h1>';

		$db_games	= $this->db()->games();
		$db_teams	= $this->db()->teams();
		$db_users	= $this->db()->users();
		$db_weeks	= $this->db()->weeks();
		$data		= json_decode( file_get_contents( sprintf( 'https://site.api.espn.com/apis/site/v2/sports/football/nfl/scoreboard?week=%d', $db_weeks->Previous() ) ) );
		$week_id	= $data->week->number;

		if ( !$db_weeks->IsLocked( $week_id ) )
		{
			printf( 'Week %d is not locked yet, no scores updated', $week_id );

			return true;
		}

		foreach ( $data->events as $event )
		{
			$competition	= $event->competitions[ 0 ];
			$team1			= $competition->competitors[ 0 ];
			$team2			= $competition->competitors[ 1 ];

			if ( $team1->homeAway == 'home' )
			{
				$home = $team1;
				$away = $team2;
			}
			else
			{
				$home = $team2;
				$away = $team1;
			}

			if ( !$db_teams->Load_Abbr( $home->team->abbreviation, $homeTeam ) ||
				 !$db_teams->Load_Abbr( $away->team->abbreviation, $awayTeam ) )
			{
				printf( 'Skipped <b>%s</b> vs. <b>%s</b> because the teams could not be loaded<br />', htmlentities( $away->team->displayName ), htmlentities( $home->team->displayName ) );
				continue;
			}

			if ( !$db_games->Load_Week_Teams( $week_id, $awayTeam[ 'id' ], $homeTeam[ 'id' ], $game ) )
			{
				printf( 'Skipped <b>%s</b> vs. <b>%s</b> because the game could not be found<br />', htmlentities( $awayTeam[ 'team' ] ), htmlentities( $homeTeam[ 'team' ] ) );
				continue;
			}

			if ( !$competition->status->type->completed )
			{
				printf( 'Skipped <b>%s</b> vs. <b>%s</b> because the game is not over yet<br />', htmlentities( $awayTeam[ 'team' ] ), htmlentities( $homeTeam[ 'team' ] ) );
				continue;
			}

			$away_score = $away->score;
			$home_score = $home->score;

			if ( $home_score == $away_score )
			{
				$game[ 'tied' ]		= 1;
				$game[ 'winner' ]	= 0;
				$game[ 'loser' ]	= 0;
			}
			else
			{
				$game[ 'tied' ]		= 0;
				$game[ 'winner' ] 	= ( $home_score > $away_score ) ? $homeTeam[ 'id' ] : $awayTeam[ 'id' ];
				$game[ 'loser' ]	= ( $home_score > $away_score ) ? $awayTeam[ 'id' ] : $homeTeam[ 'id' ];
			}

			$game[ 'homeScore' ]	= $home_score;
			$game[ 'awayScore' ]	= $away_score;
			$game[ 'final' ]		= 1;

			$db_games->Update( $game );
		}

		$db_teams->Recalculate_Records();

		if ( !Functions::Update_Records( $this->db() ) )
		{
			throw new NFLPickEmException( 'Failed to update weekly / user records' );
		}

		printf( '<p><b>Games Updated</b></p>' );

		return true;
	}
}
