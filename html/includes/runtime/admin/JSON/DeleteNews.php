<?php

class JSON_DeleteNews extends JSONAdminAction
{
	public function execute()
	{
		$db_news	= $this->db()->news();
		$news_id 	= Functions::Post( 'news_id' );

		$db_news->Delete( $news_id );

		return true;
	}
}
