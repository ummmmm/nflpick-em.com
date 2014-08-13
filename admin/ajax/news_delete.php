<?php
function Module_JSON( &$db, &$user )
{
	$token		= Functions::Get( 'token' );
	$news_id	= Functions::Post( 'news_id' );
	
	if ( !News::Delete( $db, $news_id ) )
	{
		return JSON_Response_Global_Error();
	}
	
	return JSON_Response_Success();
}
?>