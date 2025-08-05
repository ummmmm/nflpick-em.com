<?php

class JSON_UpdatePoll extends JSONAdminAction
{
	public function execute()
	{
		$poll_id			= Functions::Post( 'poll_id' );
		$question			= Functions::Post( 'question' );
		$answers 			= Functions::Post_Array( 'answers' );
		$active				= Functions::Post_Active( 'active' );
		$db_polls			= $this->db()->polls();
		$db_poll_votes		= $this->db()->pollvotes();
		$db_poll_answers	= $this->db()->pollanswers();

		if ( $question === '' )							throw new NFLPickEmException( 'Question cannot be blank' );
		else if ( !$db_polls->Load( $poll_id, $poll ) )	throw new NFLPickEmException( 'Poll does not exist' );
		
		$db_poll_answers->List_Load_Poll( $poll_id, $loaded_answers );
		
		$poll[ 'question' ] = $question;
		$poll[ 'active' ] 	= $active;
		
		$db_polls->Update( $poll );
		
		foreach( $loaded_answers as $answer )
		{
			if ( !array_key_exists( $answer[ 'id' ], $answers ) )
			{
				$db_poll_answers->Delete( $answer[ 'id' ] );
				$db_poll_votes->Delete_Answer( $answer[ 'id' ] );
			}
			else
			{
				if ( !$db_poll_answers->Load( $answer[ 'id' ], $loaded_answer ) )
				{
					throw new NFLPickEmException( 'Answer does not exist' );
				}
				
				$loaded_answer[ 'answer' ] = $answers[ $answer[ 'id' ] ];
				
				$db_poll_answers->Update( $loaded_answer );
			}

			unset( $answers[ $answer[ 'id' ] ] );
		}

		foreach( $answers as $key => $answer )
		{
			if ( $answer == '' )
			{
				continue;
			}

			$answer_insert[ 'poll_id' ] = $poll_id;
			$answer_insert[ 'answer' ]	= $answer;
			
			$db_poll_answers->Insert( $answer_insert );
		}
		
		return true;
	}
}
