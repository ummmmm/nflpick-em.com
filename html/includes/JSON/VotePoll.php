<?php

class JSON_VotePoll implements iJSON
{
	public function __construct( Database &$db, Authentication &$auth, JSON &$json )
	{
		$this->_db		= $db;
		$this->_auth	= $auth;
		$this->_json	= $json;
	}

	public function requirements()
	{
		return array( 'user' => true, 'token' => true );
	}

	public function execute()
	{
		$db_polls			= new Polls( $this->_db );
		$db_poll_answers	= new Poll_Answers( $this->_db );
		$db_poll_votes		= new Poll_Votes( $this->_db );
		$token				= Functions::Post( 'token' );
		$poll_id			= Functions::Post( 'poll_id' );
		$answer_id			= Functions::Post( 'answer_id' );
		
		$count = $db_polls->Load( $poll_id, $poll );
		
		if ( $count === false )
		{
			return $this->_json->DB_Error();
		}
		
		if ( $count === 0 )
		{
			return $this->_json->setError( array( 'NFL-POLLS_VOTE-1', 'Failed to load poll' ) );
		}
		
		$count = $db_poll_answers->Load_Poll( $answer_id, $poll_id, $answer );
		
		if ( $count === false )
		{
			return $this->_json->DB_Error();
		}
		
		if ( $count === 0 )
		{
			return $this->_json->setError( array( 'NFL-POLLS_VOTE-2', 'Failed to load answer' ) );
		}
		
		$count = $this->_Vote_Load_Poll_User( $poll_id, $this->_auth->userID );
		
		if ( $count === false )
		{
			return $this->_json->DB_Error();
		}
		
		if ( $count !== 0 )
		{
			return $this->_json->setError( array( 'already_voted', 'You have already voted on this poll' ) );
		}
		
		$vote[ 'poll_id' ] 		= $poll_id;
		$vote[ 'answer_id' ]	= $answer_id;
		$vote[ 'user_id' ]		= $this->_auth->userID;
		
		if ( !$db_poll_votes->Insert( $vote ) )
		{
			return $this->_json->DB_Error();
		}
		
		return true;
	}

	// Helper functions

	private function _Vote_Load_Poll_User( $poll_id, $user_id )
	{
		return $this->_db->single( 'SELECT * FROM poll_votes WHERE poll_id = ? AND user_id = ?', $null, $poll_id, $user_id );
	}
}
