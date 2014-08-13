<?php
function Module_JSON( &$db, &$user )
{
	$token	= Functions::Post( 'token' );
	
	if ( !Sessions::Validate( $db, $user->id, $token ) )
	{
		return JSON_Response_Error( 'NFL-EMAILPREFERENCES-0', 'Action cannot be completed. You do not have a valid session.' );
	}
	
	$loaded_user = $user->account;
	
	$loaded_user[ 'email_preference' ] = ( $loaded_user[ 'email_preference' ] ) ? 0 : 1;
	
	if ( !$user->Update( $loaded_user ) )
	{
		return JSON_Response_Error( 'NFL-EMAILPREFERENCES-1', 'Failed updating preference.' );
	}
	
	$value = ( $loaded_user[ 'email_preference' ] ) ? 'Disable Email Notifications' : 'Enable Email Notifications';
	
	return JSON_Response_Success( $value );
}
?>
