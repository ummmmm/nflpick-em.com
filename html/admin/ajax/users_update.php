<?php
function Module_JSON( &$db, &$user )
{
	$token 		= Functions::Get( 'token' );
	$user_id	= Functions::Post( 'user_id' );

	if ( !Sessions::Validate( $db, $user->id, $token ) || !$user->account[ 'admin' ] )
	{
		return JSON_Response_Error( 'NFL-USERS_UPDATE-0', 'You do not have a valid token to complete this action.' );
	}
	
	$count = $user->Load( $user_id, $loaded_user );
	
	if ( $count === false )
	{
		return JSON_Response_Global_Error();
	}
	
	if ( $count === 0 )
	{
		return JSON_Response_Error( 'NFL-USERS_UPDATE-1', 'Failed to load user' );
	}
	
	$loaded_user[ 'paid' ] = ( $loaded_user[ 'paid' ] === 1 ) ? 0 : 1;
	
	if ( !$user->Update( $loaded_user ) )
	{
		return JSON_Response_Global_Error();
	}
	
	return JSON_Response_Success();
}
?>
