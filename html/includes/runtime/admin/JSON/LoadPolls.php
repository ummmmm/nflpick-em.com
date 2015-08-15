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
		return array( 'admin' => true );
	}

	public function execute()
	{
		$db_poll_answers	= new Poll_Answers( $this->_db );
		$db_poll_votes		= new Poll_Votes( $this->_db );
		$db_polls			= new Polls( $this->_db );
		$count 				= $db_polls->List_Load( $polls );
		
		if ( $count === false )
		{
			return $this->_json->DB_Error();
		}
		
		foreach( $polls as &$poll )
		{
			$count = $db_poll_answers->List_Load_Poll( $poll[ 'id' ], $answers );
			
			if ( $count === false )
			{
				return $this->_json->DB_Error();
			}
			
			$votes_count = $db_poll_votes->Total_Poll( $poll[ 'id' ] );
			
			if ( $votes_count === false )
			{
				return $this->_json->DB_Error();
			}
			
			$poll[ 'date' ] 		= Functions::FormatDate( $poll[ 'date' ] );
			$poll[ 'answers' ] 		= $answers;
			$poll[ 'total_votes' ]	= $votes_count;
		}
		
		return $this->_json->setData( $polls );
	}
}
