<?php

class JSON_LoginUser extends JSONAdminAction
{
	public function execute()
	{
		$user_id 		= Functions::Post_Int( 'user_id' );
		$db_users		= $this->db()->users();
		$db_sessions	= $this->db()->sessions();

		$count = $db_users->Load( $user_id, $loaded_user );

		if ( $count === false )
		{
			return $this->setDBError();
		}

		if ( $count === 0 )
		{
			return $this->setError( array( 'NFL-USERS_LOGIN-0', 'Could not load user' ) );
		}

		if ( !$db_sessions->Delete_Cookie( Functions::Cookie( 'session' ) ) )
		{
			return $this->setDBError();
		}

		$db_sessions	= $this->db()->sessions();
		$cookieid		= sha1( session_id() );
		$token			= sha1( uniqid( rand(), TRUE ) );

		setcookie( 'session', $cookieid, time() + 60 * 60 * 24 * 30, INDEX, '', true, true );

		if ( !$db_sessions->Insert( array( 'token' => $token, 'cookieid' => $cookieid, 'userid' => $loaded_user[ 'id' ] ) ) )
		{
			return $this->setDBError();
		}

		return true;
	}
}
