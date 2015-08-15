<?php

class JSON_UpdatePaidUser implements iJSON
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
		$user_id	= Functions::Post( 'user_id' );
		$db_users	= new Users( $this->_db );
		$count 		= $db_users->Load( $user_id, $loaded_user );
		
		if ( $count === false )
		{
			return $this->_json->DB_Error();
		}
		
		if ( $count === 0 )
		{
			return $this->_json->setError( array( 'NFL-USERS_UPDATE-1', 'Failed to load user' ) );
		}
		
		$loaded_user[ 'paid' ] = ( $loaded_user[ 'paid' ] === 1 ) ? 0 : 1;
		
		if ( !$db_users->Update( $loaded_user ) )
		{
			return $this->_json->DB_Error();
		}
		
		return true;
	}
}
