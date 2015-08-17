<?php

class JSON_UpdateNews implements iJSON
{
	public function __construct( Database &$db, Authentication &$auth, JSON &$json )
	{
		$this->_db		= $db;
		$this->_auth	= $auth;
		$this->_json	= $json;
	}

	public function requirements()
	{
		return array( 'admin' => true, 'token' => true );
	}

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
			return $this->_json->DB_Error();
		}
		
		if ( $count === 0 )
		{
			return $this->_json->setError( array( 'NFL-NEWS_UPDATE-0', 'Failed to load news' ) );
		}
		
		if ( $title === '' )
		{
			return $this->_json->setError( array( 'NFL-NEWS_UPDATE-1', 'Title cannot be blank' ) );
		}
		
		if ( $message === '' )
		{
			return $this->_json->setError( array( 'NFL-NEWS_UPDATE-2', 'Message cannot be blank' ) );
		}
		
		$news[ 'title' ] 	= $title;
		$news[ 'news' ]		= $message;
		$news[ 'active' ] 	= $active;
		
		if ( !$db_news->Update( $news ) )
		{
			return $this->_json->DB_Error();
		}
		
		return true;
	}
}
