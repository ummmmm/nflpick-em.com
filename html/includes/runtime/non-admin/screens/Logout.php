<?php

class Screen_Logout extends Screen
{
	public function content()
	{
		$db_sessions = new Sessions( $this->_db );
		$db_sessions->Delete_Cookie( Functions::Cookie( "session" ) );
		setcookie( 'session', null, -1, INDEX );
		header( sprintf( 'location: %s', INDEX ) );

		return true;
	}
}
