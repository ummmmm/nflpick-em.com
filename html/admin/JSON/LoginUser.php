<?php

class JSON_LoginUser implements iJSON
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
		$user_id 		= Functions::Post_Int( 'user_id' );
		$db_users		= new Users( $this->_db );
		$db_sessions	= new Sessions( $this->_db );
		
		$count = $db_users->Load( $user_id, $loaded_user );
		
		if ( $count === false )
		{
			return $this->_json->DB_Error();
		}
		
		if ( $count === 0 )
		{
			return $this->_json->setError( array( 'NFL-USERS_LOGIN-0', 'Could not load user' ) );
		}
		
		if ( !$db_sessions->Delete_Cookie( Functions::Cookie( 'session' ) ) )
		{
			return $this->_json->DB_Error();
		}
		
		$db_users->id = $loaded_user[ 'id' ];
		$db_users->CreateSession();
		
		return true;
	}
}
