<?php

require_once( "Database.php" );
require_once( "Authentication.php" );
require_once( "functions.php" );

abstract class JSON
{
	private $_json_manager;

	protected $_auth;

	protected $_data;
	protected $_error;

	public function __construct( JSONManager &$json_manager, Authentication &$auth )
	{
		$this->_json_manager	= $json_manager;
		$this->_auth			= $auth;
	}

	abstract protected function execute();

	public function requirements()
	{
		return array();
	}

	protected function auth()
	{
		return $this->_json_manager->auth();
	}

	protected function db()
	{
		return $this->_json_manager->db();
	}

	protected function settings()
	{
		return $this->_json_manager->settings();
	}

	protected function setError( $error )
	{
		$this->_error = $error;

		return false;
	}

	protected function setData( $data )
	{
		$this->_data = $data;

		return true;
	}

	protected function setDBError()
	{
		return $this->setError( $this->db()->Get_Error() );
	}

	public function getError()
	{
		return $this->_error;
	}

	public function getData()
	{
		return $this->_data;
	}
}

abstract class JSONUser extends JSON
{
	public function requirements()
	{
		return array( "user" => true );
	}
}

abstract class JSONUserAction extends JSON
{
	public function requirements()
	{
		return array( "user" => true, "token" => true );
	}
}

abstract class JSONAdmin extends JSON
{
	public function requirements()
	{
		return array( "admin" => true );
	}
}

abstract class JSONAdminAction extends JSON
{
	public function requirements()
	{
		return array( "admin" => true, "token" => true );
	}
}

class JSONManager
{
	const FLAG_REQ_USER 	= 0x1;
	const FLAG_REQ_ADMIN	= 0x2;
	const FLAG_REQ_TOKEN	= 0x4;

	private $_db_manager;
	private $_auth;

	private $_error;
	private $_action;
	private $_data;
	private $_settings;

	public function __construct()
	{
		$this->_db_manager 	= new DatabaseManager();
		$this->_auth		= new Authentication( $this->_db_manager );
		$this->_action		= null;
		$this->_error		= array();
		$this->_settings	= null;
	}

	public function auth()
	{
		return $this->_auth;
	}

	public function db()
	{
		return $this->_db_manager;
	}

	public function execute( $admin, $action, $token )
	{
		if ( !$this->_initialize() )
		{
			return $this->_responseError();
		}

		if ( !$this->_configure( $admin, $action, $token ) )
		{
			return $this->_responseError();
		}

		if ( !$this->_action->execute() )
		{
			$this->_setError( $this->_action->getError() );

			return $this->_responseError();
		}

		return $this->_responseSuccess();
	}

	private function _initialize()
	{
		if ( !$this->db()->initialize() )
		{
			return $this->_setError( $this->db()->Get_Error() );
		}

		$this->_auth->initialize();

		return true;
	}

	private function _configure( $admin, $action, $token )
	{
		$class 		= sprintf( 'JSON_%s', $action );
		$file_path 	= $this->_filePath( $admin, $action );

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

		$this->_action = new $class( $this, $this->_auth );

		if ( !$this->_action instanceof JSON )
		{
			return $this->_setError( array( '#Error#', 'Action is missing required inheritance' ) );
		}

		$this->_getRequirements( $flags );

		if ( ( $flags & self::FLAG_REQ_USER ) && !$this->_auth->isUser() )
		{
			return $this->_setError( array( '#Error#', 'You must be a user to complete this action' ) );
		}

		if ( ( $flags & self::FLAG_REQ_ADMIN ) && !$this->_auth->isAdmin() )
		{
			return $this->_setError( array( '#Error#', 'You must be an administrator to complete this action' ) );
		}

		if ( ( $flags & self::FLAG_REQ_TOKEN ) && !$this->_auth->isValidToken( $token ) )
		{
			return $this->_setError( array( '#Error#', 'You must have a valid token to complete this action' ) );
		}

		return true;
	}

	private function _getRequirements( &$flags )
	{
		$requirements 	= $this->_action->requirements();
		$flags			= 0x0;
		$flags			|= array_key_exists( 'user', 	$requirements ) && $requirements[ 'user' ] 	? self::FLAG_REQ_USER	: 0x0;
		$flags			|= array_key_exists( 'admin', 	$requirements ) && $requirements[ 'admin' ] ? self::FLAG_REQ_ADMIN	: 0x0;
		$flags			|= array_key_exists( 'token', 	$requirements ) && $requirements[ 'token' ] ? self::FLAG_REQ_TOKEN	: 0x0;
	}

	private function _responseError()
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

	public function settings()
	{
		if ( $this->_settings == null )
		{
			if ( !$this->db()->settings()->Load( $this->_settings ) )
			{
				throw new Exception( sprintf( 'Failed to load settings: %s', $this->db()->Get_Error() ) );
			}
		}

		return $this->_settings;
	}

	private function _responseSuccess()
	{
		return json_encode( array( 'success' => true, 'data' => $this->_action->getData() ) );
	}

	private function _filePath( $admin, $action )
	{
		if ( $admin )	$path = 'includes/runtime/admin/JSON';
		else			$path = 'includes/runtime/non-admin/JSON';

		return Functions::Strip_Nulls( sprintf( '%s/%s.php', $path, $action ) );
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
}
