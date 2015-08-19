<?php

class Screen_Default implements iScreen
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

	public function content()
	{
		print '<h1>Admin Panel</h1>';
		print '<p><a href="?view=admin&screen=games">Games</a></p>';
		print '<p><a href="?view=admin&screen=users">Users</a></p>';
		print '<p><a href="?view=admin&screen=weeks">Weeks</a></p>';
		print '<p><a href="?view=admin&screen=settings">Settings</a></p>';
		print '<p><a href="?view=admin&screen=news">News</a></p>';
		print '<p><a href="?view=admin&screen=polls">Polls</a></p>';
		print '<p><a href="?view=admin&screen=update_scores">Update Scores</a></p>';

		return true;
	}
}
