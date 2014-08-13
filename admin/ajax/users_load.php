<?php
function Module_JSON( &$db, &$user )
{
	$sort		= Functions::Post( 'sort' );
	$direction	= Functions::Post( 'direction' );
	$token 		= Functions::Post( 'token' );
	
	if ( !Sessions::Validate_Admin( $db, $user->id, $token ) )
	{
		return JSON_Response_Error( 'NFL-USERS_LOAD-0', 'Invalid token.' );
	}
	
	if ( !Load_Users( $db, $sort, $direction, $users ) )
	{
		return JSON_Response_Error();
	}
	
	foreach( $users as &$loaded_user )
	{
		$loaded_user[ 'last_on' ] 			= Functions::FormatDate( $loaded_user[ 'last_on' ] );
		$loaded_user[ 'current_place' ] 	= Functions::Place( $loaded_user[ 'current_place' ] ); 
	}
	
	return JSON_Response_Success( $users );
}

function Load_Users( $db, $sort, $direction, &$users )
{
	$current	= Weeks::Current( $db );
	$direction 	= ( $direction === 'asc' ) ? 'ASC' : 'DESC';
	$sql 		= "SELECT
						u.*,
						CONCAT( u.fname, ' ', u.lname ) AS name,
						( SELECT COUNT( * ) FROM failed_logins WHERE email = u.email ) AS failed_logins,
						( SELECT COUNT( * ) FROM sessions WHERE userid = u.id ) AS active_sessions,
						( SELECT COUNT( * ) FROM picks p WHERE p.user_id = u.id AND p.week = ? AND p.picked = 0 ) AS remaining
					FROM
						users u
					ORDER BY
						{$sort} {$direction}";
	return $db->select( $sql, $users, $current );
}
?>