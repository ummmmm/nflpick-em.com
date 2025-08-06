<?php

class JSON_InsertNews extends JSONAdminAction
{
	public function execute()
	{
		$db_news	= $this->db()->news();
		$title		= Functions::Post( 'title' );
		$message	= Functions::Post( 'message' );
		$active		= Functions::Post_Active( 'active' );	
		
		if ( $title === '' )		throw new NFLPickEmException( 'Title cannot be blank' );
		else if ( $message === '' )	throw new NFLPickEmException( 'Message cannot be blank' );

		$insert = array( 'title' => $title, 'news' => $message, 'active' => $active, 'user_id' => $this->_auth->getUserID() );
		
		$db_news->Insert( $insert );

		return true;		
	}
}
