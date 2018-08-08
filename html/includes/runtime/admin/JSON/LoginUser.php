<?php

class JSON_LoginUser extends JSONAdminAction
{
	public function execute()
	{
		$user_id 		= Functions::Post_Int( 'user_id' );
		$db_users		= new Users( $this->_db );
		$db_sessions	= new Sessions( $this->_db );
		
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
		
		$db_users->id = $loaded_user[ 'id' ];
		$db_users->CreateSession();
		
		return true;
	}
}
