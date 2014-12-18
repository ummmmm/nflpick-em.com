<?php

function Module_JSON( &$db, &$user )
{
	$db_games	= new Games( $db );
	$db_weeks	= new Weeks( $db );
	$token		= Functions::Get( 'token' );
	
	if ( !Sessions::Validate( $db, $user->id, $token ) || !$user->account[ 'admin' ] )
	{
		return JSON_Response_Error( 'NFL-GAMES_LOAD-0', 'You do not have a valid token to complete this action.' );
	}
	
	$count 	= $db_weeks->List_Load( $weeks );
	
	if ( $count === false )
	{
		return JSON_Response_Global_Error();
	}
	
	foreach( $weeks as &$week )
	{
		$count = $db_games->List_Load_Week( $week[ 'id' ], $week[ 'games' ] );
		
		foreach( $week[ 'games' ] as &$game )
		{
			$date = new DateTime();
			$date->setTimestamp( $game[ 'date' ] );
			$date->setTimezone( new DateTimeZone( 'America/Los_Angeles' ) );
			$game[ 'date' ] = $date->format( DATE_ISO8601 );
		}
		
		if ( $count === false )
		{
			return JSON_Response_Error();
		}
	}
	
	return JSON_Response_Success( $weeks );
}
