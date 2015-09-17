<?php

class JSON_UpdatePoll implements iJSON
{
	public function __construct( Database &$db, Authentication &$auth, JSON &$json )
	{
		$this->_db		= $db;
		$this->_auth	= $auth;
		$this->_json	= $json;
	}

	public function requirements()
	{
		return array( 'admin' => true, 'token' => true );
	}

	public function execute()
	{
		$poll_id			= Functions::Post( 'poll_id' );
		$question			= Functions::Post( 'question' );
		$answers 			= Functions::Post_Array( 'answers' );
		$active				= Functions::Post_Active( 'active' );
		$db_polls			= new Polls( $this->_db );
		$db_poll_votes		= new Poll_Votes( $this->_db );
		$db_poll_answers	= new Poll_Answers( $this->_db );

		if ( $question === '' )
		{
			return $this->_json->setError( array( 'NFL-POLLS_UPDATE-1', 'Question cannot be blank' ) );
		}
		
		$count = $db_polls->Load( $poll_id, $poll );
		
		if ( $count === false )
		{
			return $this->_json->DB_Error();
		}
		
		if ( $count === 0 )
		{
			return $this->_json->setError( array( 'NFL-POLLS_UPDATE-2', 'Failed to load poll' ) );
		}
		
		$count = $db_poll_answers->List_Load_Poll( $poll_id, $loaded_answers );
		
		if ( $count === false )
		{
			return $this->_json->DB_Error();
		}
		
		$poll[ 'question' ] = $question;
		$poll[ 'active' ] 	= $active;
		
		if ( !$db_polls->Update( $poll ) )
		{
			return $this->_json->DB_Error();
		}
		
		foreach( $loaded_answers as $answer )
		{
			if ( !array_key_exists( $answer[ 'id' ], $answers ) )
			{
				if ( !$db_poll_answers->Delete( $answer[ 'id' ] ) )
				{
					return $this->_json->DB_Error();
				}
				
				if ( !$db_poll_votes->Delete_Answer( $answer[ 'id' ] ) )
				{
					return $this->_json->DB_Error();
				}
			}
			else
			{
				if ( !$db_poll_answers->Load( $answer[ 'id' ], $loaded_answer ) )
				{
					return $this->_json->DB_Error();	
				}
				
				$loaded_answer[ 'answer' ] = $answers[ $answer[ 'id' ] ];
				
				if ( !$db_poll_answers->Update( $loaded_answer ) )
				{
					return $this->_json->DB_Error();
				}
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
			
			if ( !$db_poll_answers->Insert( $answer_insert ) )
			{
				return $this->_json->DB_Error();
			}
		}
		
		return true;
	}
}
