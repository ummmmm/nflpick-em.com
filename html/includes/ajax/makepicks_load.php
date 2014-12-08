<?php
function Module_JSON( &$db, &$user )
{
	$week_id	= Functions::Post( 'week_id' );
	$token		= Functions::Post( 'token' );
	
	if ( !Sessions::Validate( $db, $user->id, $token ) )
	{
		return JSON_Response_Error( 'NFL-MAKEPICKS_LOAD-0', 'Action cannot be completed. Please verify you are logged in.' );
	}
	
	$count = Weeks::Load( $db, $week_id, $week );
	
	if ( $count === false )
	{
		return JSON_Response_Error();
	}
	
	if ( $count === 0 )
	{
		return JSON_Response_Error( 'NFL-MAKEPICKS_LOAD-1', 'Failed to load week' );
	}

	$count = Games::List_Load( $db, $week_id, $week[ 'games' ] );
	
	foreach( $week[ 'games' ] as &$game )
	{
		$count = Picks::Load_User_Game( $db, $user->id, $game[ 'id' ], $pick );
		
		if ( $count === false )
		{
			return JSON_Response_Error();
		}
		
		$now	= new DateTime();
		$date 	= new DateTime( $game[ 'date' ] );
		
		$game[ 'past' ] 		 	= ( $now > $date ) ? true : false;
		$game[ 'time_formatted' ] 	= $date->format( 'h:i a' );
		$game[ 'date_formatted' ] 	= $date->format( 'F d, Y' );
		$game[ 'date_javascript' ]	= $date->format( 'F d, Y h:i:s' );
		$game[ 'pick' ] 			= $pick;
	}
	
	return JSON_Response_Success( $week );
}
?>
