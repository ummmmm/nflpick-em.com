<?php

class JSON_UpdatePerfectWeekUser extends JSONAdminAction
{
	public function execute()
	{
		$user_id	= Functions::Post( 'user_id' );
		$db_users	= $this->db()->users();
		
		if ( !$db_users->Load( $user_id, $loaded_user ) )
		{
			throw new NFLPickEmException( 'User does not exist' );
		}
		
		$loaded_user[ 'pw_opt_out' ] = ( int ) !$loaded_user[ 'pw_opt_out' ];
		
		$db_users->Update( $loaded_user );
		
		return true;
	}
}
