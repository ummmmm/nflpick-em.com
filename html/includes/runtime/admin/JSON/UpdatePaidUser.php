<?php

class JSON_UpdatePaidUser extends JSONAdminAction
{
	public function execute()
	{
		$user_id	= Functions::Post( 'user_id' );
		$db_users	= new Users( $this->_db );
		$count 		= $db_users->Load( $user_id, $loaded_user );
		
		if ( $count === false )
		{
			return $this->setDBError();
		}
		
		if ( $count === 0 )
		{
			return $this->setError( array( 'NFL-USERS_UPDATE-1', 'Failed to load user' ) );
		}
		
		$loaded_user[ 'paid' ] = ( $loaded_user[ 'paid' ] === 1 ) ? 0 : 1;
		
		if ( !$db_users->Update( $loaded_user ) )
		{
			return $this->setDBError();
		}
		
		return true;
	}
}
