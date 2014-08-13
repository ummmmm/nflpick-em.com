<?php
function Module_JSON( &$db, &$user )
{
	$token	= Functions::Get( 'token' );
	
	if ( !Sessions::Validate( $db, $user->id, $token ) || !$user->account[ 'admin' ] )
	{
		return JSON_Response_Error( 'NFL-WEEKS_LOAD-0', 'You do not have a valid token to complete this action.' );
	}
	
	$count 	= Weeks::List_Load( &$db, &$weeks );
	
	if ( $count === false )
	{
		return JSON_Response_Global_Error();
	}
	
	foreach( $weeks as &$week )
	{
		$week[ 'formatted_date' ] = Functions::FormatDate( $week[ 'date' ] );
	}
	
	return JSON_Response_Success( $weeks );
}
?>