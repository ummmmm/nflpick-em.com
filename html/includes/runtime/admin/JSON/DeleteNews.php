<?php

class JSON_DeleteNews extends JSON
{
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
			return $this->setDBError();
		}

		return true;		
	}
}
