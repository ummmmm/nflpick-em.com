<?php

require_once( "includes/config.php" );
include_once( 'includes/db/db.php' );
include_once( "includes/classes/functions.php" );

class Database
{
	private $_connected;
	private $_mysqli;
	private	$_host;
	private	$_user;
	private	$_password;
	private $_schema;
	private $_error_code;
	private $_error_message;

	public function __construct()
	{
		$db_settings = array();

		if ( !defined( "CONFIG_INI" ) || !Functions::Get_Config_Section( CONFIG_INI, "database", $db_settings ) )
		{
			die( "Failed to load configuration settings" );
		}

		$this->_connected	= false;
		$this->_mysqli		= null;
		$this->_host		= $db_settings[ 'host' ];
		$this->_user		= $db_settings[ 'username' ];
		$this->_password	= $db_settings[ 'password' ];
		$this->_schema		= $db_settings[ 'schema' ];
		$this->_mysqli 		= @new mysqli( $this->_host, $this->_user, $this->_password, $this->_schema );

		if ( $this->_mysqli->connect_error )
		{
			die( sprintf( "Database error: %s", htmlentities( $this->_mysqli->connect_error ) ) );
		}

		$this->_connected 	= true;
	}

	public function __destruct()
	{
		if ( $this->_connected )
		{
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

		return call_user_func_array( array( $this, 'query' ), array_merge( $args, $values ) );
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

		if ( !$stmt->prepare( $query ) )
		{
			return $this->_Set_Error( 'NFL-DATABASE-0', $this->error );
		}

		if ( $bind_count )
		{
			if ( $stmt->param_count != $bind_count )
			{
				return $this->_Set_Error( '#Error#', sprintf( 'Parameter count mismatch, expected %d parameters, found %d', $bind_count, $stmt->param_count ) );
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
				return $this->_Set_Error( 'NFL-DATABASE-1', $this->error );
			}
		}

		if ( !$stmt->execute() )
		{
			return $this->_Set_Error( 'NFL-DATABASE-2', $this->error );
		}

		if ( is_null( $results ) ) // Must be an INSERT, UPDATE, DELETE
		{
			if ( !$stmt->close() )
			{
				return $this->_Set_Error( 'NFL-DATABASE-8', $this->error );
			}

			return true;
		}

		// Otherwise we have a result set

		if ( !$stmt->store_result() )
		{
			$stmt->close();

			return $this->_Set_Error( 'NFL-DATABASE-3', $this->error );
		}

		if ( ( $result_count = $stmt->num_rows ) == 0 )
		{
			$stmt->free_result();

			if ( !$stmt->close() )
			{
				return $this->_Set_Error( 'NFL-DATABASE-4', $this->error );
			}

			return true;
		}

		if ( ( $meta = $stmt->result_metadata() ) === false )
		{
			$stmt->free_result();
			$stmt->close();

			return $this->_Set_Error( 'NFL-DATABASE-5', $this->error );
		}

		$fields = array();

		while( ( $field = $meta->fetch_field() ) !== false )
		{
			$colunm_name			= $field->name;
			${$colunm_name}			= null; // create a dynamic PHP variable set to the column name
			$fields[ $colunm_name ] = &${$colunm_name}; // create a reference to the dynamic variable
		}

		if ( !call_user_func_array( array( $stmt, 'bind_result' ), $fields ) )
		{
			$stmt->free_result();
			$stmt->close();

			return $this->_Set_Error( 'NFL-DATABASE-6', $this->error );
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

				return $this->_Set_Error( 'NFL-DATABASE-7', $this->error );
			}

			foreach( $fields as $key => $value )
			{
				$results[ $key ] = $value;
			}
		}

		$stmt->free_result();

		if ( !$stmt->close() )
		{
			return $this->_Set_Error( 'NFL-DATABASE-8', $this->error );
		}

		return true;
	}

	private function _Set_Error( $code, $message )
	{
		$this->_error_code		= $code;
		$this->_error_message 	= $message;

		return Functions::Error( $code, $message );
	}

	public function Get_Error()
	{
		return $this->_error_message;
	}
}
