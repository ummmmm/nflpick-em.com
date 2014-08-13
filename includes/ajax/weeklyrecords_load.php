<?php
function Module_JSON( &$db, &$user )
{
	$token = Functions::Get( 'token' );
	
	if ( !Sessions::Validate( $db, $user->id, $token ) )
	{
		return JSON_Response_Error( 'NFL-WEEKLYRECORDS_LOAD-0', 'Action cannot be completed. You do not have a valid session.' );
	}

	if ( Weeks( $db, $loaded_weeks ) === false ||
		 Users( $db, $loaded_users ) === false )
	{
		return JSON_Response_Error();
	}
	
	foreach( $loaded_users as &$loaded_user )
	{
		$loaded_user[ 'weeks' ] = array();
		
		foreach( $loaded_weeks as $loaded_week )
		{
			if ( Wins( 		$db, $loaded_user[ 'id' ], $loaded_week[ 'id' ], $wins )	 === false ||
				 Losses( 	$db, $loaded_user[ 'id' ], $loaded_week[ 'id' ], $losses )	 === false )
			{
				return JSON_Response_Error();
			}

			if ( $wins[ 'total' ] === 0 && $losses[ 'total' ] === 0 )
			{
				if ( !Functions::Worst_Record_Calculated( $db, $loaded_week[ 'id' ], $record ) )
				{
					return JSON_Response_Error();
				}

				$wins[ 'total' ] 	= $record[ 'wins' ];
				$losses[ 'total' ]	= $record[ 'losses' ];
			}
			
			array_push( $loaded_user[ 'weeks' ], array( 'id' => $loaded_week[ 'id' ], 'wins' => $wins[ 'total' ], 'losses' => $losses[ 'total' ] ) );
		}
	}
	
	return JSON_Response_Success( $loaded_users );
}

function Wins( &$db, $user_id, $week_id, &$record )
{
	return $db->single( 'SELECT COUNT( p.id ) AS total FROM picks p, games g WHERE p.winner_pick = g.winner AND p.user_id = ? AND p.week = ? AND p.game_id = g.id', $record, $user_id, $week_id );
}

function Losses( &$db, $user_id, $week_id, &$record )
{
	return $db->single( 'SELECT COUNT( p.id ) AS total FROM picks p, games g WHERE p.user_id = ? AND p.week = ? AND p.game_id = g.id AND ( p.winner_pick = g.loser OR p.picked = 0 )', $record, $user_id, $week_id );
}

function Users( &$db, &$users )
{
	return $db->select( 'SELECT id, CONCAT( fname, \' \', lname ) AS name, wins AS total_wins, losses AS total_losses FROM users ORDER BY fname ASC, lname ASC', $users );
}

function Weeks( &$db, &$weeks )
{
	return $db->select( 'SELECT w.id, ( SELECT COUNT( g.id ) FROM games g WHERE g.week = w.id AND g.winner <> 0 ) AS total_games FROM weeks w WHERE locked = 1 ORDER BY id ASC', $weeks );
}
?>
