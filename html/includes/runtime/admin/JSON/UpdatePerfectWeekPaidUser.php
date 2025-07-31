<?php

class JSON_UpdatePerfectWeekPaidUser extends JSONAdminAction
{
	public function execute()
	{
		$week_id				= Functions::Post( 'user_id' );
		$user_id				= Functions::Post( 'week_id' );
		$db_users				= $this->db()->users();
		$db_weeks				= $this->db()->weeks();
		$db_perfect_week_paid	= $this->db()->perfectweekpaid();

		if ( !$db_users->Load( $user_id, $loaded_user ) )
		{
			return $this->setError( array( 'NFL-USERS_UPDATE-1', 'Failed to load user' ) );
		}

		if ( $loaded_user[ 'pw_opt_out' ] )
		{
			return $this->setError( array( 'NFL-USERS_UPDATE-2', 'User is opted out of the perfect week pool' ) );
		}

		if ( !$db_weeks->Load( $week_id, $loaded_week ) )
		{
			return $this->setError( array( 'NFL-USERS_UPDATE-3', 'Failed to load week' ) );
		}

		if ( $db_perfect_week_paid->Load( $week_id, $user_id, $null ) )	$result = $db_perfect_week_paid->Delete( $week_id, $user_id );
		else															$result = $db_perfect_week_paid->Insert( $week_id, $user_id );

		if ( !$result )
		{
			return $this->setDBError();
		}

		return true;
	}
}
