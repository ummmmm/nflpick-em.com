<?php
function Module_JSON( &$db, &$user )
{
	$week_id 	= Functions::Post( 'week_id' );
	$token		= Functions::Get( 'token' );

	if ( !Sessions::Validate( $db, $user->id, $token ) )
	{
		return JSON_Response_Error( 'NFL-WEEKS_UPDATE-0', 'You do not have a valid token to complete this action.' );
	}

	$count = Weeks::Load( $db, $week_id, $week );

	if ( $count === false )
	{
		return JSON_Response_Global_Error();
	}

	if ( $count === 0 )
	{
		return JSON_Response_Error( '#Error', 'Failed to load week' );
	}

	$week[ 'locked' ] = ( $week[ 'locked' ] === 1 ) ? 0 : 1;

	if ( !Weeks::Update( $db, $week ) )
	{
		return JSON_Response_Global_Error();
	}

	return JSON_Response_Success();
}
?>
