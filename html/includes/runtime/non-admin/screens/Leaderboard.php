<?php

class Screen_Leaderboard implements iScreen
{
	public function __construct( Database &$db, Authentication &$auth, Screen &$screen )
	{
		$this->_db		= $db;
		$this->_auth	= $auth;
		$this->_screen	= $screen;
	}

	public function requirements()
	{
		return array();
	}

	public function content()
	{
		print "<h1>Leaderboard</h1>\n";
	
		$count = $this->_Leaderboard( $leaders );
		
		if ( $count === false )
		{
			return $this->_screen->setDBError();
		}
		
		if ( $count === 0 )
		{		
			print "<p>The leaderboard is currently empty.</p>";
			
			return true;
		}
		
		foreach ( $leaders as $leader )
		{
			printf( "<p>%s %s - %d Wins - %s Losses</p>\n", Functions::Place( $leader[ 'current_place' ] ), htmlentities( ucwords( $leader[ 'name' ] ) ), $leader[ 'wins' ], $leader[ 'losses' ] );
		}
		
		return true;
	}

	private function _Leaderboard( &$users )
	{
		return $this->_db->select( 'SELECT *, CONCAT( fname, \' \', lname ) AS name FROM users ORDER BY current_place, fname', $users );
	}
}
