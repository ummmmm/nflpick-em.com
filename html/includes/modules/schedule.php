<?php
function Module_Content( &$db, &$user )
{
	$week_id = Functions::Get( 'week' );

	if ( !Validation::Week( $week_id ) )
	{
		$count = Weeks::List_Load( $db, $weeks );

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

	if ( !Weeks::Load( $db, $week_id, $loaded_week ) )
	{
		return false;
	}

	$count = Games::List_Load( $db, $week_id, $games );

	if ( $count === false )
	{
		return false;
	}

	$count = Teams::Byes( $db, $week_id, $teams );

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
?>
