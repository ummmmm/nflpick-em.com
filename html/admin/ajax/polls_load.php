<?php
function Module_JSON( &$db, &$user )
{
	$db_poll_answers	= new Poll_Answers( $db );
	$db_poll_votes		= new Poll_Votes( $db );
	$db_polls			= new Polls( $db );
	$token 				= Functions::Get( 'token' );
	
	if ( !Sessions::Validate( $db, $user->id, $token ) )
	{
		return JSON_Response_Error( 'NFL-POLLS_LOAD-0', 'You do not have a valid token to complete this action.' );
	}
	
	$count = $db_polls->List_Load( $polls );
	
	if ( $count === false )
	{
		return JSON_Response_Global_Error();
	}
	
	foreach( $polls as &$poll )
	{
		$count = $db_poll_answers->List_Load_Poll( $poll[ 'id' ], $answers );
		
		if ( $count === false )
		{
			return JSON_Response_Global_Error();
		}
		
		$votes_count = $db_poll_votes->Total_Poll( $poll[ 'id' ] );
		
		if ( $votes_count === false )
		{
			return JSON_Response_Global_Error();
		}
		
		$poll[ 'date' ] 		= Functions::FormatDate( $poll[ 'date' ] );
		$poll[ 'answers' ] 		= $answers;
		$poll[ 'total_votes' ]	= $votes_count;
	}
	
	return JSON_Response_Success( $polls );
}
?>