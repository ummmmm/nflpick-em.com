<?php
function Module_JSON( &$db, &$user )
{
	$token		= Functions::Get( 'token' );
	$user_id	= Functions::Post_Int( 'user_id' );
	
	$count = $user->Load( $user_id, $loaded_user );
	
	if ( $count === false )
	{
		return JSON_Response_Error();
	}
	
	if ( $count === 0 )
	{
		return JSON_Response_Error( 'NFL-USERS_LOGIN-0', 'Could not load user' );
	}
	
	if ( !Sessions::Delete_Cookie( $db, Functions::Cookie( 'session' ) ) )
	{
		return JSON_Response_Error();
	}
	
	$user->id = $loaded_user[ 'id' ];
	$user->CreateSession();
	
	return JSON_Response_Success();
}
?>
