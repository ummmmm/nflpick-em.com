<?php

require_once( "Database.php" );
require_once( "Authentication.php" );
require_once( "Security.php" );
require_once( "Input.php" );
require_once( "functions.php" );

abstract class JSON
{
	private $_json_manager;
	private $_data;

	public function __construct( JSONManager &$json_manager )
	{
		$this->_json_manager = $json_manager;
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

	protected function input()
	{
		return $this->_json_manager->input();
	}

	protected function settings()
	{
		return $this->_json_manager->settings();
	}

	protected function setData( $data )
	{
		$this->_data = $data;

		return true;
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
	private $_db_manager;
	private $_auth;
	private $_input;

	private $_action;
	private $_settings;

	public function __construct()
	{
		$this->_db_manager 	= new DatabaseManager();
		$this->_auth		= new Authentication( $this->_db_manager );
		$this->_input		= new JSONInput();
		$this->_action		= null;
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

	public function action()
	{
		return $this->_action;
	}

	public function input()
	{
		return $this->_input;
	}

	public function initialize()
	{
		$this->db()->initialize();
		$this->auth()->initialize();

		$admin	= $this->input()->value_bool( 'admin' );
		$action = $this->input()->value_str( 'action' );
		$token	= $this->input()->value_str( 'token' );

		$class 		= sprintf( 'JSON_%s', $action );
		$file_path 	= $this->_filePath( $admin, $action );

		if ( !file_exists( $file_path ) )		throw new NFLPickEmException( 'Action not found' );
		else if ( !require_once( $file_path ) )	throw new NFLPickEmException( 'Failed to load action' );
		else if ( !class_exists( $class ) )		throw new NFLPickEmException( 'Action is misconfigured' );

		$this->_action = new $class( $this );

		if ( !$this->_action instanceof JSON )
		{
			throw new NFLPickEmException( 'Action is missing required inheritance' );
		}

		$requirements	= $this->_action->requirements();
		$require_user	= $requirements[ 'user' ] ?? false;
		$require_admin	= $requirements[ 'admin' ] ?? false;
		$require_token	= $requirements[ 'token' ] ?? false;

		if ( $require_user && !$this->auth()->isUser() )						throw new NFLPickEmException( 'You must be a user to complete this action' );
		else if ( $require_admin && !$this->auth()->isAdmin() )					throw new NFLPickEmException( 'You must be an administrator to complete this action' );
		else if ( $require_token && !$this->auth()->isValidToken( $token ) )	throw new NFLPickEmException( 'You must have a valid token to complete this action' );
	}

	public function execute()
	{
		$this->_action->execute();
	}

	public function settings()
	{
		if ( $this->_settings == null )
		{
			if ( !$this->db()->settings()->Load( $this->_settings ) )
			{
				throw new NFLPickEmException( 'Settings do not exist' );
			}
		}

		return $this->_settings;
	}

	private function _filePath( $admin, $action )
	{
		if ( $admin )	$path = 'includes/runtime/admin/JSON';
		else			$path = 'includes/runtime/non-admin/JSON';

		return Functions::Strip_Nulls( sprintf( '%s/%s.php', $path, $action ) );
	}
}
