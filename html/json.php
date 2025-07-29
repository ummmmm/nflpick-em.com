<?php

header( 'Content-type: application/json' );

require_once( 'includes/classes/JSON.php' );

$admin			= Functions::Post_Boolean( 'admin' );
$action			= Functions::Post( 'action' );
$token			= Functions::Post( 'token' );
$jsonmanager	= new JSONManager();

$jsonmanager->execute( $admin, $action, $token );
