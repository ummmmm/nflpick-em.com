<?php
function Module_JSON( &$db, &$user )
{	
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
	
	$count_week = Weeks::Load( $db, $week, $loaded_week );
	
	if ( $count_week === false )
	{
		return JSON_Response_Error();
	}
	
	if ( $count_week === 0 )
	{
		return JSON_Response_Error( 'NFL-MAKEPICKS-1', "Week '{$week}' could not be loaded" );
	}
	
	$date_week = new DateTime( $loaded_week[ 'date' ] );

	if ( $loaded_week[ 'locked' ] === 1 || $date_now > $date_week )
	{
		return JSON_Response_Error( 'NFL-MAKEPICKS-2', 'This week has already been locked. You can no longer make picks.' );
	}
	
	if ( !Games::Load( $db, $gameid, $game ) )
	{
		return JSON_Response_Error( 'NFL-MAKEPICKS-3', 'Game not found' );
	}
	
	if ( !Games::Exists( $db, $gameid, $week, $winner, $loser ) )
	{
		return JSON_Response_Error( 'NFL-MAKEPICKS-4', 'Invalid game data' );
	}
	
	$date_start = new DateTime( $game[ 'date' ] );
	
	if ( $date_now > $date_start )
	{
		return JSON_Response_Error( 'NFL-MAKEPICKS-5', 'This game has already started and can no longer be updated.' );
	}
	
	if ( !Teams::Load( $db, $winner, $winning_team ) || !Teams::Load( $db, $loser, $losing_team ) )
	{
		return JSON_Response_Error( 'NFL-MAKEPICKS-6', 'Failed to load teams.' );
	}
	
	$count_pick = Picks::Load_User_Game( $db, $user->id, $gameid, $pick );
	
	if ( $count_pick === false )
	{
		return JSON_Response_Global_Error();
	}

	$pick[ 'game_id' ]		= $gameid;
	$pick[ 'winner_pick' ] 	= $winner;
	$pick[ 'loser_pick' ]	= $loser;
	$pick[ 'picked' ]		= 1;
	
	if ( !Picks::Update( $db, $pick ) )
	{
		return JSON_Response_Error();
	}
	
	$remaining = Picks::Remaining( $db, $user->id, $week );
	
	return JSON_Response_Success( array( 'remaining' => $remaining, 'message' => 'You have picked the <b>' . $winning_team[ 'team' ] . '</b> to beat the <b>' . $losing_team[ 'team'] . '</b>' ) );
}
?>
