<?php

class JSON_LogoutUser extends JSONAdminAction
{
	public function execute()
	{
		$db_sessions	= new Sessions( $this->_db );
		$db_users		= new Users( $this->_db );
		$user_id		= Functions::Post( 'user_id' );
		$count 			= $db_users->Load( $user_id, $null );
		
		if ( $count === false )
		{
			return $this->setDBError();
		}

		if ( $count === 0 )
		{
			return $this->setError( array( 'NFL-USERS_LOGOUT-0', 'Unable to load user' ) );
		}

		if ( !$db_sessions->Delete_User( $user_id ) )
		{
			return $this->setDBError();
		}
		
		return true;
	}
}
