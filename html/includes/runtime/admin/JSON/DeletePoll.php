<?php

class JSON_DeletePoll extends JSON
{
	public function requirements()
	{
		return array( 'admin' => true, 'token' => true );
	}

	public function execute()
	{
		$db_polls	= new Polls( $this->_db );
		$poll_id	= Functions::Post( 'poll_id' );
		
		if ( !$db_polls->Delete( $poll_id ) )
		{
			return $this->setDBError();
		}
	
		return true;
	}
}
