<?php

function Module_JSON( &$db, &$user )
{
	$db_news 	= new News( $db );	
	$count 		= $db_news->List_Load( $news );
	
	if ( $count === false )
	{
		return JSON_Response_Global_Error();
	}
	
	return JSON_Response_Success( $news );
}
