<?php

class Screen_Leaderboard extends Screen_User
{
	public function content()
	{
		print "<h1>Leaderboard</h1>\n";

		$this->_Leaderboard( $leaders );

		if ( count( $leaders ) == 0 )
		{
			print "<p>The leaderboard is currently empty.</p>";

			return true;
		}

		print( '<table width="100%">' );
		print( '<tr>' );
		print( '<th align="left">Place</th><th align="left">Player</th><th align="left">Wins</th><th align="left">Losses</th>' );
		print( '</tr>' );

		foreach ( $leaders as $leader )
		{
			print( '<tr>' );

			if ( $leader[ 'current_place' ] <= 5 )	printf( '<td style="color:green;font-weight:bold;">%s</td>', Functions::Place( $leader[ 'current_place' ] ) );
			else									printf( '<td>%s</td>', Functions::Place( $leader[ 'current_place' ] ) );

			printf( '<td>%s%s</td>', htmlentities( ucwords( $leader[ 'name' ] ) ), $leader[ 'pw_opt_out' ] ? '*' : '' );
			printf( '<td>%d</td>', $leader[ 'wins' ] );
			printf( '<td>%d</td>', $leader[ 'losses' ] );
			print( '</tr>' );
		}
		print( '</table>' );

		print( '<br /><br />' );
		print( '* Player has opted out of the perfect week pool' );

		return true;
	}

	private function _Leaderboard( &$users )
	{
		$this->db()->select( 'SELECT *, CONCAT( fname, \' \', lname ) AS name FROM users ORDER BY current_place, name, id', $users );
	}
}
