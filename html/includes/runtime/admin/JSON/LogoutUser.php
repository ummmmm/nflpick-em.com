<?php

class JSON_LogoutUser extends JSONAdminAction
{
	public function execute()
	{
		$db_sessions	= $this->db()->sessions();
		$user_id		= Functions::Post( 'user_id' );

		$db_sessions->Delete_User( $user_id );

		return true;
	}
}
