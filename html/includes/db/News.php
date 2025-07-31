<?php

class DatabaseTableNews extends DatabaseTable
{
	public function Create()
	{
		$sql = "CREATE TABLE news
				(
					id 		int( 11 ) AUTO_INCREMENT,
					user_id int( 11 ),
					title 	varchar( 255 ),
					news	text,
					date 	int( 11 ),
					ip 		varchar( 255 ),
					active 	tinyint( 1 ),
					PRIMARY KEY (id)
				)";

		return $this->query( $sql );
	}

	public function Insert( &$news )
	{
		$news[ 'ip' ]	= $_SERVER[ 'REMOTE_ADDR' ];
		$news[ 'date' ]	= time();

		return $this->query( 'INSERT INTO news ( user_id, title, news, date, ip, active ) VALUES ( ?, ?, ?, ?, ?, ? )', $news[ 'user_id' ], $news[ 'title' ], $news[ 'news' ], $news[ 'date' ], $news[ 'ip' ], $news[ 'active' ] );
	}

	public function Update( $news )
	{
		return $this->query( 'UPDATE news SET title = ?, news = ?, active = ? WHERE id = ?', $news[ 'title' ], $news[ 'news' ], $news[ 'active' ], $news[ 'id' ] );
	}

	public function Load( $newsid, &$news )
	{
		return $this->single( 'SELECT * FROM news WHERE id = ?', $news, $newsid );
	}

	public function List_Load( &$news )
	{
		return $this->select( 'SELECT * FROM news ORDER BY id DESC', $news );
	}

	public function Delete( $news_id )
	{
		return $this->query( 'DELETE FROM news WHERE id = ?', $news_id );
	}
}
