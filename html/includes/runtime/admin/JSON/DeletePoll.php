<?php

class JSON_DeletePoll implements iJSON
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
		$db_polls	= new Polls( $this->_db );
		$poll_id	= Functions::Post( 'poll_id' );
		
		if ( !$db_polls->Delete( $poll_id ) )
		{
			return $this->_json->DB_Error();
		}
	
		return true;
	}
}
