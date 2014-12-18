<?php

function Module_JSON( &$db, &$user )
{
	$db_poll_answers	= new Poll_Answers( $db );
	$db_polls			= new Polls( $db );
	$token 				= Functions::Get( 'token' );
	$question			= Functions::Post( 'question' );
	$answers			= array_filter( Functions::Post_Array( 'answers' ) );
	$active				= Functions::Post_Active( 'active' );
	$valid_answer		= false;
	
	if ( !Sessions::Validate( $db, $user->id, $token ) || !$user->account[ 'admin' ] )
	{
		return JSON_Response_Error( 'NFL-POLLS_INSERT-0', 'You do not have a valid token to complete this action.' );
	}
	
	if ( $question === '' )
	{
		return JSON_Response_Error( 'NFL-POLLS_INSERT-1', 'Question cannot be blank' );
	}

	if ( count( $answers ) === 0 )
	{
		return JSON_Response_Error( '#Error#', 'Must provide at least one answer' );
	}

	$poll_insert[ 'question' ] 	= $question;
	$poll_insert[ 'active' ] 	= $active;
	
	if ( !$db_polls->Insert( $poll_insert ) )
	{
		return JSON_Response_Error();
	}
	
	$poll_id = $db->insert_id;
	
	foreach( $answers as $answer )
	{
		$answer_insert = array( 'poll_id' => $poll_id, 'answer' => $answer );
		
		if ( !$db_poll_answers->Insert( $answer_insert ) )
		{
			return JSON_Response_Error();
		}
	}
	
	return JSON_Response_Success();
}
