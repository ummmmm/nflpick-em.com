<?php
function Module_JSON( &$db, &$user )
{
	$token 		= Functions::Get( 'token' );
	$news_id	= Functions::Post( 'news_id' );
	$title		= Functions::Post( 'title' );
	$message	= Functions::Post( 'message' );
	$active		= Functions::Post_Active( 'active' );	
	
	$count = News::Load( $db, $news_id, $news );
	
	if ( $count === false )
	{
		return JSON_Response_Global_Error();
	}
	
	if ( $count === 0 )
	{
		return JSON_Response_Error( 'NFL-NEWS_UPDATE-0', 'Failed to load news' );
	}
	
	if ( $title === '' )
	{
		return JSON_Response_Error( 'NFL-NEWS_UPDATE-1', 'Title cannot be blank' );
	}
	
	if ( $message === '' )
	{
		return JSON_Response_Error( 'NFL-NEWS_UPDATE-2', 'Message cannot be blank' );
	}
	
	$news[ 'title' ] 	= $title;
	$news[ 'news' ]		= $message;
	$news[ 'active' ] 	= $active;
	
	if ( !News::Update( $db, $news ) )
	{
		return JSON_Response_Global_Error();
	}
	
	return JSON_Response_Success();
}
?>