<?php

class JSON_LoadPolls extends JSONAdmin
{
	public function execute()
	{
		$db_poll_answers	= new Poll_Answers( $this->_db );
		$db_poll_votes		= new Poll_Votes( $this->_db );
		$db_polls			= new Polls( $this->_db );
		$count 				= $db_polls->List_Load( $polls );
		
		if ( $count === false )
		{
			return $this->setDBError();
		}
		
		foreach( $polls as &$poll )
		{
			$count = $db_poll_answers->List_Load_Poll( $poll[ 'id' ], $answers );
			
			if ( $count === false )
			{
				return $this->setDBError();
			}
			
			$votes_count = $db_poll_votes->Total_Poll( $poll[ 'id' ] );
			
			if ( $votes_count === false )
			{
				return $this->setDBError();
			}
			
			$poll[ 'date' ] 		= Functions::FormatDate( $poll[ 'date' ] );
			$poll[ 'answers' ] 		= $answers;
			$poll[ 'total_votes' ]	= $votes_count;
		}
		
		return $this->setData( $polls );
	}
}
