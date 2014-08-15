<?php
function Module_JSON( &$db, &$user )
{
	$token	= Functions::Get( 'token' );
	
	if ( !Sessions::Validate( $db, $user->id, $token ) || !$user->account[ 'admin' ] )
	{
		return JSON_Response_Error( 'NFL-GAMES_LOAD-0', 'You do not have a valid token to complete this action.' );
	}
	
	$count 	= Weeks::List_Load( $db, $weeks );
	
	if ( $count === false )
	{
		return JSON_Response_Global_Error();
	}
	
	foreach( $weeks as &$week )
	{
		$count = Games::List_Load( $db, $week[ 'id' ], $week[ 'games' ] );
		
		foreach( $week[ 'games' ] as &$game )
		{
			$game[ 'date' ] = new DateTime( $game[ 'date' ] );
			$game[ 'date' ]->setTimezone( new DateTimeZone( 'America/Los_Angeles' ) );
			$game[ 'date' ] = $game[ 'date' ]->format( DATE_ISO8601 );
		}
		
		if ( $count === false )
		{
			return JSON_Response_Global_Error();
		}
	}
	
	return JSON_Response_Success( $weeks );
}
?>