<?php

class JSON_VotePoll extends JSONUserAction
{
	public function execute()
	{
		$db_polls			= $this->db()->polls();
		$db_poll_answers	= $this->db()->pollanswers();
		$db_poll_votes		= $this->db()->pollvotes();
		$poll_id			= $this->input()->value_int( 'poll_id' );
		$answer_id			= $this->input()->value_int( 'answer_id' );
		
		if ( !$db_polls->Load( $poll_id, $poll ) )										throw new NFLPickEmException( 'Poll does not exist' );
		else if ( !$db_poll_answers->Load_Poll( $answer_id, $poll_id, $answer ) )		throw new NFLPickEmException( 'Answer does not exist' );
		else if ( $this->_Vote_Load_Poll_User( $poll_id, $this->auth()->getUserID() ) )	throw new NFLPickEmException( 'You have already voted on this poll' );
		
		$vote[ 'poll_id' ] 		= $poll_id;
		$vote[ 'answer_id' ]	= $answer_id;
		$vote[ 'user_id' ]		= $this->auth()->getUserID();

		$db_poll_votes->Insert( $vote );
		
		return true;
	}

	// Helper functions

	private function _Vote_Load_Poll_User( $poll_id, $user_id )
	{
		return $this->db()->single( 'SELECT * FROM poll_votes WHERE poll_id = ? AND user_id = ?', $null, $poll_id, $user_id );
	}
}
