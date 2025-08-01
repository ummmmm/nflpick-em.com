<?php

require_once( "includes/config.php" );
include_once( "includes/classes/functions.php" );

class DatabaseManager
{
	private $_error;
	private $_connection;
	private $_methods;

	public function __construct()
	{
		$this->_error		= array();
		$this->_methods		= array();
		$this->_connection	= new DatabaseConnection();
	}

	public function __destruct()
	{
		$this->_connection->disconnect();
	}

	public function initialize()
	{
		$db_settings = array();

		if ( !defined( "CONFIG_INI" ) || !Functions::Get_Config_Section( CONFIG_INI, "database", $db_settings ) )
		{
			return $this->_setError( array( '#Error#', 'Failed to load configuration settings' ) );
		}

		if ( !$this->_connection->connect( $db_settings[ 'host' ], $db_settings[ 'username' ], $db_settings[ 'password' ], $db_settings[ 'schema' ] ) )
		{
			return $this->_setError( $this->_connection->Get_Error() );
		}

		if ( !$this->_load_tables() )
		{
			return false;
		}

		return true;
	}

	public function connection()
	{
		return $this->_connection;
	}

	public function query( $query )
	{
		$bind_parmas = array_slice( func_get_args(), 1 );
		return $this->connection()->query( $query, ...$bind_parmas );
	}

	public function select( $query, &$results )
	{
		$bind_parmas = array_slice( func_get_args(), 2 );
		return $this->connection()->select( $query, $results, ...$bind_parmas );
	}

	public function single( $query, &$result )
	{
		$bind_parmas = array_slice( func_get_args(), 2 );
		return $this->connection()->single( $query, $result, ...$bind_parmas );
	}

	public function insertID()
	{
		return $this->connection()->insertID();
	}

	private function _setError( $error )
	{
		$this->_error = $error;

		return false;
	}

	public function Get_Error()
	{
		return $this->_error;
	}

	public function dynamic_tables()
	{
		return $this->_methods;
	}

	private function _load_tables()
	{
		try
		{
			$loaded_classes = array();

			foreach ( glob( 'includes/db/*.php' ) as $file )
			{
				$before_classes = get_declared_classes();
				require_once( $file );
				$after_classes	= get_declared_classes();
				$new_classes	= array_diff( $after_classes, $before_classes );

				foreach ( $new_classes as $new_class )
				{
					$reflection = new ReflectionClass( $new_class );

					if ( $reflection->isInstantiable() && $reflection->isSubclassOf( DatabaseTable::class ) )
					{						
						$name		= strtolower( str_replace( 'DatabaseTable', '', $reflection->getShortName() ) );
						$instance	= new $new_class( $this );

						$this->_methods[ $name ] = function () use ( $instance )
						{
							return $instance;
						};
					}
				}
			}
		}
		catch ( Exception $e )
		{
			return $this->_setError( $e );
		}

		return true;
	}

	public function __call( $name, $arguments )
	{
		if ( isset( $this->_methods[ $name ] ) )
		{
			return call_user_func_array( $this->_methods[ $name ], $arguments );
		}

		throw new BadMethodCallException( "Method \"{$name}\" does not exist" );
	}
}

class DatabaseConnection
{
	private $_mysqli;

	public function __construct()
	{
		mysqli_report( MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT ); // Force all errors to throw exceptions

		$this->_mysqli = null;
	}

	public function __destruct()
	{
		$this->disconnect();
	}

	public function connect( $host, $user, $password, $schema )
	{
		$this->_mysqli = new mysqli( $host, $user, $password, $schema );

		return true;
	}

	public function disconnect()
	{
		if ( $this->_mysqli )
		{
			$this->_mysqli->close();
			$this->_mysqli = null;
		}
	}

	public function query( $query, ...$params )
	{
		$ret = $this->_mysqli->execute_query( $query, $params );

		if ( $ret !== true )
		{
			throw new Exception( 'The "query" method does not support result sets' );
		}

		return true;
	}

	public function select( $query, &$results, ...$params )
	{
		$ret		= $this->_mysqli->execute_query( $query, $params );
		$results	= $ret->fetch_all( MYSQLI_ASSOC );

		return $ret->num_rows;
	}

	public function single( $query, &$result, ...$params )
	{
		$ret	= $this->_mysqli->execute_query( $query, $params );
		$result	= $ret->fetch_assoc();

		return $ret->num_rows;
	}

	public function insertID()
	{
		return $this->_mysqli->insert_id;
	}
}

abstract class DatabaseTable
{
	protected $_db_manager;

	public function __construct( DatabaseManager &$db_manager )
	{
		$this->_db_manager = $db_manager;
	}

	abstract public function Create();

	public function getError()
	{
		return $this->db()->connection()->Get_Error();
	}

	public function db()
	{
		return $this->_db_manager;
	}

	public function query( $query )
	{
		$bind_parmas = array_slice( func_get_args(), 1 );
		return $this->db()->query( $query, ...$bind_parmas );
	}

	public function select( $query, &$results )
	{
		$bind_parmas = array_slice( func_get_args(), 2 );
		return $this->db()->select( $query, $results, ...$bind_parmas );
	}

	public function single( $query, &$result )
	{
		$bind_parmas = array_slice( func_get_args(), 2 );
		return $this->db()->single( $query, $result, ...$bind_parmas );
	}

	public function insertID()
	{
		return $this->db()->insertID();
	}
}
