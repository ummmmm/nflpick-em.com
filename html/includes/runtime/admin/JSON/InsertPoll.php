<?php

class JSON_InsertPoll implements iJSON
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
		$db_poll_answers	= new Poll_Answers( $this->_db );
		$db_polls			= new Polls( $this->_db );
		$question			= Functions::Post( 'question' );
		$answers			= array_filter( Functions::Post_Array( 'answers' ) );
		$active				= Functions::Post_Active( 'active' );
		$valid_answer		= false;
		
		if ( $question === '' )
		{
			return $this->_json->setError( array( 'NFL-POLLS_INSERT-1', 'Question cannot be blank' ) );
		}

		if ( count( $answers ) === 0 )
		{
			return $this->_json->setError( array( '#Error#', 'Must provide at least one answer' ) );
		}

		$poll_insert[ 'question' ] 	= $question;
		$poll_insert[ 'active' ] 	= $active;
		
		if ( !$db_polls->Insert( $poll_insert ) )
		{
			return $this->_json->DB_Error();
		}
		
		$poll_id = $this->_db->insertID();
		
		foreach( $answers as $answer )
		{
			if ( $answer == '' )
			{
				continue;
			}

			$answer_insert = array( 'poll_id' => $poll_id, 'answer' => $answer );
			
			if ( !$db_poll_answers->Insert( $answer_insert ) )
			{
				return $this->_json->DB_Error();
			}
		}
		
		return true;
	}
}
