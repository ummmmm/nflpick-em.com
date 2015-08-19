<?php

class Screen_Weeks implements iScreen
{
	public function __construct( Database &$db, Authentication &$auth, Screen &$screen )
	{
		$this->_db		= $db;
		$this->_auth	= $auth;
		$this->_screen	= $screen;
	}

	public function requirements()
	{
		return array( "admin" => true );
	}

	public function jquery()
	{
		print "$.fn.load_weeks();";

		return true;
	}

	public function content()
	{
		print '<h1>Weeks</h1>';
		print '<div id="weeks_loading">Loading...</div>';

	return true;
	}
}
