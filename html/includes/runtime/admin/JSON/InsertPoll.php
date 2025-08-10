<?php

class JSON_InsertPoll extends JSONAdminAction
{
	public function execute()
	{
		$db_poll_answers	= $this->db()->pollanswers();
		$db_polls			= $this->db()->polls();
		$question			= $this->input()->value_str( 'question' );
		$answers			= $this->input()->value_array_str( 'answers' );
		$active				= $this->input()->value_bool( 'active', int: true );
		$valid_answer		= false;
		
		if ( $question === '' )				throw new NFLPickEmException( 'Question cannot be blank' );
		else if ( count( $answers ) === 0 )	throw new NFLPickEmException( 'Must provide at least one answer' );

		$poll_insert[ 'question' ] 	= $question;
		$poll_insert[ 'active' ] 	= $active;
		
		$db_polls->Insert( $poll_insert );

		$poll_id = $db_polls->insertID();
		
		foreach( $answers as $answer )
		{
			if ( $answer == '' )
			{
				continue;
			}

			$answer_insert = array( 'poll_id' => $poll_id, 'answer' => $answer );
			
			$db_poll_answers->Insert( $answer_insert );
		}
		
		return true;
	}
}
