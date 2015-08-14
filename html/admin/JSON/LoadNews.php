<?php

class JSON_LoadNews implements iJSON
{
	public function __construct( Database &$db, Authentication &$auth, JSON &$json )
	{
		$this->_db		= $db;
		$this->_auth	= $auth;
		$this->_json	= $json;
	}

	public function requirements()
	{
		return array( 'admin' => true );
	}

	public function execute()
	{
		$db_news 	= new News( $this->_db );	
		$count 		= $db_news->List_Load( $news );
		
		if ( $count === false )
		{
			return $this->_json->DB_Error();
		}
		
		return $this->_json->setData( $news );
	}
}
