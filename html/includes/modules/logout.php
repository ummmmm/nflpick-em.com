<?php
function Module_Content( &$db, &$user )
{
	if ( $user->logged_in )
	{
		Sessions::Delete( $db, $user->token );
		setcookie( 'session', null, -1, INDEX );
	}

	header( sprintf( 'location: %s', INDEX ) );

	return true;
}
?>
