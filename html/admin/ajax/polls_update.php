<?php
function Module_JSON( &$db, &$user )
{
	$token		= Functions::Get( 'token' );
	$poll_id	= Functions::Post( 'poll_id' );
	$question	= Functions::Post( 'question' );
	$answers 	= Functions::Post_Array( 'answers' );
	$active		= Functions::Post_Active( 'active' );
	
	if ( !Sessions::Validate( $db, $user->id, $token ) || !$user->account[ 'admin' ] )
	{
		return JSON_Response_Error( 'NFL-POLLS_UPDATE-0', 'You do not have a valid token to complete this action.' );
	}
	
	if ( $question === '' )
	{
		return JSON_Response_Error( 'NFL-POLLS_UPDATE-1', 'Question cannot be blank' );
	}
	
	$count = Polls::Load( $db, $poll_id, $poll );
	
	if ( $count === false )
	{
		return JSON_Response_Global_Error();
	}
	
	if ( $count === 0 )
	{
		return JSON_Response_Error( 'NFL-POLLS_UPDATE-2', 'Failed to load poll' );
	}
	
	$count = Polls::AnswersList_Load_Poll( $db, $poll_id, $loaded_answers );
	
	if ( $count === false )
	{
		return JSON_Response_Global_Error();
	}
	
	$poll[ 'question' ] = $question;
	$poll[ 'active' ] 	= $active;
	
	if ( !Polls::Update( $db, $poll ) )
	{
		return JSON_Response_Global_Error();
	}
	
	foreach( $loaded_answers as $answer )
	{
		if ( !array_key_exists( $answer[ 'id' ], $answers ) )
		{
			if ( !Polls::Answer_Delete( $db, $answer[ 'id' ] ) )
			{
				return JSON_Response_Global_Error();
			}
			
			if ( !Polls::Votes_Delete_Answer( $db, $answer[ 'id' ] ) )
			{
				return JSON_Response_Global_Error();
			}
		} else {
			if ( !Polls::Answer_Load( $db, $answer[ 'id' ], $loaded_answer ) )
			{
				return JSON_Response_Global_Error();
			}
			
			$loaded_answer[ 'answer' ] = $answers[ $answer[ 'id' ] ];
			
			if ( !Polls::Answer_Update( $db, $loaded_answer ) )
			{
				return JSON_Response_Global_Error();
			}
		}

		unset( $answers[ $answer[ 'id' ] ] );
	}
	
	foreach( $answers as $key => $answer )
	{
		$answer_insert[ 'poll_id' ] = $poll_id;
		$answer_insert[ 'answer' ]	= $answer;
		
		if ( !Polls::Answer_Insert( $db, $answer_insert ) )
		{
			return JSON_Response_Global_Error();
		}
	}
	
	return JSON_Response_Success();
}
?>