<?php

header( 'Content-type: application/json' );

require_once( 'includes/classes/JSON.php' );

$json	= new JSON();
$admin	= Functions::Post_Boolean( 'admin' );
$action	= Functions::Post( 'module' );
$token	= Functions::Get( 'token' );

$json->initialize( $admin, $action, $token );

if ( !$json->execute() )	die( $json->responseError() );
else						die( $json->responseSuccess() );
