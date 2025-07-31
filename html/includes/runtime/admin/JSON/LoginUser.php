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
			return $this->setError( array( '#Error#', 'Could not load user' ) );
		}

		$this->auth()->logout();
		$this->auth()->login( $loaded_user[ 'id' ] );

		return true;
	}
}
