<?php

class JSON_DeleteNews implements iJSON
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
		$news_id 	= Functions::Post( 'news_id' );
		
		if ( !$db_news->Delete( $news_id ) )
		{
			return $this->_json->DB_Error();
		}

		return true;		
	}
}
