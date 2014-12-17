<?php

function Module_JSON( &$db, &$user )
{
	$db_news	= new News( $db );
	$news_id 	= Functions::Post( 'news_id' );
	
	if ( !$db_news->Delete( $news_id ) )
	{
		return JSON_Response_Error();
	}
	
	return JSON_Response_Success();
}
