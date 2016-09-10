<?php

class JSON_LockWeek extends JSON
{
	public function requirements()
	{
		return array( 'admin' => true, 'token' => true );
	}

	public function execute()
	{
		$db_weeks	= new Weeks( $this->_db );
		$week_id 	= Functions::Post( 'week_id' );
		$count 		= $db_weeks->Load( $week_id, $week );

		if ( $count === false )
		{
			return $this->setDBError();
		}

		if ( $count === 0 )
		{
			return $this->setError( array( "#Error#", "Failed to load week" ) );
		}

		$week[ 'locked' ] = ( $week[ 'locked' ] === 1 ) ? 0 : 1;

		if ( !$db_weeks->Update( $week ) )
		{
			return $this->setDBError();
		}

		return true;
	}
}
