<?php

require_once( 'includes/classes/JSON.php' );
require_once( 'includes/classes/Exceptions.php' );

header( 'Content-type: application/json' );

try
{
	$jsonmanager = new JSONManager();
	$jsonmanager->initialize();
	$jsonmanager->execute();

	print( json_encode( array( 'success' => true, 'data' => $jsonmanager->action()->getData() ) ) );
	return;
}
catch ( NFLPickEmException $e )
{
	print( json_encode( array( 'success' => false, 'error_code' => '#Error#', 'error_message' => $e->getMessage() ) ) );
	return;
}
catch ( JSONException $e )
{
	print( json_encode( array( 'success' => false, 'error_code' => '#Error#', 'error_message' => sprintf( 'Invalid JSON: %s (%d)', $e->getMessage(), $e->getLine() ) ) ) );
}
catch ( Exception $e )
{
	print( json_encode( array( 'success' => false, 'error_code' => '#Error#', 'error_message' => sprintf( 'An unknown error has occurred: %s (%d)', $e->getMessage(), $e->getCode() ) ) ) );
	return;
}

