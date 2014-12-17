<?php
function Module_JSON( &$db, &$user )
{
	$db_games	= new Games( $db );
	$game_id	= Functions::Post_Int( 'game_id' );
	$scored 	= Functions::Post_Boolean( 'scored' );
	$token 		= Functions::Post( 'token' );
	
	if ( !Sessions::Validate_Admin( $db, $user->id, $token ) )
	{
		return JSON_Response_Error( 'NFL-GAMES_UPDATE-5', 'You do not have a valid token to complete this action' );
	}
	
	if ( !$db_games->Load( $game_id, $loaded_game ) )
	{
		return JSON_Response_Error( 'NFL-GAMES_UPDATE-6', 'Failed to load game' );
	}
	
	if ( $scored === true )
	{
		return Update_Scores( $db, $user, $loaded_game );
	}
	
	return Update_Games( $db, $user, $loaded_game );	
}

function Update_Scores( &$db, &$user, &$game )
{
	$db_games	= new Games( $db );
	$awayScore	= Functions::Post_Int( 'awayScore' );
	$homeScore	= Functions::Post_Int( 'homeScore' );
	
	if ( $awayScore === $homeScore || $awayScore < 0 || $homeScore < 0 )
	{
		return JSON_Response_Error( 'NFL-GAMES_UPDATE-0', 'Invalid game score' );
	}
	
	$game[ 'winner' ]		= $homeScore > $awayScore ? $game[ 'home' ] : $game[ 'away' ];
	$game[ 'loser' ]		= $homeScore > $awayScore ? $game[ 'away' ] : $game[ 'home' ];
	$game[ 'homeScore' ]	= $homeScore;
	$game[ 'awayScore' ] 	= $awayScore;
	
	if ( !$db_games->Update( $game ) )
	{
		return JSON_Response_Error();
	}
	
	if ( !Teams_Update_Record( $db, $game[ 'home' ] ) )
	{
		return JSON_Response_Error();
	}
	
	if ( !Teams_Update_Record( $db, $game[ 'away' ] ) )
	{
		return JSON_Response_Error();
	}
	
	if ( !Users_Update_Record( $db ) )
	{
		return JSON_Response_Error();
	}
	
	if ( !$db_games->Load( $game[ 'id' ], $game ) )
	{
		return JSON_Response_Error();
	}
	
	/*if ( !Missed_Week_Record_Update( $db, $game[ 'week' ] ) )
	{
		return JSON_Response_Error();
	}*/
	
	if ( !Users_Place_Update( $db, $user ) )
	{
		return JSON_Response_Error();
	}
	
	return JSON_Response_Success( $game );
}

function Update_Games( &$db, &$user, &$game )
{
	$db_games	= new Games( $db );
	$db_teams	= new Teams( $db );
	$db_weeks	= new Weeks( $db );
	$week_id	= Functions::Post_Int( 'week' );
	$away_id	= Functions::Post_Int( 'away' );
	$home_id	= Functions::Post_Int( 'home' );
	$month		= Functions::Post_Int( 'month' );
	$day		= Functions::Post_Int( 'day' );
	$year		= Functions::Post_Int( 'year' );
	$hour		= Functions::Post_Int( 'hour' );
	$minute		= Functions::Post_Int( 'minute' );
	
	$mktime = mktime( $hour, $minute, 0, $month, $day, $year );
	
	if ( !checkdate( $month, $day, $year ) || $mktime === false || $hour > 23 || $minute > 59 )
	{
		return JSON_Response_Error( 'NFL-GAMES_UPDATE-1', 'Invalid game date' );
	}
	
	$team_count = $db_teams->Load( $away_id, $loaded_away );
	
	if ( $team_count === false )
	{
		return JSON_Response_Error();
	}
	
	if ( $team_count === 0 )
	{
		return JSON_Response_Error( 'NFL-GAMES_UPDATE-2', 'Failed to load the away team' );
	}
	
	$team_count = $db_teams->Load( $home_id, $loaded_home );
	
	if ( $team_count === false )
	{
		return JSON_Response_Error();
	}
	
	if ( $team_count === 0 )
	{
		return JSON_Response_Error( 'NFL-GAMES_UPDATE-3', 'Failed to load the home team' );
	}
	
	$week_count = $db_weeks->Load( $week_id, $loaded_week );
	
	if ( $week_count === false )
	{
		return JSON_Response_Error();
	}
	
	if ( $week_count === 0 )
	{
		return JSON_Response_Error( 'NFL-GAMES_UPDATE-4', 'Failed to load week' ); 
	}
	
	$date = new DateTime( date( 'Y-m-d H:i:s', $mktime ) );
	
	$game[ 'week' ] = $week_id;
	$game[ 'away' ] = $away_id;
	$game[ 'home' ] = $home_id;
	$game[ 'date' ] = $date->format( DATE_ISO8601 );
	
	if ( !$db_games->Update( $game ) )
	{
		return JSON_Response_Error();
	}
	
	if ( !$db_games->Load( $game[ 'id' ], $game ) )
	{
		return JSON_Response_Error();
	}
	
	return JSON_Response_Success( $game );
}

function Teams_Update_Record( &$db, $teamid )
{
	return $db->query( 'UPDATE
							teams t
						SET
							t.wins 		= ( SELECT COUNT( g.id ) FROM games g WHERE g.winner 	= t.id ),
							t.losses	= ( SELECT COUNT( g.id ) FROM games g WHERE g.loser		= t.id )
						WHERE
							t.id 		= ?', $teamid );
}

function Users_Update_Record( &$db )
{
	return $db->query( 'UPDATE
							users u
						SET
							u.wins		= ( SELECT COUNT( p.id ) FROM picks p, games g WHERE p.game_id = g.id AND p.winner_pick = g.winner AND p.user_id = u.id AND g.winner <> 0 ),
							u.losses	= ( SELECT COUNT( g.id ) FROM picks p, games g WHERE p.game_id = g.id AND p.user_id = u.id AND ( p.winner_pick = 0 AND g.winner <> 0 OR ( p.winner_pick = g.loser AND g.loser <> 0 ) ) )' );
}

function Users_Place_Update( &$db, &$user )
{
	$count = $user->List_Load( $users );
	
	if ( $count === false )
	{
		return false;
	}
	
	foreach( $users as $loaded_user )
	{
		if ( !$db->single( 'SELECT COUNT( id ) + 1 AS place FROM users WHERE wins > ?', $current, $loaded_user[ 'wins' ] ) )
		{
			return false;
		}
		
		if ( !$db->query( 'UPDATE users SET current_place = ? WHERE id = ?', $current[ 'place' ], $loaded_user[ 'id' ] ) )
		{
			return false;
		}
	}
	
	return true;
}

function MissedPicksList_Load( &$db, &$users )
{
	return $db->select( 'SELECT u.id AS user_id, w.id AS week_id FROM users u, weeks w WHERE u.id NOT IN ( SELECT p.user_id FROM picks p WHERE p.week = w.id ) AND w.locked = 1', $users );
}

function Missed_Week_Record_Update( &$db, $week_id )
{
	$count = $db->single( 'SELECT IF( ( SELECT COUNT( id ) AS played_games FROM games WHERE winner = 0 AND week = ? ) = 0, 1, 0 ) AS all_played', $played_games, $week_id );
	
	if ( $count === false )
	{
		return false;
	}
	
	if ( $played_games[ 'all_played' ] === 0 )
	{
		return true;
	}
	
	$count = MissedPicksList_Load( $db, $loaded_users );
	
	if ( $count === false )
	{
		return false;
	}
	
	foreach( $loaded_users as $loaded_user )
	{
		$week_id = $loaded_user[ 'week_id' ];
		$user_id = $loaded_user[ 'user_id' ];
		$count	 = Functions::Worst_Record_Calculated( $db, $week_id, $record );
		
		if ( $count === false )
		{
			return false;
		}
		
		$total_games 	= $record[ 'total' ];
		$wins			= $record[ 'wins' ];
		$losses			= $record[ 'losses' ];
		
		if ( !$db->query( 'UPDATE users SET wins = ( wins + ? ), losses = ( ( losses - ? ) + ? ) WHERE id = ?', $wins, $total_games, $losses, $user_id ) )
		{
			return false;
		}
		
		return true;
	}
}
?>
