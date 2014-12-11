<?php
include_once( 'Definitions.php' );

class Database extends mysqli
{
	public 	$_host;
	public 	$_user;
	private	$_password;
	public  $_schema;
	private $_connected = false;

	public function __construct()
	{
		$settings = parse_ini_file( DB_CONFIG, true );

		if ( $settings === false )
		{
			die( 'Failed to parse the configuration file' );
		}

		$this->_host		= $settings[ 'database' ][ 'host' ];
		$this->_user		= $settings[ 'database' ][ 'username' ];
		$this->_password	= $settings[ 'database' ][ 'password' ];
		$this->_schema		= $settings[ 'database' ][ 'schema' ];

		parent::__construct( $this->_host, $this->_user, $this->_password, $this->_schema );

		if ( mysqli_connect_error() )
		{
			die( 'Database error: ' . mysqli_connect_error() );
		}

		$this->_connected = true;
	}

	public function __destruct()
	{
		if ( $this->_connected )
		{
			parent::close();
		}
	}

	public function query( $query )
	{
		$args = func_get_args();

		return $this->_Run_Statement( $query, $args );
	}

	public function select( $query, &$results )
	{
		$results 	= array();
		$args		= func_get_args();

		if ( !$this->_Run_Statement( $query, $args, true, $results, $count ) )
		{
			return false;
		}

		return $count;
	}

	public function single( $query, &$results )
	{
		$results 	= array();
		$args		= func_get_args();

		if ( !$this->_Run_Statement( $query, $args, false, $results, $count ) )
		{
			return false;
		}

		return $count;
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

		foreach( $values as $key => $null )
		{
			$columns 	.= $columns == '' ? $key : sprintf( ', %s', $key );
			$prepared	.= $prepared == '' ? '?' : ', ?';
		}

		array_push( $args, sprintf( 'INSERT INTO %s ( %s ) VALUES ( %s )', $table, $columns, $prepared ) );

		return call_user_func_array( array( $this, 'query' ), array_merge( $args, $values ) );
	}

	private function _Run_Statement( &$query, $arg_list, $multiple_results = false, &$results = null, &$count = 0 )
	{
		$count 			= 0;
		$arg_count 		= count( $arg_list );
		$min_arg_number = ( is_null( $results ) ) ? 1 : 2;
		$stmt 			= $this->stmt_init();

		if ( !$stmt->prepare( $query ) )
		{
			return Functions::Error( 'NFL-DATABASE-0', $this->error );
		}

		if ( $arg_count > $min_arg_number )
		{
			$bind_params = array( '' ); // initialize the empty array

			for( $i = $min_arg_number; $i < $arg_count; $i++ )
			{
				switch( gettype( $arg_list[ $i ] ) )
				{
					case 'integer':
						$bind_params[ 0 ] .= 'i';
						break;

					case 'double':
						$bind_params[ 0 ] .= 'd';
						break;

					case 'string':
						$bind_params[ 0 ] .= 's';
						break;

					default:
						$bind_params[ 0 ] .= 'b';
						break;
				}

				$bind_params[ $i ] = &$arg_list[ $i ];
			}

			if ( !call_user_func_array( array( $stmt, 'bind_param' ), $bind_params ) )
			{
				return Functions::Error( 'NFL-DATABASE-1', $this->error );
			}
		}

		if ( !$stmt->execute() )
		{
			return Functions::Error( 'NFL-DATABASE-2', $this->error );
		}

		if ( !is_null( $results ) )
		{
			if ( !$stmt->store_result() )
			{
				return Functions::Error( 'NFL-DATABASE-3', $this->error );
			}

			if ( !$count = $stmt->num_rows )
			{
				if ( !$stmt->close() )
				{
					return Functions::Error( 'NFL-DATABASE-4', $this->error );
				}

				return true;
			}

			if ( !$meta = $stmt->result_metadata() )
			{
				return Functions::Error( 'NFL-DATABASE-5', $this->error );
			}

			$fields = array();

			while( $field = $meta->fetch_field() )
			{
				$var 			= $field->name;
				$$var 			= null;
				$fields[ $var ] = &$$var;
			}

			if ( !call_user_func_array( array( $stmt, 'bind_result' ), $fields ) )
			{
				return Functions::Error( 'NFL-DATABASE-6', $this->error );
			}

			if ( $multiple_results )
			{
				$i = 0;

				while( $stmt->fetch() )
				{
					$results[ $i ] = array();

					foreach( $fields as $key => $value )
					{
						$results[ $i ][ $key ] = $value;
					}

					$i++;
				}
			} else {
				if ( !$stmt->fetch() )
				{
					return Functions::Error( 'NFL-DATABASE-7', $this->error );
				}

				foreach( $fields as $key => $value )
				{
					$results[ $key ] = $value;
				}
			}
		}

		if ( !$stmt->close() )
		{
			return Functions::Error( 'NFL-DATABASE-8', $this->error );
		}

		return true;
	}
}
?>
