<?php

class JSON_DeleteUser implements iJSON
{
	public function __construct( Database &$db, Authentication &$auth, JSON &$json )
	{
		$this->_db		= $db;
		$this->_auth	= $auth;
		$this->_json	= $json;
	}

	public function requirements()
	{
		return array( 'admin' => true, 'token' => true );
	}

	public function execute()
	{
		$db_users	= new Users( $this->_db );
		$password	= Functions::Post( 'password' );
		$user_id 	= Functions::Post( 'user_id' );

		if ( !$db_users->validateLogin( $this->_auth->getUser()[ 'email' ], $password, $null ) )
		{
			return $this->_json->setError( array( '#Error#', 'Invalid password' ) );
		}

		if ( !$db_users->Load( $user_id, $user ) )
		{
			return $this->_json->setError( array( '#Error#', 'Failed to load user' ) );
		}
		else if ( $user[ 'admin' ] )
		{
			return $this->_json->setError( array( '#Error#', 'You cannot delete an admin user' ) );
		}

		if ( !$db_users->Delete( $user_id ) )
		{
			return $this->_json->DB_Error();
		}

		return true;
	}
}
