<?php

class JSON_VotePoll extends JSONUserAction
{
	public function execute()
	{
		$db_polls			= $this->db()->polls();
		$db_poll_answers	= $this->db()->pollanswers();
		$db_poll_votes		= $this->db()->pollvotes();
		$token				= Functions::Post( 'token' );
		$poll_id			= Functions::Post( 'poll_id' );
		$answer_id			= Functions::Post( 'answer_id' );
		
		$count = $db_polls->Load( $poll_id, $poll );
		
		if ( $count === false )
		{
			return $this->setDBError();
		}
		
		if ( $count === 0 )
		{
			return $this->setError( array( 'NFL-POLLS_VOTE-1', 'Failed to load poll' ) );
		}
		
		$count = $db_poll_answers->Load_Poll( $answer_id, $poll_id, $answer );
		
		if ( $count === false )
		{
			return $this->setDBError();
		}
		
		if ( $count === 0 )
		{
			return $this->setError( array( 'NFL-POLLS_VOTE-2', 'Failed to load answer' ) );
		}
		
		$count = $this->_Vote_Load_Poll_User( $poll_id, $this->_auth->getUserID() );
		
		if ( $count === false )
		{
			return $this->setDBError();
		}
		
		if ( $count !== 0 )
		{
			return $this->setError( array( 'already_voted', 'You have already voted on this poll' ) );
		}
		
		$vote[ 'poll_id' ] 		= $poll_id;
		$vote[ 'answer_id' ]	= $answer_id;
		$vote[ 'user_id' ]		= $this->_auth->getUserID();
		
		if ( !$db_poll_votes->Insert( $vote ) )
		{
			return $this->setDBError();
		}
		
		return true;
	}

	// Helper functions

	private function _Vote_Load_Poll_User( $poll_id, $user_id )
	{
		return $this->db()->single( 'SELECT * FROM poll_votes WHERE poll_id = ? AND user_id = ?', $null, $poll_id, $user_id );
	}
}
