<?php
	require_once( '/home1/dcarver/data/db.php' );
	require_once( '/home1/dcarver/www/includes/classes/database.php' );
	require_once( '/home1/dcarver/www/includes/classes/functions.php' );

	$db 	= new Database( $connection );

	if ( !Weeks::Load( $db, Weeks::Current( $db ), $week ) )
	{
		return false;
	}

	$week[ 'locked' ] = 1;

	if ( !Weeks::Update( $db, $week ) )
	{
		return false;
	}
?>
