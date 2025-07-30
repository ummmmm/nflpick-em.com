<?php

class Screen_PerfectWeeks extends Screen_Admin
{
	public function content()
	{
		$week_id	= Functions::Get( 'week' );
		$db_weeks	= $this->db()->weeks();

		if ( $week_id == '' )
		{
			return $this->_WeekList();
		}

		if ( !$db_weeks->Load( $week_id, $week ) )
		{
			return Functions::Information( 'Error', 'Invalid week.' );
		}

		return $this->_PerfectWeek( $week );
	}

	private function _WeekList()
	{
		$db_weeks = $this->db()->weeks();

		if ( $db_weeks->List_Load_Locked( $weeks ) === false )
		{
			return $this->setDBError();
		}

		$count = $this->_PerfectWeekList_Load( $weeks );

		print '<h1>Perfect Weeks</h1>';

		if ( $count == 0 )
		{
			print( 'There are no perfect weeks so far this year.' );
			return true;
		}

		foreach ( $weeks as &$week )
		{
			printf( '<p><a href="?view=admin&screen=perfect_weeks&week=%d">Week %d</a></p>', $week[ 'id' ], $week[ 'id' ] );
		}

		return true;
	}

	private function _PerfectWeek( &$week )
	{
		printf( '<h1>Week %d</h1>', $week[ 'id' ] );

		$count = $this->_PerfectWeekUserList_Load( $week[ 'id' ], $users );

		if ( $count == 0 )
		{
			print( 'There are no perfect week winners for this week' );
			return true;
		}

		printf( '<h3>Winners (%d)</h3>', $count );
		print( '<ol>' );
		foreach ( $users as $user )
		{
			printf( '<li>%s %s</li>', htmlentities( $user[ 'fname' ] ), htmlentities( $user[ 'lname' ] ) );
		}
		print( '</ol><br /><br />' );

		$count = $this->_PerfectWeekUserOptOutList_Load( $week[ 'id' ], $users );
		printf( '<h3>Winners Opt-Out(%d)</h3>', $count );

		if ( $count == 0 )
		{
			print( '&lt;None&gt;' );
		}
		else
		{
			print( '<ol>' );
			foreach ( $users as $user )
			{
				printf( '<li>%s %s</li>', htmlentities( $user[ 'fname' ] ), htmlentities( $user[ 'lname' ] ) );
			}
			print( '</ol>' );
		}

		print( '<br /><br />' );

		$count = $this->_PerfectWeekUserPaidList_Load( $week[ 'id' ], $users );
		printf( '<h3>Paid (%d)</h3>', $count );

		if ( $count == 0 )
		{
			print( '&lt;None&gt;' );
		}
		else
		{
			print( '<ol>' );
			foreach ( $users as $user )
			{
				printf( '<li><a href="javascript:;" onclick="$.fn.update_perfect_week_paid( %d, %d );">%s %s</a></li>', $user[ 'id' ], $week[ 'id' ], htmlentities( $user[ 'fname' ] ), htmlentities( $user[ 'lname' ] ) );
			}
			print( '</ol>' );
		}

		print( '<br /><br />' );

		$count = $this->_PerfectWeekUserUnpaidList_Load( $week[ 'id' ], $users );
		printf( '<h3>Unpaid (%d)</h3>', $count );

		if ( $count == 0 )
		{
			print( '&lt;None&gt;' );
		}
		else
		{
			print( '<ol>' );
			foreach ( $users as $user )
			{
				printf( '<li><a href="javascript:;" onclick="$.fn.update_perfect_week_paid( %d, %d );">%s %s</a></li>', $user[ 'id' ], $week[ 'id' ], htmlentities( $user[ 'fname' ] ), htmlentities( $user[ 'lname' ] ) );
			}
			print( '</ol>' );
		}

		return true;
	}

	private function _PerfectWeekList_Load( &$weeks )
	{
		return $this->db()->connection()->select( 'SELECT
										*
									FROM
										weeks w
									WHERE
									(
										SELECT
											COUNT( * )
										FROM
											weekly_records wr
										WHERE
											wr.week_id = w.id AND
											wr.wins =
											(
												SELECT
													COUNT( * )
												FROM
													games g
												WHERE
													g.week = wr.week_id
											)
									) > 0', $weeks );
	}

	private function _PerfectWeekUserList_Load( $week_id, &$users )
	{
		return $this->db()->connection()->select( 'SELECT u.* FROM users u, weekly_records wr WHERE u.pw_opt_out = 0 AND u.id = wr.user_id AND wr.week_id = ? AND wr.wins = ( SELECT COUNT( * ) FROM games g WHERE g.week = wr.week_id ) AND wr.losses = 0 ORDER BY u.fname, u.lname', $users, $week_id );
	}

	private function _PerfectWeekUserOptOutList_Load( $week_id, &$users )
	{
		return $this->db()->connection()->select( 'SELECT u.* FROM users u, weekly_records wr WHERE u.pw_opt_out = 1 AND u.id = wr.user_id AND wr.week_id = ? AND wr.wins = ( SELECT COUNT( * ) FROM games g WHERE g.week = wr.week_id ) AND wr.losses = 0 ORDER BY u.fname, u.lname', $users, $week_id );
	}

	private function _PerfectWeekUserPaidList_Load( $week_id, &$users )
	{
		return $this->db()->connection()->select( 'SELECT u.* FROM users u, perfect_week_paid pwp WHERE u.id = pwp.user_id AND pwp.week_id = ? ORDER BY u.fname, u.lname', $users, $week_id );
	}

	private function _PerfectWeekUserUnpaidList_Load( $week_id, &$users )
	{
		return $this->db()->connection()->select( 'SELECT
										u.*
									FROM
										users u
										LEFT OUTER JOIN perfect_week_paid pwp ON u.id = pwp.user_id AND pwp.week_id = ?,
										weekly_records wr
									WHERE
										u.pw_opt_out	= 0		AND
										wr.user_id		= u.id	AND
										wr.week_id		= ?		AND
										wr.losses		<> 0	AND
										pwp.user_id IS NULL', $users, $week_id, $week_id );
	}
}
