<?php
function Module_Head( &$db, &$user )
{
	return true;
}

function Module_Content( &$db, &$user )
{
	print '<h1>Leaderboard</h1>';
	
	$count = Leaderboard( $db, $leaders );
	
	if ( $count === false )
	{
		return false;
	}
	
	if ( $count === 0 )
	{		
		print '<p>The leaderboard is currently empty.</p>';
		
		return true;
	}
	
	foreach ( $leaders as $leader )
	{
		print '<p>' . Functions::Place( $leader[ 'current_place' ] ) . ' ' . htmlentities( ucwords( $leader[ 'name' ] ) ) . ' - ' . " {$leader[ 'wins' ]} Wins - {$leader[ 'losses' ]} Losses</p>";
	}
	
	return true;
}

function Leaderboard( &$db, &$users )
{
	return $db->select( 'SELECT *, CONCAT( fname, \' \', lname ) AS name FROM users ORDER BY current_place, fname', $users );
}
?>
