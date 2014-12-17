<?php

class News
{
	private $_db;

	public function __construct( Database &$db )
	{
		$this->_db = $db;
	}

	public function Create()
	{
		$sql = "CREATE TABLE news
				(
					id 		int( 11 ) AUTO_INCREMENT,
					user_id int( 11 ),
					title 	varchar( 255 ),
					news	text,
					date 	datetime,
					ip 		varchar( 255 ),
					active 	tinyint( 1 ),
					PRIMARY KEY (id)
				)";

		return $this->_db->query( $sql );
	}

	public function Insert( &$news )
	{
		$news[ 'ip' ]	= $_SERVER[ 'REMOTE_ADDR' ];
		$news[ 'date' ]	= Functions::Timestamp();

		return $this->_db->insert( 'news', $news );
	}

	public function Update( $news )
	{
		return $this->_db->query( 'UPDATE news SET title = ?, news = ?, active = ? WHERE id = ?', $news[ 'title' ], $news[ 'news' ], $news[ 'active' ], $news[ 'id' ] );
	}

	public function Load( $newsid, &$news )
	{
		return $this->_db->single( 'SELECT * FROM news WHERE id = ?', $news, $newsid );
	}

	public function List_Load( &$news )
	{
		return $this->_db->select( 'SELECT * FROM news ORDER BY id DESC', $news );
	}

	public function Delete( $news_id )
	{
		return $this->_db->query( 'DELETE FROM news WHERE id = ?', $news_id );
	}
}
