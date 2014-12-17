<?php

function Module_Head()
{
	return true;
}

function Module_Content( &$db, &$user )
{
	print '<h1>Games Insert</h1>';

	$db_games	= new Games( $db ):
	$db_teams	= new Teams( $db );
	$db_weeks	= new Weeks( $db );
	$count 		= 0;
	$skip		= 0;
	$url 		= 'http://football.myfantasyleague.com/2014/export?TYPE=nflSchedule&W=';

	for ( $i = 1; $i <= 17; $i++ )
	{
		$xml = simplexml_load_file( sprintf( '%s%d', $url, $i ) );

		foreach ( $xml->matchup as $matchup )
		{
			$kickoff 	= ( int ) 		$matchup[ 'kickoff' ];
			$team1 		= ( string ) 	$matchup->team[ 0 ][ 'id' ];
			$team2 		= ( string ) 	$matchup->team[ 1 ][ 'id' ];

			if ( !$db_weeks->Load( $i, $null ) )
			{
				return Functions::Error( '#Error#', "Failed to load week {$i}" );
			}

			if ( !$db_teams->Load_Abbr( $team1, $loaded_team1 ) || !$db_teams->Load_Abbr( $team2, $loaded_team2 ) )
			{
				return Functions::Error( '#Error#', "Failed to load either {$team1} or {$team2}" );
			}

			if ( ( int ) $matchup->team[ 0 ][ 'isHome' ] )
			{
				$away = $loaded_team2[ 'id' ];
				$home = $loaded_team1[ 'id' ];
			}
			else
			{
				$away = $loaded_team1[ 'id' ];
				$home = $loaded_team2[ 'id' ];	
			}

			if ( $db_games->Exists_Week_Teams( $i, $home, $away, $null ) )
			{
				$skip++;
				continue;
			}

			$gamedate 	= new DateTime();
			$gamedate->setTimestamp( $kickoff );
			$game 		= array( 'away' => $away, 'home' => $home, 'date' => $gamedate->format( 'Y-m-d H:i:s' ), 'week' => $i );

			if ( !$db_games->Insert( $game ) )
			{
				return Functions::Error();
			}

			$count++;
		}
	}

	printf( 'Inserted %d games, skipped %d', $count, $skip );

	return true;
}
