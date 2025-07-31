<?php

class Screen_Default extends Screen
{
	public function content()
	{
		$settings	= $this->settings();
		$action		= Functions::Get( 'action' );

		$total = ( $action === 'viewall' ) ? 1000 : $settings[ 'max_news' ];

		$news_count = $this->_NewsLoad_List( $news_list, $total );

		if ( $news_count === false )
		{
			return $this->setDBError();
		}

		if ( $news_count == 0 )
		{
			$date = new DateTime( 'now', new DateTimeZone( 'America/Los_Angeles' ) );
			print '<h1>No News</h1>';
			print '<div class="descr">' . $date->format( 'M d, Y' ) . ' by David Carver</div>';
			print '<p>There seems to be no news in the system right now and hopefully we\'ll have some posted soon!</p>';

			return true;
		}

		foreach( $news_list as $news )
		{
			$date = new DateTime();
			$date->setTimezone( new DateTimeZone( 'America/Los_Angeles' ) );
			$date->setTimestamp( $news[ 'date' ] );

			print '<h1>' . $news[ 'title' ] . '</h1>';
			print '<div class="descr">' . $date->format( 'M d, Y' ) . ' by ' . htmlentities( $news[ 'name' ] ) . '</div>';
			print '<p>' . nl2br( $news[ 'news' ] ) . '</p>';
		}

		if ( $action != "viewall" )
		{
			print '<div align="center"><a href="?action=viewall" title="View All News">View All Posted News</a></div>';
		}

		return true;
	}

	private function _NewsLoad_List( &$news, &$total )
	{
		return $this->db()->select( 'SELECT n.*, CONCAT( u.fname, \' \', u.lname ) AS name FROM news n, users u WHERE n.active = 1 AND n.user_id = u.id ORDER BY date DESC LIMIT ?', $news, $total );
	}
}
