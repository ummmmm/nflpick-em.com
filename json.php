<?php
header( 'Content-type: application/json' );

$document_root = $_SERVER[ 'DOCUMENT_ROOT' ];

require_once( '/home4/dcarver/data/db.php' );
require_once( $document_root . '/includes/classes/functions.php' );
require_once( $document_root . '/includes/classes/database.php' );
require_once( $document_root . '/includes/classes/validation.php' );
require_once( $document_root . '/includes/classes/user.php' );
require_once( $document_root . '/includes/classes/settings.php' );
require_once( $document_root . '/includes/classes/mail.php' );

$db		= new Database( $connection );
$user 	= new User( $db );
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
	
	$module_path = sprintf( '%s/admin/ajax/%s.php', $document_root, $module );
} else {
	$module_path = sprintf( '%s/includes/ajax/%s.php', $document_root, $module );
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

call_user_func( 'Module_JSON', &$db, &$user );

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