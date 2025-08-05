<?php

class JSON_LoadPolls extends JSONAdmin
{
	public function execute()
	{
		$db_poll_answers	= $this->db()->pollanswers();
		$db_polls			= $this->db()->polls();

		$db_polls->List_Load( $polls );

		foreach( $polls as &$poll )
		{
			$db_poll_answers->List_Load_Poll( $poll[ 'id' ], $answers );
			$this->_load_poll_votes( $poll[ 'id' ], $votes );

			$poll[ 'date' ] 		= Functions::FormatDate( $poll[ 'date' ] );
			$poll[ 'answers' ] 		= $answers;
			$poll[ 'total_votes' ]	= $count;
			$poll[ 'votes' ]		= $votes;
		}

		return $this->setData( $polls );
	}

	private function _load_poll_votes( $poll_id, &$votes )
	{
		$this->db()->select( 'SELECT pa.answer, CONCAT( u.fname, \' \', u.lname ) AS name FROM poll_votes pv, poll_answers pa, users u WHERE pv.poll_id = ? AND pv.answer_id = pa.id AND pv.user_id = u.id ORDER BY pv.answer_id, name', $votes, $poll_id );
	}
}
