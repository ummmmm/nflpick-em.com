<?php
function Module_JSON( &$db, &$user )
{
	$db_news	= new News( $db );
	$token 		= Functions::Get( 'token' );
	$news_id	= Functions::Post( 'news_id' );
	$title		= Functions::Post( 'title' );
	$message	= Functions::Post( 'message' );
	$active		= Functions::Post_Active( 'active' );	
	
	$count = $db_news->Load( $news_id, $news );
	
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
	
	if ( !$db_news->Update( $news ) )
	{
		return JSON_Response_Global_Error();
	}
	
	return JSON_Response_Success();
}
?>