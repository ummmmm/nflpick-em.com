<?php

class Screen_WeeklyRecords extends Screen_Admin
{
	public function content()
	{
		$week_id	= $this->input()->value_GET_int( 'week' );
		$user_id	= $this->input()->value_GET_int( 'user' );
		$db_weeks	= $this->db()->weeks();
		$db_users	= $this->db()->users();

		if ( $week_id == 0 )
		{
			return $this->_WeekList();
		}

		if ( !$db_weeks->Load( $week_id, $week ) )
		{
			return Functions::Information( 'Error', 'Invalid week.' );
		}

		if ( $user_id == 0 )
		{
			return $this->_WeekPicks( $week );
		}

		if ( !$db_users->Load( $user_id, $user ) )
		{
			return Functions::Information( 'Error', 'Invalid user.' );
		}

		return $this->_WeekUserPicks( $week, $user );
	}

	private function _WeekList()
	{
		$db_weeks = $this->db()->weeks();
		$db_weeks->List_Load_Locked( $weeks );

		print '<h1>Weekly Records</h1>';

		if ( count( $weeks ) === 0 )
		{
			print( '<p>No locked weeks</p>' );
		}

		foreach ( $weeks as &$week )
		{
			printf( '<p><a href="?view=admin&screen=weekly_records&week=%d">Week %d</a></p>', $week[ 'id' ], $week[ 'id' ] );
		}

		return true;
	}

	private function _WeekPicks( &$week )
	{
		$db_games			= $this->db()->games();
		$db_users			= $this->db()->users();
		$db_picks			= $this->db()->picks();
		$db_weekly_records	= $this->db()->weeklyrecords();

		$db_users->List_Load( $users );
		$db_games->List_Load_Week( $week[ 'id' ], $games );

		$games_in_progress = false;

		foreach ( $games as &$game )
		{
			if ( !$game[ 'final' ] )
			{
				$games_in_progress = true;
				break;
			}
		}

		print '<h1>Weekly Records</h1>';

		if ( $games_in_progress )
		{
			print( '<p>Weekly records cannot be updated until all games are final</p>' );
			return true;
		}

		foreach ( $users as &$user )
		{
			$missing_count = $db_picks->Missing( $user[ 'id' ], $week[ 'id' ] );

			if ( $missing_count === 0 )
			{
				continue;
			}

			$db_weekly_records->Load_User_Week( $user[ 'id' ], $week[ 'id' ], $weekly_record );

			printf( '<p><a href="?view=admin&screen=weekly_records&week=%d&user=%d">%s</a>%s</p>', $week[ 'id' ], $user[ 'id' ], htmlentities( $user[ 'name' ] ), ( $weekly_record[ 'manual' ] ? ' - <span style="color:red;">Manual</span>' : '' ) );
		}

		return true;
	}

	private function _WeekUserPicks( &$week, &$user )
	{
		$db_picks			= $this->db()->picks();
		$db_weekly_records	= $this->db()->weeklyrecords();

		$db_weekly_records->Load_User_Week( $user[ 'id' ], $week[ 'id' ], $weekly_record );

		$missing_count = $db_picks->Missing( $user[ 'id' ], $week[ 'id' ] );

		printf( '<h1>%s - Week %d</h1>', htmlentities( $user[ 'name' ] ), $week[ 'id' ] );
		printf( '<p>Missing %d picks', $missing_count );
		print( '<table>' );
		printf( '<tr><td><b>Wins:</b></td><td><input type="number" length="5" id="wins" value="%d" /></td></tr>', $weekly_record[ 'wins' ] );
		printf( '<tr><td><b>Losses:</b></td><td><input type="number" length="5" id="losses" value="%d" /></td></tr>', $weekly_record[ 'losses' ] );
		print( '</table>' );
		printf( '<input type="submit" onclick="$.fn.update_weekly_records( %d, %d );" value="Update" />', $user[ 'id' ], $week[ 'id' ] );

		return true;
	}
}
