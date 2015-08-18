<?php

class JSON_LoadPolls implements iJSON
{
	public function __construct( Database &$db, Authentication &$auth, JSON &$json )
	{
		$this->_db		= $db;
		$this->_auth	= $auth;
		$this->_json	= $json;
	}

	public function requirements()
	{
		return array();
	}

	public function execute()
	{
		$db_polls			= new Polls( $this->_db );
		$db_poll_votes		= new Poll_Votes( $this->_db );
		$db_poll_answers	= new Poll_Answers( $this->_db );
		$nav_poll 			= Functions::Post( 'nav' );
		
		if ( $nav_poll === '1' )
		{
			$count = $db_polls->Latest( $loaded_polls );
		} else {
			$count = $db_polls->List_Load( $loaded_polls );
		}
		
		if ( $count === false )
		{
			return $this->_json->DB_Error();
		}
		
		foreach( $loaded_polls as &$poll )
		{
			$count = $db_poll_answers->List_Load_Poll( $poll[ 'id' ], $answers );
			
			if ( $count === false )
			{
				return $this->_json->DB_Error();
			}
			
			$vote_count = $db_poll_votes->Total_Poll( $poll[ 'id' ] );
			
			if ( $vote_count === false )
			{
				return $this->_json->DB_Error();
			}
			
			$poll[ 'total_votes' ] = $vote_count;
			
			foreach( $answers as &$answer )
			{
				$answer_count = $db_poll_votes->Total_Answer( $answer[ 'id' ] );
				
				if ( $answer_count === false )
				{
					return $this->_json->DB_Error();
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
					return $this->_json->DB_Error();
				}
				
				$poll[ 'voted' ] = ( $count !== 0 ) ? true : false;
			}
		}

		return $this->_json->setData( $loaded_polls );
	}

	// Helper functions

	private function _Vote_Casted( $user_id, $poll_id )
	{
		return $this->_db->single( 'SELECT * FROM poll_votes WHERE user_id = ? AND poll_id = ?', $null, $user_id, $poll_id );
	}
}
