<?php

class Screen_Leaderboard extends Screen
{
	public function requirements()
	{
		return array( "user" => true );
	}

	public function content()
	{
		print "<h1>Leaderboard</h1>\n";
	
		$count = $this->_Leaderboard( $leaders );
		
		if ( $count === false )
		{
			return $this->setDBError();
		}
		
		if ( $count === 0 )
		{		
			print "<p>The leaderboard is currently empty.</p>";
			
			return true;
		}
		
		foreach ( $leaders as $leader )
		{
			printf( "<p>%s %s - %d Wins - %d Losses</p>\n", Functions::Place( $leader[ 'current_place' ] ), htmlentities( ucwords( $leader[ 'name' ] ) ), $leader[ 'wins' ], $leader[ 'losses' ] );
		}
		
		return true;
	}

	private function _Leaderboard( &$users )
	{
		return $this->_db->select( 'SELECT *, CONCAT( fname, \' \', lname ) AS name FROM users ORDER BY current_place, name, id', $users );
	}
}
