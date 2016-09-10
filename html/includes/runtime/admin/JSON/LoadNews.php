<?php

class JSON_LoadNews extends JSONAdmin
{
	public function execute()
	{
		$db_news 	= new News( $this->_db );	
		$count 		= $db_news->List_Load( $news );
		
		if ( $count === false )
		{
			return $this->setDBError();
		}
		
		return $this->setData( $news );
	}
}
