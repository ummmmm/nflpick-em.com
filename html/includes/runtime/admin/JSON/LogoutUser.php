<?php

class JSON_LogoutUser implements iJSON
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
		$db_sessions	= new Sessions( $this->_db );
		$db_users		= new Users( $this->_db );
		$user_id		= Functions::Post( 'user_id' );
		$count 			= $db_users->Load( $user_id, $null );
		
		if ( $count === false )
		{
			return $this->_json->DB_Error();
		}

		if ( $count === 0 )
		{
			return $this->_json->setError( array( 'NFL-USERS_LOGOUT-0', 'Unable to load user' ) );
		}

		if ( !$db_sessions->Delete_User( $user_id ) )
		{
			return $this->_json->DB_Error();
		}
		
		return true;
	}
}
