<?php

class Screen_Online extends Screen
{
	public function content()
	{
		$db_settings = new Settings( $this->_db );

		if ( !$db_settings->Load( $settings ) )
		{
			return false;
		}

		$count = $this->_OnlineUsersList_Load( $users, $settings[ 'online' ] );

		if ( $count === false )
		{
			return false;
		}

		if ( $count == 0 )
		{
			print '<h1>Online Users</h1>';
			print '<p>Currently no users online.</p>';

			return true;
		}

		printf( "<h1>Online Users - Last %d Minutes</h1>", $settings[ "online" ] );

		foreach( $users as $loaded_user )
		{
			$date = new DateTime();
			$date->setTimestamp( $loaded_user[ 'last_on' ] );

			printf( "<p>%s - Last active on %s at %s</p>", htmlentities( $loaded_user[ 'name' ] ), $date->format( "D F d, Y" ), $date->format( "h:i A T" ) );
		}

		return true;
	}

	private function _OnlineUsersList_Load( &$users, $minutes )
	{
		$time	= time() - ( 60 * $minutes );
		$online = $this->_db->select( 'SELECT CONCAT( fname, \' \', lname ) AS name, last_on FROM users WHERE last_on > ? ORDER BY last_on DESC, name', $users, $time );

		if ( $online === false )
		{
			return $this->setDBError();
		}

		return $online;
	}
}
