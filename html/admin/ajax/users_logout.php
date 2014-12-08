<?php
function Module_JSON( &$db, &$user )
{
	$token		= Functions::Get( 'token' );
	$user_id	= Functions::Post( 'user_id' );

	$count = $user->Load( $user_id, $null );
	
	if ( $count === false )
	{
		return JSON_Response_Error();
	}

	if ( $count === 0 )
	{
		return JSON_Response_Error( 'NFL-USERS_LOGOUT-0', 'Unable to load user' );
	}

	if ( !Sessions::Delete_User( $db, $user_id ) )
	{
		return JSON_Response_Error();
	}
	
	return JSON_Response_Success();
}
?>
