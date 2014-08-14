<?php
function Module_Content( &$db, &$user )
{
	if ( !Settings::Load( $db, $settings ) )
	{
		return false;
	}
	
	$count = OnlineUsersList_Load( $db, $users, $settings[ 'online' ] );

	if ( $count === false )
	{
		return false;
	}

	if ( $count === 0 )
	{
		print '<h1>Online Users</h1>';
		print '<p>Currently no users online.</p>';
		
		return true;
	}
	
	print '<h1>Online Users - Last ' . htmlentities( $settings[ 'online' ] ) . ' Minutes</h1>';
	
	foreach( $users as $loaded_user )
	{
		$date = new DateTime( $loaded_user[ 'last_on' ] );
		print '<p>' . $loaded_user[ 'name' ] . ' - Last active on ' . $date->format( 'D F d, Y' ) . ' at '. $date->format( 'h:i A T' ) . '</p>';
	}
	
	return true;
}

function OnlineUsersList_Load( &$db, &$list, &$online_time )
{
	$date 	= Functions::Timestamp();
	$online = $db->select( 'SELECT CONCAT( fname, \' \', lname ) AS name, last_on FROM users WHERE last_on > ( ? - INTERVAL ? MINUTE ) ORDER BY last_on DESC, name', $list, $date, $online_time );
	
	if ( $online === false )
	{
		return false;
	}
	
	return $online;
}
?>
