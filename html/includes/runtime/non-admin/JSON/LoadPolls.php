<?php

class JSON_LoadPolls extends JSON
{
	public function execute()
	{
		$db_polls			= $this->db()->polls();
		$db_poll_votes		= $this->db()->pollvotes();
		$db_poll_answers	= $this->db()->pollanswers();
		$nav_poll 			= $this->input()->value_bool( 'nav' );

		if ( $nav_poll )	$db_polls->Latest( $loaded_polls );
		else				$db_polls->List_Load( $loaded_polls );

		foreach( $loaded_polls as &$poll )
		{
			$db_poll_answers->List_Load_Poll( $poll[ 'id' ], $answers );

			$poll[ 'total_votes' ] = $db_poll_votes->Total_Poll( $poll[ 'id' ] );

			foreach( $answers as &$answer )
			{
				$answer[ 'total_votes' ] = $db_poll_votes->Total_Answer( $answer[ 'id' ] );
			}

			$poll[ 'answers' ] = $answers;

			if ( !$this->auth()->isUser() )	$poll[ 'voted' ] = true;
			else							$poll[ 'voted' ] = $this->_Vote_Casted( $this->auth()->getUserID(), $poll[ 'id' ] ) > 0;
		}

		return $this->setData( $loaded_polls );
	}

	// Helper functions

	private function _Vote_Casted( $user_id, $poll_id )
	{
		return $this->db()->single( 'SELECT * FROM poll_votes WHERE user_id = ? AND poll_id = ?', $null, $user_id, $poll_id );
	}
}
