<?php
function Module_Content( &$db, &$user )
{
	print '<h1>Update Scores</h1>';

	try
	{
		$doc 	= new SimpleXMLElement( 'http://www.nfl.com/liveupdate/scorestrip/ss.xml', 0, true );
		$week	= (int) $doc->gms->attributes()->w;

		if ( !Weeks::IsLocked( $db, $week ) )
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

			if ( !Teams::Load_Abbr( $db, $home, $homeTeam ) ||
				 !Teams::Load_Abbr( $db, $away, $awayTeam ) )
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

			if ( !Games::Load_Week_Teams( $db, $week, $awayTeam[ 'id' ], $homeTeam[ 'id' ], $game ) )
			{
				printf( 'Skipped <b>%s</b> vs. <b>%s</b> because the game could not be found<br />', $awayTeam[ 'team' ], $homeTeam[ 'team' ] );
				continue;
			}

			$game[ 'winner' ] 		= ( $homeScore > $awayScore ) ? $homeTeam[ 'id' ] : $awayTeam[ 'id' ];
			$game[ 'loser' ]		= ( $homeScore > $awayScore ) ? $awayTeam[ 'id' ] : $homeTeam[ 'id' ];
			$game[ 'homeScore' ]	= $homeScore;
			$game[ 'awayScore' ]	= $awayScore;

			if ( !Games::Update( $db, $game ) )
			{
				return false;
			}
		}

		if ( !Teams::Recalculate_Records( $db ) ||
			 !Users::Recalculate_Records( $db )	)
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