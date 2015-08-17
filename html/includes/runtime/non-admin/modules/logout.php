<?php
function Module_Content( &$db, &$user )
{
	if ( $user->logged_in )
	{
		$db_sessions = new Sessions( $db );
		$db_sessions->Delete( $user->token );
		setcookie( 'session', null, -1, INDEX );
	}

	header( sprintf( 'location: %s', INDEX ) );

	return true;
}
?>
