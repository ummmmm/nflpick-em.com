<?php

class JSON_UpdatePerfectWeekPaidUser extends JSONAdminAction
{
	public function execute()
	{
		$week_id				= $this->input()->value_int( 'user_id' );
		$user_id				= $this->input()->value_int( 'week_id' );
		$db_users				= $this->db()->users();
		$db_weeks				= $this->db()->weeks();
		$db_perfect_week_paid	= $this->db()->perfectweekpaid();

		if ( !$db_users->Load( $user_id, $loaded_user ) )		throw new NFLPickEmException( 'User does not exist' );
		else if ( $loaded_user[ 'pw_opt_out' ] )				throw new NFLPickEmException( 'User is opted out of the perfect week pool' );
		else if ( !$db_weeks->Load( $week_id, $loaded_week ) )	throw new NFLPickEmException( 'Week does not exist' );

		if ( $db_perfect_week_paid->Load( $week_id, $user_id, $null ) )	$db_perfect_week_paid->Delete( $week_id, $user_id );
		else															$db_perfect_week_paid->Insert( $week_id, $user_id );

		return true;
	}
}
