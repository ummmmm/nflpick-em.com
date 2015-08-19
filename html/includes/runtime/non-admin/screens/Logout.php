<?php

class Screen_Logout implements iScreen
{
	public function __construct( Database &$db, Authentication &$auth, Screen &$screen )
	{
		$this->_db		= $db;
		$this->_auth	= $auth;
		$this->_screen	= $screen;
	}

	public function requirements()
	{
		return array( "user" => true );
	}

	public function content()
	{
		$db_sessions = new Sessions( $this->_db );
		$db_sessions->Delete_Cookie( Functions::Cookie( "session" ) );
		setcookie( 'session', null, -1, INDEX );
		header( sprintf( 'location: %s', INDEX ) );

		return true;
	}
}
