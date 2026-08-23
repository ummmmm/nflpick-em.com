<?php

require( 'includes/classes/API.php' );

class Screen_UpdateScores extends Screen_Admin
{
	public function content()
	{
		print '<h1>Update Scores</h1>';

		$api = new API( $this->db() );
		$api->update_scores()

		return true;
	}
}
