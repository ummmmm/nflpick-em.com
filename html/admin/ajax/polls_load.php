<?php
function Module_JSON( &$db, &$user )
{
	$token = Functions::Get( 'token' );
	
	if ( !Sessions::Validate( $db, $user->id, $token ) )
	{
		return JSON_Response_Error( 'NFL-POLLS_LOAD-0', 'You do not have a valid token to complete this action.' );
	}
	
	$count = Polls::List_Load( $db, $polls );
	
	if ( $count === false )
	{
		return JSON_Response_Global_Error();
	}
	
	foreach( $polls as &$poll )
	{
		$count = Polls::AnswersList_Load_Poll( $db, $poll[ 'id' ], $answers );
		
		if ( $count === false )
		{
			return JSON_Response_Global_Error();
		}
		
		$votes_count = Polls::Votes_Total_Poll( $db, $poll[ 'id' ] );
		
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