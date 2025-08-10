<?php

class RawInput
{
	public function value_POST_str( $name, $default = '' )
	{
		return $this->_value_xxx_str( $_POST, $name, $default );
	}

	public function value_POST_int( $name, $default = 0 )
	{
		return $this->_value_xxx_int( $_POST, $name, $default );
	}

	public function value_POST_bool( $name, $default = false, $int = false )
	{
		return $this->_value_xxx_bool( $_POST, $name, $default, $int );
	}

	public function value_GET_str( $name, $default = '' )
	{
		return $this->_value_xxx_str( $_GET, $name, $default );
	}

	public function value_GET_int( $name, $default = 0 )
	{
		return $this->_value_xxx_int( $_GET, $name, $default );
	}

	public function value_GET_bool( $name, $default = false, $int = false )
	{
		return $this->_value_xxx_bool( $_GET, $name, $default, $int );
	}

	private function _value_xxx_str( $array, $name, $default )
	{
		if ( !isset( $array[ $name ] ) )
		{
			return $default;
		}

		return trim( $array[ $name ] );
	}

	private function _value_xxx_int( $array, $name, $default )
	{
		if ( !isset( $array[ $name ] ) )
		{
			return $default;
		}

		if ( !preg_match( '/^-?\d+$/', $array[ $name ] ) )
		{
			throw new NFLPickEmException( sprintf( 'Invalid input type for field %s, integer expected', $name ) );
		}

		return ( int ) $array[ $name ];
	}

	private function _value_xxx_bool( $array, $name, $default, $int )
	{
		if ( !isset( $array[ $name ] ) )
		{
			return $int ? ( int ) $default : ( bool ) $default;
		}

		if ( $array[ $name ] !== '1' && $array[ $name ] !== '0' )
		{
			throw new NFLPickEmException( sprintf( 'Invalid input type for field %s, 1/0 expected', $name ) );
		}

		return $int ? ( int ) $array[ $name ] : ( bool ) $array[ $name ];
	}
}


class JSONInput
{
	private $_requestBody;

	public function __construct()
	{
		$this->_requestBody = json_decode( file_get_contents( 'php://input' ), true, 512, JSON_THROW_ON_ERROR );
	}

	public function value_str( $name, $default = '' )
	{
		$raw = trim( $this->_requestBody[ $name ] ?? $default );

		if ( !is_string( $raw ) )
		{
			throw new NFLPickEmException( sprintf( 'Invalid input type for field %s, string expected', $name ) );
		}

		return $raw;
	}

	public function value_int( $name, $default = 0 )
	{
		$raw = $this->_requestBody[ $name ] ?? $default;

		if ( !is_int( $raw ) )
		{
			throw new NFLPickEmException( sprintf( 'Invalid input type for field %s, integer expected', $name ) );
		}

		return $raw;
	}

	public function value_bool( $name, $default = false, $int = false )
	{
		$raw = $this->_requestBody[ $name ] ?? $default;

		if ( !is_bool( $raw ) )
		{
			throw new NFLPickEmException( sprintf( 'Invalid input type for field %s, boolean expected', $name ) );
		}

		return $int ? ( int ) $raw : $raw;
	}

	public function value_array_str( $name, $default = [] )
	{
		$raw = $this->_requestBody[ $name ] ?? $default;

		if ( !is_array( $raw ) )
		{
			throw new NFLPickEmException( sprintf( 'Invalid input type for field %s, array expected', $name ) );
		}

		foreach ( $raw as $index => $value )
		{
			if ( !is_string( $value ) )
			{
				throw new NFLPickEmException( sprintf( 'Invalid array element type for index %d, string expected', $index ) );
			}
		}

		return $raw;
	}
}
