<?php

class JSON_UpdateNews extends JSONAdminAction
{
	public function execute()
	{
		$db_news	= $this->db()->news();
		$news_id	= Functions::Post( 'news_id' );
		$title		= Functions::Post( 'title' );
		$message	= Functions::Post( 'message' );
		$active		= Functions::Post_Active( 'active' );	
		
		if ( !$db_news->Load( $news_id, $news ) )	throw new NFLPickEmException( 'News does not exist' );
		else if ( $title === '' )					throw new NFLPickEmException( 'Title cannot be blank' );
		else if ( $message === '' )					throw new NFLPickEmException( 'Message cannot be blank' );
		
		$news[ 'title' ] 	= $title;
		$news[ 'news' ]		= $message;
		$news[ 'active' ] 	= $active;
		
		$db_news->Update( $news );
		
		return true;
	}
}
