<?php

function Module_JSON( &$db, &$user )
{
	$news		= new News( $db );
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

	$insert = array( 'title' => $title, 'news' => $message, 'active' => $active, 'user_id' => $user->id );
	
	if ( !$news->Insert( $insert ) )
	{
		return JSON_Response_Global_Error();
	}
	
	return JSON_Response_Success();
}
