<?php

class JSON_InsertPoll extends JSONAdminAction
{
	public function execute()
	{
		$db_poll_answers	= $this->db()->pollanswers();
		$db_polls			= $this->db()->polls();
		$question			= Functions::Post( 'question' );
		$answers			= array_filter( Functions::Post_Array( 'answers' ) );
		$active				= Functions::Post_Active( 'active' );
		$valid_answer		= false;
		
		if ( $question === '' )
		{
			return $this->setError( array( 'NFL-POLLS_INSERT-1', 'Question cannot be blank' ) );
		}

		if ( count( $answers ) === 0 )
		{
			return $this->setError( array( '#Error#', 'Must provide at least one answer' ) );
		}

		$poll_insert[ 'question' ] 	= $question;
		$poll_insert[ 'active' ] 	= $active;
		
		if ( !$db_polls->Insert( $poll_insert ) )
		{
			return $this->setDBError();
		}
		
		$poll_id = $db_polls->insertID();
		
		foreach( $answers as $answer )
		{
			if ( $answer == '' )
			{
				continue;
			}

			$answer_insert = array( 'poll_id' => $poll_id, 'answer' => $answer );
			
			if ( !$db_poll_answers->Insert( $answer_insert ) )
			{
				return $this->setDBError();
			}
		}
		
		return true;
	}
}
