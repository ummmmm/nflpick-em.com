<?php

function Module_Content( &$db, &$user )
{
	$db_games	= new Games( $db );
	$db_teams	= new Teams( $db );
	$db_weeks	= new Weeks( $db );
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
			print '<p><a href="?module=schedule&week=' . $loaded_week[ 'id' ] . '">Week ' . $loaded_week[ 'id' ]. '</a></p>';
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

function ByeTeams( &$db, $week_id, &$teams )
{
	return $db->single( 'SELECT GROUP_CONCAT( team ORDER BY team SEPARATOR \', \' ) AS bye_teams FROM teams t WHERE NOT EXISTS( SELECT g.id FROM games g WHERE ( g.away = t.id OR g.home = t.id ) AND g.week = ? )', $teams, $week_id );
}
