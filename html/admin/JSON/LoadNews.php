<?php

class JSON_LoadNews implements iJSON
{
	private $_db;
	private $_auth;
	private $_error;
	private $_data;

	public function __construct( Database &$db, Authentication &$auth )
	{
		$this->_db		= $db;
		$this->_auth	= $auth;
		$this->_data	= null;
		$this->_error	= array();
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
			return $this->_db->Get_Error();
		}
		
		return $this->_setData( $news );
	}

	public function getData()
	{
		return $this->_data;
	}

	public function getError()
	{
		return $this->_error;
	}

	public function _setData( $data )
	{
		$this->_data = $data;
		return true;
	}

	private function _setError( $error )
	{
		$this->_error = $error;
		return false;
	}
}
