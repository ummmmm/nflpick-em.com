<?php

class JSON_DeletePoll extends JSONAdminAction
{
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
