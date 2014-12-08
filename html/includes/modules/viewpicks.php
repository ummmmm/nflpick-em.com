<?php
function Module_Content( &$db, &$user )
{
	Validation::User( $user->id );

	$weekid = Functions::Get( 'week' );

	if ( $weekid === '' )
	{
		return WeekList( $db );
	}

	if ( !Validation::Week( $weekid ) )
	{
		return Functions::Information( 'Error', 'Invalid week.' );
	}

	if ( !Weeks::IsLocked( $db, $weekid ) )
	{
		return Functions::Information( 'Error', 'Week ' . htmlentities( $weekid ) . ' is not locked yet.' );
	}

	return PickLayout( $db, $user, $weekid );
}

function PickLayout( &$db, $user, $weekid )
{
	if ( $user->List_Load( $users ) === false )
	{
		return false;
	}

	$games_count = Games::List_Load( $db, $weekid, $games );

	if ( $games_count === false )
	{
		return false;
	}

	print "<h1>Week {$weekid} User Picks</h1>";
	print '<table class="picks" style="font-size:8px;" width="100%">';

	$user_records = array();

	foreach( $users as $loaded_user )
	{
		$initials 								= strtoupper( substr( $loaded_user[ 'fname' ], 0, 1 ) . '.' . substr( $loaded_user[ 'lname' ], 0, 1 ) ) . '.';
		$user_records[ $loaded_user[ 'id' ] ] 	= array( 'losses' => 0, 'wins' => 0 );
		$missing_count 							= Picks::Missing( $db, $loaded_user[ 'id' ], $weekid );

		if ( $missing_count === false )
		{
			return false;
		}

		print '<tr>';
		print '<td class="picks">';
		print '<a href="javascript:;" title="' . htmlentities( $loaded_user[ 'name' ] ) . '" onclick="$.fn.highlightPicks( ' . $loaded_user[ 'id' ] . ', ' . $weekid . ');">' . htmlentities( $initials ) . '</a>';
		print '</td>';

		foreach( $games as $game )
		{
			if ( !Picks::Load_User_Game( $db, $loaded_user[ 'id' ], $game[ 'id' ], $pick ) )
			{
				return false;
			}

			if ( $pick[ 'picked' ] === 0 )
			{
				$team = '<span style="color:red;">N/A</span>';
			}
			else if ( $pick[ 'winner_pick' ] == $game[ 'away' ] )
			{
				$team = $game[ 'awayAbbr' ];
			} else {
				$team = $game[ 'homeAbbr' ];
			}

			if ( $pick[ 'winner_pick' ] === $game[ 'winner' ] )
			{
				$user_records[ $loaded_user[ 'id' ] ][ 'wins' ] += 1;
			} else if ( $game[ 'winner' ] != 0 ){
				$user_records[ $loaded_user[ 'id' ] ][ 'losses' ] += 1;
			}

			$output = ( $pick[ 'winner_pick' ] == $game[ 'winner' ] ) ? "<b>{$team}</b>" : $team;

			print '<td userid="' . $loaded_user[ 'id' ] . '" gameid="' . $game[ 'id' ] . '">' . $output . '</td>';
		}

		if ( $missing_count == $games_count )
		{
			if ( !Functions::Worst_Record_Calculated( $db, $weekid, $user_records[ $loaded_user[ 'id' ] ] ) )
			{
				return false;
			}
		}

		print '<td><b>' . $user_records[ $loaded_user[ 'id' ] ][ 'wins' ] . ' - ' . $user_records[ $loaded_user[ 'id' ] ][ 'losses' ] . '</b></td>';

		print '</tr>';
	}

	print '</table>';
	print '<p><a href="javascript:;" id="highlightpicks" onclick="$.fn.highlightPicks( 0, ' . $weekid . ');">Highlighting On</a></p>';

	return true;
}

function WeekList( &$db )
{
	print '<h1>Pick \'Em Weeks</h1>';
	if ( !Weeks::List_Load( $db, $weeks ) )
	{
		return false;
	}

	foreach( $weeks as $week )
	{
		$locked = $week[ 'locked' ] ? '- <b>Locked</b>' : '';
		printf( '<p><a href="?module=viewpicks&week=%d" title="Week %d">Week %d</a> %s</p>', $week[ 'id' ], $week[ 'id' ], $week[ 'id' ], $locked );
	}

	return true;
}
?>
