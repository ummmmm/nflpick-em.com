<?php
	require_once( '/home4/dcarver/www/includes/classes/functions.php' );
	require_once( '/home4/dcarver/www/includes/classes/database.php' );

	$db = new Database();

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
