<?php
header( 'Content-type: application/json' );

require_once( 'includes/classes/functions.php' );
require_once( 'includes/classes/database.php' );
require_once( 'includes/classes/validation.php' );
require_once( 'includes/classes/user.php' );
require_once( 'includes/classes/settings.php' );
require_once( 'includes/classes/mail.php' );
require_once( 'includes/classes/Authentication.php' );

$db		= new Database();
$user 	= new User( $db );
$auth 	= new Authentication();
$module	= Functions::Post( 'module' );
$view	= Functions::Post( 'view' );

if ( !Validation::Filename( $module ) )
{
	return	JSON_Response_Error( 'NFL-JSON-0', "'{$module}' is not a valid file parameter" );
}

if ( $view === 'admin' )
{
	if ( !$user->logged_in || $user->account[ 'admin' ] !== 1 )
	{
		return JSON_Response_Error( 'NFL-JSON-1', 'You must be an administrator to complete this action.' );
	}

	$module_path = sprintf( 'admin/ajax/%s.php', $module );
} else {
	$module_path = sprintf( 'includes/ajax/%s.php', $module );
}

if ( !file_exists( $module_path ) )
{
	return JSON_Response_Error( 'NFL-JSON-2', "Module '{$module}' could not be found.");
}

require_once( $module_path );

if ( !function_exists( 'Module_JSON' ) )
{
	return JSON_Response_Error( 'NFL-JSON-3', "Module '{$module}' does not implement Module_JSON" );
}

Module_JSON( $db, $user );

function JSON_Response_Error( $error_code = null, $error_message = null )
{
	if ( is_null( $error_code ) && is_null( $error_message ) )
	{
		global $error_code;
		global $error_message;
	}

	print json_encode( array( 'success' => 0, 'error_code' => $error_code, 'error_message' => $error_message ) );

	@error_log( $_SERVER[ 'REMOTE_ADDR' ] . ' - ' . $error_code . ': ' . $error_message );

	return false;
}

function JSON_Response_Global_Error()
{
	return JSON_Response_Error();
}

function JSON_Response_Success( $data = null )
{
	if ( is_null( $data ) )
	{
		print json_encode( array( 'success' => 1 ) );
	} else {
		print json_encode( array( 'success' => 1, 'data' => $data ) );
	}

	return true;
}
?>