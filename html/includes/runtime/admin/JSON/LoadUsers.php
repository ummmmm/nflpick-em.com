<?php

class JSON_LoadUsers extends JSONAdmin
{
	public function execute()
	{
		$sort = $this->input()->value_str( 'sort' );

		$this->_Load_Users( $sort, $users );

		foreach( $users as &$loaded_user )
		{
			$loaded_user[ 'last_on' ] = Functions::FormatDate( $loaded_user[ 'last_on' ] );
		}

		return $this->setData( $users );
	}

	// Helper functions

	private function _Load_Users( $sort, &$users )
	{
		$current	= $this->db()->weeks()->Current();
		$direction	= 'ASC';

		if ( str_starts_with( $sort, '-' ) )
		{
			$sort		= substr( $sort, 1 );
			$direction	= 'DESC';
		}

		switch ( $sort )
		{
			case 'name'				:
			case 'current_place'	:
			case 'last_on'			:
			case 'paid'				:
			case 'pw_opt_in'		:
			case 'failed_logins'	:
			case 'active_sessions'	:
			case 'remaining'		:
			{
				break;
			}
			default					:
			{
				throw new NFLPickEmException( 'Invalid sort' );
			}
		}

		$sql 		= "SELECT
							u.*,
							CONCAT( u.fname, ' ', u.lname ) AS name,
							( SELECT COUNT( * ) FROM failed_logins WHERE email = u.email ) AS failed_logins,
							( SELECT COUNT( * ) FROM sessions WHERE userid = u.id AND expires >= ? ) AS active_sessions,
							( ( SELECT COUNT( * ) FROM games g WHERE week = ? ) - ( SELECT COUNT( * ) FROM picks p WHERE p.user_id = u.id AND p.week = ? ) ) AS remaining
						FROM
							users u
						ORDER BY
							{$sort} {$direction}, name ASC";

		$this->db()->select( $sql, $users, time(), $current, $current );
	}
}
