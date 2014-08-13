<?php
function Module_Content( $db, $user )
{
	if ( !isset( $_GET[ 'user_id' ] ) )
	{
		$count = $user->List_Load( $users );
		
		if ( $count === false )
		{
			return false;
		}
		
		if ( $count === 0 )
		{
			print '<h1>Error</h1>';
			print '<p>No users found.</p>';
			
			return true;
		}
		
		print '<h1>Users</h1>';
		
		foreach ( $users as $new_user )
		{
			print '<p><a href="/?module=profile&user_id=' . $new_user[ 'id' ] . '">' . $new_user[ 'name' ] . '</a></p>';
		}
		
		return true;
	}
	
	$user_id = trim( $_GET[ 'user_id' ] );
	
	$count = $user->Load( $user_id, $new_user );
	
	if ( $count === false )
	{
		return false;
	}
	
	if ( $count === 0 )
	{
		print '<h1>User Not Found</h1>';
		print '<p>Sorry but user \'' . htmlentities( $user_id ) . '\' could not be found.</p>';
		
		return true;
	}
	
	print '<h1>' . htmlentities( $new_user[ 'name' ] ) . '\'s Profile</h1>';
	print '<p>' . htmlentities( $new_user[ 'name' ] ) . ' currently has ' . htmlentities( $new_user[ 'wins' ] ) . ' wins and ' . htmlentities( $new_user[ 'losses' ] ) . ' losses with a winning percentage of '.'. In addition, ' . $new_user[ 'fname' ] . '\'s last activity on the site was '.'.';
	
	return true;
}
?>