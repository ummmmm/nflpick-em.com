<?php

class Screen_WeeklyRecords extends Screen_Admin
{
	public function content()
	{
		$week_id = Functions::Get( 'week' );
		$user_id = Functions::Get( 'user' );

		if ( $week_id == '' )
		{
			return $this->_WeekList();
		}

		if ( !Validation::Week( $week_id ) )
		{
			return Functions::Information( 'Error', 'Invalid week.' );
		}

		if ( $user_id == '' )
		{
			return $this->_WeekPicks( $week_id );
		}

		return $this->_WeekUserPicks( $week_id, $user_id );
	}

	private function _WeekList()
	{
		$db_weeks = new Weeks( $this->_db );

		if ( $db_weeks->List_Load_Locked( $weeks ) === false )
		{
			return $this->setDBError();
		}

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

	private function _WeekPicks( $week_id )
	{
		$db_games			= new Games( $this->_db );
		$db_weeks			= new Weeks( $this->_db );
		$db_users			= new Users( $this->_db );
		$db_picks			= new Picks( $this->_db );
		$db_weekly_records	= new Weekly_Records( $this->_db );

		if ( !$db_weeks->Load( $week_id, $week ) )
		{
			return $this->setDBError();
		}

		if ( !$db_users->List_Load( $users ) )
		{
			return $this->setDBError();
		}

		if ( !$db_games->List_Load_Week( $week_id, $games ) )
		{
			return $this->setDBError();
		}

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
			$missing_count = $db_picks->Missing( $user[ 'id' ], $week_id );

			if ( $missing_count === false )
			{
				return $this->setDBError();
			}

			if ( $missing_count === 0 )
			{
				continue;
			}

			if ( !$db_weekly_records->Load_User_Week( $user[ 'id' ], $week_id, $weekly_record ) )
			{
				return $this->setDBError();
			}

			printf( '<p><a href="?view=admin&screen=weekly_records&week=%d&user=%d">%s</a>%s</p>', $week_id, $user[ 'id' ], htmlentities( $user[ 'name' ] ), ( $weekly_record[ 'manual' ] ? ' - <span style="color:red;">Manual</span>' : '' ) );
		}

		return true;
	}

	private function _WeekUserPicks( $week_id, $user_id )
	{
		$db_picks			= new Picks( $this->_db );
		$db_weeks			= new Weeks( $this->_db );
		$db_users			= new Users( $this->_db );
		$db_weekly_records	= new Weekly_Records( $this->_db );

		if ( !$db_weeks->Load( $week_id, $week ) || !$db_users->Load( $user_id, $user ) || !$db_weekly_records->Load_User_Week( $user_id, $week_id, $weekly_record ) )
		{
			return $this->setDBError();
		}

		$missing_count = $db_picks->Missing( $user_id, $week_id );

		if ( $missing_count === false )
		{
			return $this->setDBError();
		}

		printf( '<h1>%s - Week %d</h1>', htmlentities( $user[ 'name' ] ), $week_id );
		printf( '<p>Missing %d picks', $missing_count );
		print( '<table>' );
		printf( '<tr><td><b>Wins:</b></td><td><input type="number" length="5" id="wins" value="%d" /></td></tr>', $weekly_record[ 'wins' ] );
		printf( '<tr><td><b>Losses:</b></td><td><input type="number" length="5" id="losses" value="%d" /></td></tr>', $weekly_record[ 'losses' ] );
		print( '</table>' );
		printf( '<input type="submit" onclick="$.fn.update_weekly_records( \'%d\', \'%d\' );" value="Update" />', $user_id, $week_id );

		return true;
	}
}
