<?php

header( 'Content-type: application/json' );

require_once( 'includes/classes/JSON.php' );

$admin	= Functions::Post_Boolean( 'admin' );
$action	= Functions::Post( 'action' );
$token	= Functions::Post( 'token' );
$json	= new JSON();

$json->initialize( $admin, $action, $token );

if ( !$json->execute() )	die( $json->responseError() );
else						die( $json->responseSuccess() );
