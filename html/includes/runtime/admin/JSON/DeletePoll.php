<?php

class JSON_DeletePoll extends JSONAdminAction
{
	public function execute()
	{
		$db_polls	= $this->db()->polls();
		$poll_id	= Functions::Post( 'poll_id' );
		
		$db_polls->Delete( $poll_id );
	
		return true;
	}
}
