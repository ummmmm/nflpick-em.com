<?php
function Module_JSON( &$db, &$user )
{
	$token = Functions::Get( 'token' );
	
	$count = News::List_Load( $db, $news );
	
	if ( $count === false )
	{
		return JSON_Response_Global_Error();
	}
	
	return JSON_Response_Success( $news );
}
?>