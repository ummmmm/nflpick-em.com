<?php

class JSON_LoadWeeklyRecords extends JSONUser
{
	public function execute()
	{
		$output				= array();
		$db_users			= $this->db()->users();
		$db_weeks			= $this->db()->weeks();
		$db_weekly_records	= $this->db()->weeklyrecords();

		if ( !$db_users->List_Load( $users ) )
		{
			return $this->setDBError();
		}

		if ( $db_weeks->List_Load_Locked( $weeks ) === false )
		{
			return $this->setDBError();
		}

		foreach ( $users as &$user )
		{
			$data_entry						= array();
			$data_entry[ 'id' ]				= $user[ 'id' ];
			$data_entry[ 'name' ]			= $user[ 'name' ];
			$data_entry[ 'total_wins' ]		= $user[ 'wins' ];
			$data_entry[ 'total_losses' ]	= $user[ 'losses' ];
			$data_entry[ 'weeks' ]			= array();

			foreach ( $weeks as &$week )
			{
				if ( !$db_weekly_records->Load_User_Week( $user[ 'id' ], $week[ 'id' ], $weekly_record ) )
				{
					return $this->setError( 'Unable to load all user records' );
				}

				$week_entry				= array();
				$week_entry[ 'id' ]		= $weekly_record[ 'week_id' ];
				$week_entry[ 'wins' ]	= $weekly_record[ 'wins' ];
				$week_entry[ 'losses' ]	= $weekly_record[ 'losses' ];
				$week_entry[ 'manual' ]	= $weekly_record[ 'manual' ];

				array_push( $data_entry[ 'weeks' ], $week_entry );
			}

			array_push( $output, $data_entry );
		}

		return $this->setData( $output );
	}
}
