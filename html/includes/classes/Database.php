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
	private $_connected;
	private $_mysqli;
	private $_error;

	public function __construct()
	{
		$this->_error		= array();
		$this->_connected	= false;
	}

	public function __destruct()
	{
		$this->disconnect();
	}

	public function connect( $host, $user, $password, $schema )
	{
		try
		{
			$this->_mysqli = new mysqli( $host, $user, $password, $schema );
		}
		catch ( Exception $e )
		{
			return $this->_setError( 'Failed to connect to the database' );
		}

		$this->_connected = true;

		return true;
	}

	public function disconnect()
	{
		if ( $this->_connected )
		{
			$this->_connected = false;
			$this->_mysqli->close();
		}
	}

	public function query( $query )
	{
		$bind_parmas = array_slice( func_get_args(), 1 );

		return $this->_Run_Statement( $query, $bind_parmas );
	}

	public function select( $query, &$results )
	{
		$results_count		= 0;
		$results 			= array();
		$bind_parmas		= array_slice( func_get_args(), 2 );
		$multiple_results	= true;

		if ( !$this->_Run_Statement( $query, $bind_parmas, $multiple_results, $results, $results_count ) )
		{
			return false;
		}

		return $results_count;
	}

	public function single( $query, &$result )
	{
		$result_count		= 0;
		$result 			= array();
		$bind_parmas		= array_slice( func_get_args(), 2 );
		$multiple_results	= false;

		if ( !$this->_Run_Statement( $query, $bind_parmas, $multiple_results, $result, $result_count ) )
		{
			return false;
		}

		return $result_count;
	}

	public function insert( $table, &$values )
	{
		if ( !is_array( $values ) || count( $values ) === 0 )
		{
			return false;
		}

		$columns	= '';
		$prepared	= '';
		$args		= array();

		foreach ( $values as $key => $null )
		{
			$columns 	.= $columns == '' ? $key : sprintf( ', %s', $key );
			$prepared	.= $prepared == '' ? '?' : ', ?';
		}

		array_push( $args, sprintf( 'INSERT INTO %s ( %s ) VALUES ( %s )', $table, $columns, $prepared ) );

		return call_user_func_array( array( $this, 'query' ), array_values( array_merge( $args, $values ) ) );
	}

	public function insertID()
	{
		return $this->_mysqli->insert_id;
	}

	private function _Run_Statement( &$query, $params, $multiple_results = false, &$results = null, &$result_count = 0 )
	{
		$result_count 	= 0;
		$bind_count 	= count( $params );
		$stmt 			= $this->_mysqli->stmt_init();

		try
		{
			$stmt->prepare( $query );
		}
		catch ( Exception $e )
		{
			return $this->_setError( array( 'NFL-DATABASE-0', $this->_mysqli->error ) );
		}

		if ( $bind_count )
		{
			if ( $stmt->param_count != $bind_count )
			{
				return $this->_setError( array( '#Error#', sprintf( 'Parameter count mismatch, expected %d parameters, found %d', $bind_count, $stmt->param_count ) ) );
			}

			$bind_params = array( '' ); // initialize the empty array

			for ( $i = 0; $i < $bind_count; $i++ )
			{
				switch( gettype( $params[ $i ] ) )
				{
					case 'integer'	:
						$bind_params[ 0 ] .= 'i';
						break;

					case 'double'	:
						$bind_params[ 0 ] .= 'd';
						break;

					case 'string'	:
						$bind_params[ 0 ] .= 's';
						break;

					default			:
						$bind_params[ 0 ] .= 'b';
						break;
				}

				$bind_params[ $i + 1 ] = &$params[ $i ];
			}

			if ( !call_user_func_array( array( $stmt, 'bind_param' ), $bind_params ) )
			{
				return $this->_setError( array( 'NFL-DATABASE-1', $this->_mysqli->error ) );
			}
		}

		if ( !$stmt->execute() )
		{
			return $this->_setError( array( 'NFL-DATABASE-2', $this->_mysqli->error ) );
		}

		if ( is_null( $results ) ) // Must be an INSERT, UPDATE, DELETE
		{
			if ( !$stmt->close() )
			{
				return $this->_setError( array( 'NFL-DATABASE-8', $this->_mysqli->error ) );
			}

			return true;
		}

		// Otherwise we have a result set

		if ( !$stmt->store_result() )
		{
			$stmt->close();

			return $this->_setError( array( 'NFL-DATABASE-3', $this->_mysqli->error ) );
		}

		if ( ( $result_count = $stmt->num_rows ) == 0 )
		{
			$stmt->free_result();

			if ( !$stmt->close() )
			{
				return $this->_setError( array( 'NFL-DATABASE-4', $this->_mysqli->error ) );
			}

			return true;
		}

		if ( ( $meta = $stmt->result_metadata() ) === false )
		{
			$stmt->free_result();
			$stmt->close();

			return $this->_setError( array( 'NFL-DATABASE-5', $this->_mysqli->error ) );
		}

		$fields = array();

		while( ( $field = $meta->fetch_field() ) !== false )
		{
			$colunm_name			= $field->name;
			${$colunm_name}			= null; // create a dynamic PHP variable set to the column name
			$fields[ $colunm_name ] = &${$colunm_name}; // create a reference to the dynamic variable
		}

		if ( !call_user_func_array( array( $stmt, 'bind_result' ), array_values( $fields ) ) )
		{
			$stmt->free_result();
			$stmt->close();

			return $this->_setError( array( 'NFL-DATABASE-6', $this->_mysqli->error ) );
		}

		if ( $multiple_results )
		{
			$i = 0;

			while ( $stmt->fetch() )
			{
				$results[ $i ] = array();

				foreach ( $fields as $key => $value )
				{
					$results[ $i ][ $key ] = $value;
				}

				$i++;
			}
		}
		else
		{
			if ( !$stmt->fetch() )
			{
				$stmt->free_result();
				$stmt->close();

				return $this->_setError( array( 'NFL-DATABASE-7', $this->_mysqli->error ) );
			}

			foreach( $fields as $key => $value )
			{
				$results[ $key ] = $value;
			}
		}

		$stmt->free_result();

		if ( !$stmt->close() )
		{
			return $this->_setError( array( 'NFL-DATABASE-9', $this->_mysqli->error ) );
		}

		return true;
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
}

abstract class DatabaseTable
{
	protected $db_manager;

	public function __construct( DatabaseManager $db_manager )
	{
		$this->db_manager = $db_manager;
	}

	abstract public function Create();

	public function getError()
	{
		return $this->db_manager->connection()->Get_Error();
	}

	public function query( $query )
	{
		$bind_parmas = array_slice( func_get_args(), 1 );
		return $this->db_manager->connection()->query( $query, ...$bind_parmas );
	}

	public function select( $query, &$results )
	{
		$bind_parmas = array_slice( func_get_args(), 2 );
		return $this->db_manager->connection()->select( $query, $results, ...$bind_parmas );
	}

	public function single( $query, &$result )
	{
		$bind_parmas = array_slice( func_get_args(), 2 );
		return $this->db_manager->connection()->single( $query, $result, ...$bind_parmas );
	}

	public function insertID()
	{
		return $this->db_manager->connection()->insertID();
	}
}
