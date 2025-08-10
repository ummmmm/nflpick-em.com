<?php

class JSON_DeletePoll extends JSONAdminAction
{
	public function execute()
	{
		$db_polls	= $this->db()->polls();
		$poll_id	= $this->input()->value_int( 'poll_id' );
		
		$db_polls->Delete( $poll_id );
	
		return true;
	}
}
