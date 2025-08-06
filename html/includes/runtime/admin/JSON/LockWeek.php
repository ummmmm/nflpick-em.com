<?php

class JSON_LockWeek extends JSONAdminAction
{
	public function execute()
	{
		$db_weeks	= $this->db()->weeks();
		$week_id 	= Functions::Post( 'week_id' );

		if ( !$db_weeks->Load( $week_id, $week ) )
		{
			throw new NFLPickEmException( 'Week does not exist' );
		}

		$week[ 'locked' ] = ( int ) !$week[ 'locked' ];

		$db_weeks->Update( $week );

		return true;
	}
}
