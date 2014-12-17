<?php
function Module_JSON( &$db, &$user )
{
	$db_picks		= new Picks( $db );
	$db_sent_picks	= new Sent_Picks( $db );
	$db_weeks		= new Weeks( $db );
	$week			= Functions::Post( 'week' );
	$token			= Functions::Post( 'token' );
	
	if ( !Sessions::Validate( $db, $user->id, $token ) )
	{
		return JSON_Response_Error( 'NFL-EMAILPICKS-0', 'Action cannot be completed. Please verify you are logged in.' );
	}
	
	if ( !$db_weeks->Load( $week, $null ) )
	{
		return JSON_Response_Error( 'NFL-EMAILPICKS-1', "Failed to load week '{$week}'" );
	}
	
	$count = $db_picks->UserWeekList_Load( $user->id, $week, $picks );
	
	if ( $count === false )
	{
		return JSON_Response_Error();
	}
	
	$sent = array( 'userid' => $user->id, 'week' => $week, 'date' => Functions::Timestamp(), 'picks' => array() );
	$mail = new Mail( $user->account[ 'email' ], "Week {$week} Picks" );
	
	foreach( $picks as $pick )
	{
		if ( $pick[ 'winner_pick' ] === 0 )
		{
			continue;
		}

		array_push( $sent[ 'picks' ], array( 'winner' => $pick[ 'winner' ], 'loser' => $pick[ 'loser' ] ) );
	}
	
	$mail->message( ConfirmationText( $week, $sent[ 'picks' ] ) );

	if ( $mail->send() === false )
	{
		return JSON_Response_Error( 'NFL-EMAILPICKS-3', 'The email failed to send. Please try again later.' );
	}
	
	$insert = array( 'user_id' => $user->id, 'picks' => json_encode( $sent ), 'week' => $week );

	if ( !$db_sent_picks->Insert( $insert ) )
	{
		return JSON_Response_Error();
	}
	
	return JSON_Response_Success( "Your picks for week {$week} have been sent." );
}

function ConfirmationText( $week, $picks )
{
	$output = 'Here are your picks for week ' . $week . '. Please save them for your records.<br /><br />';
	
	foreach( $picks as $pick )
	{
		$output .= 'You have picked the <b>' . $pick[ 'winner' ] . '</b> to beat the <b> ' . $pick[ 'loser' ] . '</b><br />';
	}
	
	return $output;
}
