<?php
require_once( 'Database.php' );
require_once( 'functions.php' );

class Setup
{
	private $_db_manager;
	private $_error;

	public function __construct( DatabaseManager $db_manager )
	{
		$this->_db_manager = $db_manager;
	}

	public function Install()
	{
		if ( $this->Configured() )
		{
			return false;
		}

		if ( !$this->_Create_Tables() )
		{
			return false;
		}

		return true;
	}

	public function Uninstall( $email, $password )
	{
		$db_users = $this->_db_manager->users();

		if ( !$db_users->validateLogin( $email, $password, $user ) )
		{
			return $this->_Set_Error( 'Invalid email / password' );
		}
		else if ( $user[ 'admin' ] != 1 )
		{
			return $this->_Set_Error( 'You must be an admin to uninstall the site' );
		}
		else if ( !$this->_Drop_Tables() )
		{
			return $this->_Set_Error( 'Failed to drop database' );
		}

		return true;
	}

	private function _Drop_Tables()
	{
		if ( $this->_Get_Tables( $tables ) === false )
		{
			return false;
		}

		foreach ( $tables as $table )
		{
			if ( $this->_db_manager->connection()->query( sprintf( 'DROP TABLE %s', array_values( $table )[ 0 ] ) ) === false )
			{
				return $this->_Set_Error( sprintf( 'Failed droping table %s', $table ) );
			}
		}

		return true;
	}

	private function _Get_Tables( &$tables )
	{
		$count = $this->_db_manager->connection()->select( 'SHOW TABLES', $tables );

		if ( $count === false )
		{
			return $this->_Set_Error( 'Failed to load tables' );
		}

		return $count;
	}

	public function Configured()
	{
		$count = $this->_Get_Tables( $null );

		if ( $count === false )
		{
			return true;
		}

		if ( $count > 0 )
		{
			$this->_Set_Error( 'NFL Pick-Em site has already been configured' );
			return true;
		}

		return false;
	}

	private function _Create_Tables()
	{
		foreach ( $this->_db_manager->dynamic_tables() as $name => $func )
		{
			if ( !$func()->Create() )
			{
				return $this->_Set_Error( $func()->getError() );
			}
		}

		return true;
	}

	private function _Set_Error( $error )
	{
		$this->_error = $error;
		return false;
	}

	public function Get_Error()
	{
		return $this->_error;
	}
}
