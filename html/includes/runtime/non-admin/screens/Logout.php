<?php

class Screen_Logout extends Screen
{
	public function content()
	{
		$db_sessions = $this->db()->sessions();
		$db_sessions->Delete_Cookie( Functions::Cookie( "session" ) );
		setcookie( 'session', null, -1, INDEX );
		header( sprintf( 'location: %s', INDEX ) );

		return true;
	}
}
