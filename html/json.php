<?php

require_once( 'includes/classes/JSON.php' );

header( 'Content-type: application/json' );

$admin	= Functions::Post_Boolean( 'admin' );
$action	= Functions::Post( 'action' );
$token	= Functions::Post( 'token' );

try
{
	$jsonmanager = new JSONManager();
	$jsonmanager->initialize( $admin, $action, $token );
	$jsonmanager->execute();

	print( json_encode( array( 'success' => true, 'data' => $jsonmanager->action()->getData() ) ) );
	return;
}
catch ( NFLPickEmException $e )
{
	print( json_encode( array( 'success' => false, 'error_code' => '#Error#', 'error_message' => $e->getMessage() ) ) );
	return;
}
catch ( Exception $e )
{
	print( json_encode( array( 'success' => false, 'error_code' => '#Error#', 'error_message' => sprintf( 'An unknown error has occurred: %d: %s', $e->getCode(), $e->getMessage() ) ) ) );
	return;
}

