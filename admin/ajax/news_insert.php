<?php
function Module_JSON( &$db, &$user )
{
	$token 		= Functions::Get( 'token' );
	$title		= Functions::Post( 'title' );
	$message	= Functions::Post( 'message' );
	$active		= Functions::Post_Active( 'active' );	
	
	if ( $title === '' )
	{
		return JSON_Response_Error( 'NFL-NEWS_INSERT-0', 'Title cannot be blank' );
	}
	
	if ( $message === '' )
	{
		return JSON_Response_Error( 'NFL-NEWS_INSERT-1', 'Message cannot be blank' );
	}
	
	$news[ 'title' ] 	= $title;
	$news[ 'news' ]		= $message;
	$news[ 'active' ] 	= $active;
	$news[ 'user_id' ]	= $user->id;
	
	if ( !News::Insert( $db, $news ) )
	{
		return JSON_Response_Global_Error();
	}
	
	return JSON_Response_Success();
}
?>