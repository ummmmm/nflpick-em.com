<?php

class JSON_UpdateNews extends JSONAdminAction
{
	public function execute()
	{
		$db_news	= $this->db()->news();
		$news_id	= $this->input()->value_int( 'news_id' );
		$title		= $this->input()->value_str( 'title' );
		$message	= $this->input()->value_str( 'message' );
		$active		= $this->input()->value_bool( 'active', int: true );
		
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
