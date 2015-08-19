<?php

class Screen_Users implements iScreen
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
		print "$.fn.sort( 'LoadUsers', 'name', $.fn.sort_user_callback );";

		return true;
	}

	public function content()
	{
		$db_users 	= new Users( $this->_db );
		$count		= $db_users->List_Load( $users );

		if ( $count === false )	return $this->_screen->setDBError();

		print '<h1>Users</h1>';
		print '<div class="sortby">Sort By: ';
		print '<a href="javascript:;" id="name" direction="asc" onclick="$.fn.sort( \'LoadUsers\', \'name\', $.fn.sort_user_callback );">Name</a>';
		print '<a href="javascript:;" id="current_place" direction="asc" onclick="$.fn.sort( \'LoadUsers\', \'current_place\', $.fn.sort_user_callback );">Current Place</a>';
		print '<a href="javascript:;" id="last_on" direction="desc" onclick="$.fn.sort( \'LoadUsers\', \'last_on\', $.fn.sort_user_callback );">Last Active</a>';
		print '<a href="javascript:;" id="paid" direction="asc" onclick="$.fn.sort( \'LoadUsers\', \'paid\', $.fn.sort_user_callback );">Paid</a>';
		print '<a href="javascript:;" id="failed_logins" direction="asc" onclick="$.fn.sort( \'LoadUsers\', \'failed_logins\', $.fn.sort_user_callback );">Failed Logins</a>';
		print '<a href="javascript:;" id="active_sessions" direction="desc" onclick="$.fn.sort( \'LoadUsers\', \'active_sessions\', $.fn.sort_user_callback );">Active Sessions</a>';
		print '<a href="javascript:;" id="remaining" direction="asc" onclick="$.fn.sort( \'LoadUsers\', \'remaining\', $.fn.sort_user_callback );">Remaining Picks</a>';
		print '</div>';
		print '<div id="users_loading">Loading...</div>';

	return true;
	}
}
