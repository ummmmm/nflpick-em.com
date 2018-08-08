<?php

class JSON_InsertNews extends JSONAdminAction
{
	public function execute()
	{
		$db_news	= new News( $this->_db );
		$title		= Functions::Post( 'title' );
		$message	= Functions::Post( 'message' );
		$active		= Functions::Post_Active( 'active' );	
		
		if ( $title === '' )
		{
			return $this->setError( array( 'NFL-NEWS_INSERT-0', 'Title cannot be blank' ) );
		}
		
		if ( $message === '' )
		{
			return $this->setError( array( 'NFL-NEWS_INSERT-1', 'Message cannot be blank' ) );
		}

		$insert = array( 'title' => $title, 'news' => $message, 'active' => $active, 'user_id' => $this->_auth->getUserID() );
		
		if ( !$db_news->Insert( $insert ) )
		{
			return $this->setDBError();
		}

		return true;		
	}
}
