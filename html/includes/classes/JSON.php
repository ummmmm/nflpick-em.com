<?php

require_once( 'Database.php' );
require_once( 'Authentication.php' );

interface iJSON
{
	function execute();
	function requirements();
}

class JSON
{
	private $_db;
	private $_error;
	private $_auth;
	private $_action;
	private $_misconfigured;
	private $_data;
	private $_requirement_flags;

	public function __construct()
	{
		$this->_db 					= new Database();
		$this->_auth				= new Authentication();
		$this->_misconfigured		= false;
		$this->_action				= null;
		$this->_error				= array();
		$this->_requirement_flags	= array( 'user' => 0x1, 'admin' => 0x2, 'token' => 0x4 );
	}

	public function initialize( $admin, $action, $token )
	{
		$this->_misconfigured = !$this->_configure( $admin, $action, $token );
	}

	public function execute()
	{
		if ( $this->_misconfigured )
		{
			return false;
		}

		if ( !$this->_action->execute() )
		{
			return false;
		}

		return true;
	}

	private function _configure( $admin, $action, $token )
	{
		$class 			= sprintf( 'JSON_%s', $action );
		$file_path 		= $this->_filePath( $admin, $action );
		$requirements	= array();

		if ( !file_exists( $file_path ) )
		{
			return $this->_setError( array( '#Error#', 'Action not found' ) );
		}
		else if ( !require_once( $file_path ) )
		{
			return $this->_setError( array( '#Error#', 'Failed to load action' ) );
		}

		if ( !class_exists( $class ) )
		{
			return $this->_setError( array( '#Error#', 'Action is misconfigured' ) );
		}

		$this->_action = new $class( $this->_db, $this->_auth, $this );

		if ( !$this->_action instanceof iJSON )
		{
			return $this->_setError( array( '#Error#', 'Action is missing required interface' ) );
		}

		$this->_getRequirements( $flags );

		if ( ( $flags & $this->_requirement_flags[ 'admin' ] ) && !$this->_auth->isAdmin() )
		{
			return $this->_setError( array( '#Error#', 'You must be an administrator to complete this action' ) );
		}

		if ( ( $flags & $this->_requirement_flags[ 'user' ] ) && !$this->_auth->isUser() )
		{
			return $this->_setError( array( '#Error#', 'You must be logged in to complete this action' ) );
		}

		if ( ( $flags & $this->_requirement_flags[ 'token' ] ) && !$this->_auth->isValidToken( $token ) )
		{
			return $this->_setError( array( '#Error#', 'You must have a valid token to complete this action' ) );
		}

		return true;
	}

	private function _getRequirements( &$flags )
	{
		$requirements 	= $this->_action->requirements();
		$flags			= 0x0;
		$flags			|= array_key_exists( 'user', 	$requirements ) && $requirements[ 'user' ] 	? $this->_requirement_flags[ 'user' ] 	: 0x0;
		$flags			|= array_key_exists( 'admin', 	$requirements ) && $requirements[ 'admin' ] ? $this->_requirement_flags[ 'admin' ] 	: 0x0;
		$flags			|= array_key_exists( 'token', 	$requirements ) && $requirements[ 'token' ] ? $this->_requirement_flags[ 'token' ] 	: 0x0;
	}

	public function responseError()
	{
		$error = $this->_getError();

		if ( is_array( $error ) )
		{
			@list( $code, $message ) = $error;
		}
		else
		{
			$code 		= "UNKNOWN";
			$message	= $error;
		}

		return json_encode( array( 'success' => false, 'error_code' => $code, 'error_message' => $message ) );
	}

	public function responseSuccess()
	{
		return json_encode( array( 'success' => true, 'data' => $this->_getData() ) );
	}


	private function _filePath( $admin, $action )
	{
		if ( $admin )	$path = 'includes/runtime/admin/JSON';
		else			$path = 'includes/runtime/non-admin/JSON';

		return Functions::Strip_Nulls( sprintf( '%s/%s.php', $path, $action ) );
	}

	private function _getData()
	{
		return $this->_data;
	}

	private function _getError()
	{
		return $this->_error;
	}

	private function _setError( $error )
	{
		$this->_error = $error;

		return false;
	}

	public function DB_Error()
	{
		$this->_error = $this->_db->Get_Error();

		return false;
	}

	public function setData( $data )
	{
		$this->_data = $data;

		return true;
	}

	public function setError( $error )
	{
		$this->_error = $error;

		return false;
	}
}
