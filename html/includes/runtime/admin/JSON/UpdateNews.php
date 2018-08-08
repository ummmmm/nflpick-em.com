<?php

class JSON_UpdateNews extends JSONAdminAction
{
	public function execute()
	{
		$db_news	= new News( $this->_db );
		$news_id	= Functions::Post( 'news_id' );
		$title		= Functions::Post( 'title' );
		$message	= Functions::Post( 'message' );
		$active		= Functions::Post_Active( 'active' );	
		
		$count = $db_news->Load( $news_id, $news );
		
		if ( $count === false )
		{
			return $this->setDBError();
		}
		
		if ( $count === 0 )
		{
			return $this->setError( array( 'NFL-NEWS_UPDATE-0', 'Failed to load news' ) );
		}
		
		if ( $title === '' )
		{
			return $this->setError( array( 'NFL-NEWS_UPDATE-1', 'Title cannot be blank' ) );
		}
		
		if ( $message === '' )
		{
			return $this->setError( array( 'NFL-NEWS_UPDATE-2', 'Message cannot be blank' ) );
		}
		
		$news[ 'title' ] 	= $title;
		$news[ 'news' ]		= $message;
		$news[ 'active' ] 	= $active;
		
		if ( !$db_news->Update( $news ) )
		{
			return $this->setDBError();
		}
		
		return true;
	}
}
