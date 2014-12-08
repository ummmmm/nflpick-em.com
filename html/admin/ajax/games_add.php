<?php
function Module_JSON( &$db, &$user )
{
	$total				= 0;
	$skipped			= 0;
	$games 				= explode( '|', Functions::Post( 'games' ) );
	$loaded_team_ids 	= array();
	$loaded_weeks 		= array();
	$token				= Functions::Get( 'token' );
	
	if ( !Sessions::Validate( $db, $user->id, $token ) )
	{
		return JSON_Response_Error( 'NFL-GAMES_ADD-0', 'You do not have a valid token to complete this action.' );
	}
	
	if ( $user->account[ 'admin' ] !== 1 )
	{
		return JSON_Response_Error( 'NFL-GAMES_ADD-1', 'You must be an administrator to complete this action.' );
	}
	
	foreach( $games as $game )
	{
		list( $weekid, $away, $home, $date ) = explode( '::', $game );

		if ( !preg_match( '/(AM|PM)$/', $date ) )
		{
			return JSON_Response_Error( '#Error#', 'Missing AM/PM from game, maybe a bye week? Check your JS parser again' );
		}
		
		$away = preg_replace( '/NY/', 'New York', $away );
		$home = preg_replace( '/NY/', 'New York', $home );
		
		if ( !isset( $loaded_weeks[ $weekid ] ) )
		{
			if ( !Weeks::Load( $db, $weekid, $week ) )
			{
				return JSON_Response_Error( 'NFL-GAMES_ADD-3', "Failed to load week '{$weekid}'" );
			}
			
			$loaded_weeks[ $weekid ] = $week;
		}
		
		if ( !isset( $loaded_team_ids[ $away ] ) )
		{
			if ( !Teams::Load_Name( $db, $away, $team ) )
			{
				return JSON_Response_Error();
			}

			$away_id					= $team[ 'id' ];
			$loaded_teams_ids[ $away ] 	= $team[ 'id' ];
		} else { 
			$away_id = $loaded_teams_ids[ $away ];
		}
		
		if ( !isset( $loaded_team_ids[ $home ] ) )
		{
			if ( !Teams::Load_Name( $db, $home, $team ) )
			{
				return JSON_Response_Error();
			}

			$home_id					= $team[ 'id' ];
			$loaded_teams_ids[ $home ]	= $team[ 'id' ];
		} else { 
			$home_id = $loaded_teams_ids[ $home ];
		}
		
		if ( Games::Exists_Week_Teams( $db, $weekid, $home_id, $away_id ) )
		{
			$skipped++;
			continue;
		}
		
		$gamedate = new DateTime( $date, new DateTimeZone( 'America/New_York' ) );
		$gamedate->setTimezone( new DateTimeZone( 'America/Los_Angeles' ) );
		
		if ( !Games::Insert( $db, array( 'away' => $away_id, 'home' => $home_id, 'date' => $gamedate->format( 'Y-m-d H:i:s' ), 'week' => $weekid ) ) )
		{
			return JSON_Response_Error();
		}
		
		$total++;
	}
	
	return JSON_Response_Success( "{$total} games added. {$skipped} games skipped." );
}
?>