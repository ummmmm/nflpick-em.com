<?php

function Module_JSON( &$db, &$user )
{
	$db_polls			= new Polls( $db );
	$db_poll_answers	= new Poll_Answers( $db );
	$db_poll_votes		= new Poll_Votes( $db );
	$token				= Functions::Post( 'token' );
	$poll_id			= Functions::Post( 'poll_id' );
	$answer_id			= Functions::Post( 'answer_id' );
	
	if ( !Sessions::Validate( $db, $user->id, $token ) )
	{
		return JSON_Response_Error( 'NFL-POLLS_VOTE-0', 'Action cannot be completed. You do not have a valid session.' );
	}
	
	$count = $db_polls->Load( $poll_id, $poll );
	
	if ( $count === false )
	{
		return JSON_Response_Global_Error();
	}
	
	if ( $count === 0 )
	{
		return JSON_Response_Error( 'NFL-POLLS_VOTE-1', 'Failed to load poll' );
	}
	
	$count = $db_poll_answers->Load_Poll( $answer_id, $poll_id, $answer );
	
	if ( $count === false )
	{
		return JSON_Response_Global_Error();
	}
	
	if ( $count === 0 )
	{
		return JSON_Response_Error( 'NFL-POLLS_VOTE-2', 'Failed to load answer' );
	}
	
	$count = Vote_Load_Poll_User( $db, $poll_id, $user->id );
	
	if ( $count === false )
	{
		return JSON_Response_Global_Error();
	}
	
	if ( $count !== 0 )
	{
		return JSON_Response_Error( 'already_voted', 'You have already voted on this poll' );
	}
	
	$vote[ 'poll_id' ] 		= $poll_id;
	$vote[ 'answer_id' ]	= $answer_id;
	$vote[ 'user_id' ]		= $user->id;
	
	if ( !$db_poll_votes->Insert( $vote ) )
	{
		return JSON_Response_Global_Error();
	}
	
	return JSON_Response_Success();
}

function Vote_Load_Poll_User( &$db, $poll_id, $user_id )
{
	return $db->single( 'SELECT * FROM poll_votes WHERE poll_id = ? AND user_id = ?', $null, $poll_id, $user_id );
}
