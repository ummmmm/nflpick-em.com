<?php

class JSON_UpdateUser extends JSONAdminAction
{
	public function execute()
	{
		$db_users	= new Users( $this->_db );
		$user_id	= Functions::Post_Int( 'user_id' );
		$first_name	= Functions::Post( 'first_name' );
		$last_name	= Functions::Post( 'last_name' );
		$message	= Functions::Post( 'message' );

		if ( !$db_users->Load( $user_id, $user ) )
		{
			return $this->setError( array( 'NFL-USER_UPDATE-1', 'User does not exist' ) );
		}

		if ( $first_name === '' )
		{
			return $this->setError( array( 'NFL-USER_UPDATE-2', 'First name cannot be blank' ) );
		}

		if ( $last_name === '' )
		{
			return $this->setError( array( 'NFL-USER_UPDATE-3', 'Last name cannot be blank' ) );
		}

		$user[ 'fname' ] = $first_name;
		$user[ 'lname' ] = $last_name;

		$user[ 'active' ]	= ( $message === '' ) ? 1 : 0;
		$user[ 'message' ]	= $message;

		if ( !$db_users->Update( $user ) )
		{
			return $this->setDBError();
		}

		return true;
	}
}
