<?php
function Module_JSON( &$db, &$user )
{
	$userid 	= Functions::Post( 'userid' );
	$week		= Functions::Post( 'week' );
	$token		= Functions::Post( 'token' );
	
	if ( !Sessions::Validate( $db, $user->id, $token ) )
	{
		return JSON_Response_Error( 'NFL-HIGHLIGHTPICKS-0', 'Action cannot be completed. Please verify you are logged in.' );
	}	
	
	if ( !Weeks::IsLocked( $db, $week ) )
	{
		return JSON_Response_Error( 'NFL-HIGHLIGHTPICKS-1', "Week '{$week}' has not been locked yet." );
	}

	$diff_picks = array();

	if ( $userid != 0 )
	{	
		if ( Load_Different_Picks( $db, $user->id, $userid, $week, $picks ) === false )
		{
			return JSON_Response_Error();
		}

		array_push( $diff_picks, array( 'userid' => $userid, 'games' => explode( ',', $picks[ 'game_ids' ] ) ) );
	}
	else
	{
		if ( Load_Different_Picks( $db, $user->id, NULL, $week, $users_picks ) === false )
		{
			return JSON_Response_Error();
		}

		foreach( $users_picks as &$user_picks )
		{
			array_push( $diff_picks, array( 'userid' => $user_picks[ 'user_id' ], 'games' => explode( ',', $user_picks[ 'game_ids' ] ) ) );
		}
	}

	return JSON_Response_Success( $diff_picks );
}

function Load_Different_Picks( &$db, $userid1, $userid2, $weekid, &$picks )
{
	$sign 	= '=';
	$single	= true;

	if ( is_null( $userid2 ) )
	{
		$sign 		= '<>';
		$userid2 	= $userid1;
		$single		= false;
	}

	$query = "SELECT
				GROUP_CONCAT( p2.game_id ) AS game_ids,
				p2.user_id
			  FROM
				picks p1,
				picks p2
			  WHERE
				p1.user_id 		= 		? 			AND
				p2.user_id 		{$sign} ? 			AND
				p1.week 		= 		? 			AND
				p1.game_id 		= 		p2.game_id 	AND
				p2.picked		= 		1			AND
				p1.winner_pick	<> 		p2.winner_pick
			  GROUP BY
				p2.user_id";

	if ( $single )	$result = $db->single( $query, $picks, $userid1, $userid2, $weekid );
	else			$result = $db->select( $query, $picks, $userid1, $userid2, $weekid );

	return $result;
}
?>