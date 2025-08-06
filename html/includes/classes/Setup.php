<?php
require_once( 'Database.php' );
require_once( 'Authentication.php' );

class Setup
{
	private $_db_manager;
	private $_auth;

	public function __construct()
	{
		$this->_db_manager	= new DatabaseManager();
		$this->_auth		= new Authentication( $this->_db_manager );
	}

	public function initialize()
	{
		$this->_db_manager->initialize();
	}

	public function db()
	{
		return $this->_db_manager;
	}

	public function auth()
	{
		return $this->_auth;
	}

	public function install()
	{
		if ( $this->_get_tables( $null ) > 0 )
		{
			throw new NFLPickEmException( 'NFL Pick-Em site has already been configured' );
		}

		$this->_create_tables();
	}

	public function uninstall()
	{
		$this->_drop_tables();
	}

	private function _drop_tables()
	{
		$this->_get_tables( $tables );

		foreach ( $tables as $table )
		{
			$this->_db_manager->query( sprintf( 'DROP TABLE %s', array_values( $table )[ 0 ] ) );
		}
	}

	private function _get_tables( &$tables )
	{
		return $this->_db_manager->select( 'SHOW TABLES', $tables );
	}

	private function _create_tables()
	{
		foreach ( $this->_db_manager->dynamic_tables() as $name => $func )
		{
			$func()->Create();
		}
	}
}
