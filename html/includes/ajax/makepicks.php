<?php

function Module_JSON( &$db, &$user )
{
	$db_games	= new Games( $db );
	$db_picks	= new Picks( $db );
	$db_teams	= new Teams( $db );
	$db_weeks	= new Weeks( $db );
	$date_now 	= new DateTime();
	$week 		= Functions::Post( 'week' );
	$gameid 	= Functions::Post( 'gameid' );
	$winner		= Functions::Post( 'winner' );
	$loser		= Functions::Post( 'loser' );
	$token		= Functions::Post( 'token' );
	
	if ( !Sessions::Validate( $db, $user->id, $token ) )
	{
		return JSON_Response_Error( 'NFL-MAKEPICKS-0', 'Action cannot be completed. You do not have a valid session.' );
	}
	
	$count_week = $db_weeks->Load( $week, $loaded_week );
	
	if ( $count_week === false )
	{
		return JSON_Response_Error();
	}
	
	if ( $count_week === 0 )
	{
		return JSON_Response_Error( 'NFL-MAKEPICKS-1', "Week '{$week}' could not be loaded" );
	}
	
	$date_week = new DateTime();
	$date_week->setTimestamp( $loaded_week[ 'date' ] );

	if ( $loaded_week[ 'locked' ] === 1 || $date_now > $date_week )
	{
		return JSON_Response_Error( 'NFL-MAKEPICKS-2', 'This week has already been locked. You can no longer make picks.' );
	}
	
	if ( !$db_games->Load( $gameid, $game ) )
	{
		return JSON_Response_Error( 'NFL-MAKEPICKS-3', 'Game not found' );
	}
	
	if ( !$db_games->Exists( $gameid, $week, $winner, $loser ) )
	{
		return JSON_Response_Error( 'NFL-MAKEPICKS-4', 'Invalid game data' );
	}
	
	$date_start = new DateTime( $game[ 'date' ] );
	
	if ( $date_now > $date_start )
	{
		return JSON_Response_Error( 'NFL-MAKEPICKS-5', 'This game has already started and can no longer be updated.' );
	}
	
	if ( !$db_teams->Load( $winner, $winning_team ) || !$db_teams->Load( $loser, $losing_team ) )
	{
		return JSON_Response_Error( 'NFL-MAKEPICKS-6', 'Failed to load teams.' );
	}
	
	$count_pick = $db_picks->Load_User_Game( $user->id, $gameid, $pick );
	
	if ( $count_pick === false )
	{
		return JSON_Response_Global_Error();
	}

	$pick[ 'game_id' ]		= $gameid;
	$pick[ 'winner_pick' ] 	= $winner;
	$pick[ 'loser_pick' ]	= $loser;
	$pick[ 'picked' ]		= 1;
	
	if ( !$db_picks->Update( $pick ) )
	{
		return JSON_Response_Error();
	}
	
	$remaining = $db_picks->Remaining( $user->id, $week );
	
	return JSON_Response_Success( array( 'remaining' => $remaining, 'message' => 'You have picked the <b>' . $winning_team[ 'team' ] . '</b> to beat the <b>' . $losing_team[ 'team'] . '</b>' ) );
}
