<?php
function Module_Content( &$db, &$user )
{
	print '<h1>Update Scores</h1>';

	try
	{
		$db_games	= new Games( $db );
		$db_teams	= new Teams( $db );
		$db_users	= new Users( $db );
		$db_weeks	= new Weeks( $db );
		$doc 		= new SimpleXMLElement( 'http://www.nfl.com/liveupdate/scorestrip/ss.xml', 0, true );
		$week		= ( int ) $doc->gms->attributes()->w;

		if ( !$db_weeks->IsLocked( $week ) )
		{
			printf( 'Week %d is not locked yet, no scores updated', $week );

			return true;
		}

		foreach( $doc->gms->g as $nfl_game )
		{
			$g			= $nfl_game->attributes();
			$quarter	= ( string ) $g->q;
			$home 		= ( string ) $g->h;
			$away		= ( string ) $g->v;
			$homeScore	= ( int ) $g->hs;
			$awayScore	= ( int ) $g->vs;

			if ( !$db_teams->Load_Abbr( $home, $homeTeam ) ||
				 !$db_teams->Load_Abbr( $away, $awayTeam ) )
			{
				printf( 'Skipped <b>%s</b> vs. <b>%s</b> because the teams could not be loaded<br />', $away, $home );
				continue;
			}

			if ( $quarter != 'F' && $quarter != 'FO' )
			{
				printf( 'Skipped <b>%s</b> vs. <b>%s</b> because the game is not over yet<br />', $awayTeam[ 'team' ], $homeTeam[ 'team' ] );
				continue;
			}

			if ( $homeScore == $awayScore )
			{
				printf( 'Skipped <b>%s</b> vs. <b>%s</b> because the game ended in a tie<br />', $awayTeam[ 'team' ], $homeTeam[ 'team' ] );
				continue;
			}

			if ( !$db_games->Load_Week_Teams( $week, $awayTeam[ 'id' ], $homeTeam[ 'id' ], $game ) )
			{
				printf( 'Skipped <b>%s</b> vs. <b>%s</b> because the game could not be found<br />', $awayTeam[ 'team' ], $homeTeam[ 'team' ] );
				continue;
			}

			$game[ 'winner' ] 		= ( $homeScore > $awayScore ) ? $homeTeam[ 'id' ] : $awayTeam[ 'id' ];
			$game[ 'loser' ]		= ( $homeScore > $awayScore ) ? $awayTeam[ 'id' ] : $homeTeam[ 'id' ];
			$game[ 'homeScore' ]	= $homeScore;
			$game[ 'awayScore' ]	= $awayScore;

			if ( !$db_games->Update( $game ) )
			{
				return false;
			}
		}

		if ( !$db_teams->Recalculate_Records() || !$db_users->Recalculate_Records()	)
		{
			return false;
		}

		printf( '<p><b>Games Updated</b></p>' );
	}
	catch( Exception $e )
	{
		return Functions::Error( 'NFL-UPDATE_SCORES-1', $e->getmessage() );
	}

	return true;
}
?>