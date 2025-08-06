<?php

class JSON_DeleteUser extends JSONAdminAction
{
	public function execute()
	{
		$db_users	= $this->db()->users();
		$password	= Functions::Post( 'password' );
		$user_id 	= Functions::Post( 'user_id' );

		if ( !$this->auth()->validate_login( $this->_auth->getUser()[ 'email' ], $password, $null ) )
		{
			throw new NFLPickEmException( 'Invalid password' );
		}

		if ( !$db_users->Load( $user_id, $user ) )	throw new NFLPickEmException( 'User does not exist' );
		else if ( $user[ 'admin' ] )				throw new NFLPickEmException( 'You cannot delete an admin user' );

		$db_users->Delete( $user_id );

		return true;
	}
}
