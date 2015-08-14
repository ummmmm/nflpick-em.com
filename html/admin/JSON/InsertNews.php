<?php

class JSON_InsertNews implements iJSON
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
		$title		= Functions::Post( 'title' );
		$message	= Functions::Post( 'message' );
		$active		= Functions::Post_Active( 'active' );	
		
		if ( $title === '' )
		{
			return $this->_json->setError( array( 'NFL-NEWS_INSERT-0', 'Title cannot be blank' ) );
		}
		
		if ( $message === '' )
		{
			return $this->_json->setError( array( 'NFL-NEWS_INSERT-1', 'Message cannot be blank' ) );
		}

		$insert = array( 'title' => $title, 'news' => $message, 'active' => $active, 'user_id' => $this->_auth->userID );
		
		if ( !$db_news->Insert( $insert ) )
		{
			return $this->_json->DB_Error();
		}

		return true;		
	}
}
