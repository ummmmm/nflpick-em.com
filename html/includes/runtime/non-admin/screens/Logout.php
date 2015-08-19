<?php

class Screen_Logout implements iScreen
{
	public function __construct( Database &$db, Authentication &$auth, Screen &$screen )
	{
		$this->_db		= $db;
		$this->_auth	= $auth;
		$this->_screen	= $screen;
	}

	public function content()
	{
		if ( $this->_auth->authenticated )
		{
			$db_sessions = new Sessions( $this->_db );
			$db_sessions->Delete( $this->_auth->session[ 'token' ] );
			setcookie( 'session', null, -1, INDEX );
		}

		header( sprintf( 'location: %s', INDEX ) );

		return true;
	}
}
