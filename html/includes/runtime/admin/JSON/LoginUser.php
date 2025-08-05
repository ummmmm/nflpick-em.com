<?php

class JSON_LoginUser extends JSONAdminAction
{
	public function execute()
	{
		$user_id 		= Functions::Post_Int( 'user_id' );
		$db_users		= $this->db()->users();
		$db_sessions	= $this->db()->sessions();

		if ( !$db_users->Load( $user_id, $loaded_user ) )
		{
			throw new NFLPickEmException( 'User does not exist' );
		}

		$this->auth()->logout();
		$this->auth()->login( $loaded_user[ 'id' ] );

		return true;
	}
}
