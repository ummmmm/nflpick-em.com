<?php

class JSON_DeleteUser extends JSONAdminAction
{
	public function execute()
	{
		$db_users	= new Users( $this->_db );
		$password	= Functions::Post( 'password' );
		$user_id 	= Functions::Post( 'user_id' );

		if ( !$db_users->validateLogin( $this->_auth->getUser()[ 'email' ], $password, $null ) )
		{
			return $this->setError( array( '#Error#', 'Invalid password' ) );
		}

		if ( !$db_users->Load( $user_id, $user ) )
		{
			return $this->setError( array( '#Error#', 'Failed to load user' ) );
		}
		else if ( $user[ 'admin' ] )
		{
			return $this->setError( array( '#Error#', 'You cannot delete an admin user' ) );
		}

		if ( !$db_users->Delete( $user_id ) )
		{
			return $this->setDBError();
		}

		return true;
	}
}
