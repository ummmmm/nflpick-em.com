<?php

class JSON_DeleteNews extends JSONAdminAction
{
	public function execute()
	{
		$db_news	= $this->db()->news();
		$news_id 	= Functions::Post( 'news_id' );
		
		if ( !$db_news->Delete( $news_id ) )
		{
			return $this->setDBError();
		}

		return true;		
	}
}
