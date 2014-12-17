<?php

function Module_JSON( &$db, &$user )
{
	$polls			= new Polls( $db );
	$poll_votes		= new Poll_Votes( $db );
	$poll_answers	= new Poll_Answers( $db );
	$nav_poll 		= Functions::Post( 'nav' );
	
	if ( $nav_poll === '1' )
	{
		$count = $polls->Latest( $loaded_polls );
	} else {
		$count = $polls->List_Load( $loaded_polls );
	}
	
	if ( $count === false )
	{
		return JSON_Response_Error();
	}
	
	foreach( $loaded_polls as &$poll )
	{
		$count = $poll_answers->List_Load_Poll( $poll[ 'id' ], $answers );
		
		if ( $count === false )
		{
			return JSON_Response_Error();
		}
		
		$vote_count = $poll_votes->Total_Poll( $poll[ 'id' ] );
		
		if ( $vote_count === false )
		{
			return JSON_Response_Error();
		}
		
		$poll[ 'total_votes' ] = $vote_count;
		
		foreach( $answers as &$answer )
		{
			$answer_count = $poll_votes->Total_Answer( $answer[ 'id' ] );
			
			if ( $answer_count === false )
			{
				return JSON_Response_Error();
			}
			
			$answer[ 'total_votes' ] = $answer_count;
		}
		
		$poll[ 'answers' ] = $answers;
		
		if ( !$user->logged_in )
		{
			$poll[ 'voted' ] = true;
		} else {
			$count = Vote_Casted( $db, $user->id, $poll[ 'id' ] );
			
			if ( $count === false )
			{
				return JSON_Response_Error();
			}
			
			$poll[ 'voted' ] = ( $count !== 0 ) ? true : false;
		}
	}

	return JSON_Response_Success( $loaded_polls );
}

function Vote_Casted( &$db, $user_id, $poll_id )
{
	return $db->single( 'SELECT * FROM poll_votes WHERE user_id = ? AND poll_id = ?', $null, $user_id, $poll_id );
}
?>
