<?php
function Module_JSON( &$db, &$user )
{
	$db_weeks	= new Weeks( $db );
	$week_id 	= Functions::Post( 'week_id' );
	$token		= Functions::Get( 'token' );

	if ( !Sessions::Validate( $db, $user->id, $token ) )
	{
		return JSON_Response_Error( 'NFL-WEEKS_UPDATE-0', 'You do not have a valid token to complete this action.' );
	}

	$count = $db_weeks->Load( $week_id, $week );

	if ( $count === false )
	{
		return JSON_Response_Error();
	}

	if ( $count === 0 )
	{
		return JSON_Response_Error( '#Error#', 'Failed to load week' );
	}

	$week[ 'locked' ] = ( $week[ 'locked' ] === 1 ) ? 0 : 1;

	if ( !$db_weeks->Update( $week ) )
	{
		return JSON_Response_Error();
	}

	return JSON_Response_Success();
}
