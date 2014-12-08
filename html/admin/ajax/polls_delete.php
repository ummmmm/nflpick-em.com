<?php
function Module_JSON( &$db, &$user )
{
	$token 		= Functions::Get( 'token' );
	$poll_id	= Functions::Post( 'poll_id' );
	
	if ( !Sessions::Validate( $db, $user->id, $token ) || !$user->account[ 'admin' ] )
	{
		return JSON_Response_Error( 'NFL-POLLS_DELETE-0', 'You do not have a valid token to complete this action.' );
	}
	
	if ( !Polls::Delete( $db, $poll_id ) )
	{
		return JSON_Response_Error();
	}
	
	return JSON_Response_Success();
}
?>