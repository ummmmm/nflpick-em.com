<?php

class JSON_InsertNews extends JSONAdminAction
{
	public function execute()
	{
		$db_news	= $this->db()->news();
		$title		= $this->input()->value_str( 'title' );
		$message	= $this->input()->value_str( 'message' );
		$active		= $this->input()->value_bool( 'active', int: true );

		if ( $title === '' )		throw new NFLPickEmException( 'Title cannot be blank' );
		else if ( $message === '' )	throw new NFLPickEmException( 'Message cannot be blank' );

		$insert = array( 'title' => $title, 'news' => $message, 'active' => $active, 'user_id' => $this->auth()->getUserID() );

		$db_news->Insert( $insert );

		return true;
	}
}
