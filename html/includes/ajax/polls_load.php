<?php
function Module_JSON( &$db, &$user )
{
	$nav_poll = Functions::Post( 'nav' );
	
	if ( $nav_poll === '1' )
	{
		$count = Polls::Latest( $db, $polls );
	} else {
		$count = Polls::List_Load( $db, $polls );
	}
	
	if ( $count === false )
	{
		return JSON_Response_Error();
	}
	
	foreach( $polls as &$poll )
	{
		$count = Polls::AnswersList_Load_Poll( $db, $poll[ 'id' ], $answers );
		
		if ( $count === false )
		{
			return JSON_Response_Error();
		}
		
		$vote_count = Polls::Votes_Total_Poll( $db, $poll[ 'id' ] );
		
		if ( $vote_count === false )
		{
			return JSON_Response_Error();
		}
		
		$poll[ 'total_votes' ] = $vote_count;
		
		foreach( $answers as &$answer )
		{
			$answer_count = Polls::Votes_Total_Answer( $db, $answer[ 'id' ] );
			
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

	return JSON_Response_Success( $polls );
}

function Vote_Casted( &$db, $user_id, $poll_id )
{
	return $db->single( 'SELECT * FROM poll_votes WHERE user_id = ? AND poll_id = ?', $null, $user_id, $poll_id );
}
?>
