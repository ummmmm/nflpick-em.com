<?php

class JSON_LoadPolls extends JSON
{
	public function execute()
	{
		$db_polls			= $this->db()->polls();
		$db_poll_votes		= $this->db()->pollvotes();
		$db_poll_answers	= $this->db()->pollanswers();
		$nav_poll 			= Functions::Post( 'nav' );
		
		if ( $nav_poll === '1' )
		{
			$count = $db_polls->Latest( $loaded_polls );
		} else {
			$count = $db_polls->List_Load( $loaded_polls );
		}
		
		if ( $count === false )
		{
			return $this->setDBError();
		}
		
		foreach( $loaded_polls as &$poll )
		{
			$count = $db_poll_answers->List_Load_Poll( $poll[ 'id' ], $answers );
			
			if ( $count === false )
			{
				return $this->setDBError();
			}
			
			$vote_count = $db_poll_votes->Total_Poll( $poll[ 'id' ] );
			
			if ( $vote_count === false )
			{
				return $this->setDBError();
			}
			
			$poll[ 'total_votes' ] = $vote_count;
			
			foreach( $answers as &$answer )
			{
				$answer_count = $db_poll_votes->Total_Answer( $answer[ 'id' ] );
				
				if ( $answer_count === false )
				{
					return $this->setDBError();
				}
				
				$answer[ 'total_votes' ] = $answer_count;
			}
			
			$poll[ 'answers' ] = $answers;
			
			if ( !$this->_auth->isUser() )
			{
				$poll[ 'voted' ] = true;
			}
			else
			{
				$count = $this->_Vote_Casted( $this->_auth->getUserID(), $poll[ 'id' ] );
				
				if ( $count === false )
				{
					return $this->setDBError();
				}
				
				$poll[ 'voted' ] = ( $count !== 0 ) ? true : false;
			}
		}

		return $this->setData( $loaded_polls );
	}

	// Helper functions

	private function _Vote_Casted( $user_id, $poll_id )
	{
		return $this->_db->single( 'SELECT * FROM poll_votes WHERE user_id = ? AND poll_id = ?', $null, $user_id, $poll_id );
	}
}
