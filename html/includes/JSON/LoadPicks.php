<?php

class JSON_LoadPicks implements iJSON
{
	private $_db;
	private $_auth;
	private $_error;
	private $_data;

	public function __construct( Database &$db, Authentication &$auth )
	{
		$this->_db		= $db;
		$this->_auth	= $auth;
		$this->_data	= null;
		$this->_error	= array();
	}

	public function requirements()
	{
		return array( 'user' => true );
	}

	public function execute()
	{
		$db_games	= new Games( $this->_db );
		$db_picks	= new Picks( $this->_db );
		$db_weeks	= new Weeks( $this->_db );
		$week_id	= Functions::Post( 'week_id' );

		$count = $db_weeks->Load( $week_id, $week );

		if ( $count === false )
		{
			return $this->_setError( $db_weeks->Get_Error() );
		}

		if ( $count === 0 )
		{
			return $this->_setError( array( '#Error#', 'Failed to load week' ) );
		}

		$count = $db_games->List_Load_Week( $week_id, $week[ 'games' ] );

		foreach( $week[ 'games' ] as &$game )
		{
			$count = $db_picks->Load_User_Game( $this->_auth->userID, $game[ 'id' ], $pick );

			if ( $count === false )
			{
				return $this->_setError( $db_picks->Get_Error() );
			}

			$now	= new DateTime();
			$date 	= new DateTime();
			$date->setTimestamp( $game[ 'date' ] );

			$game[ 'past' ] 		 	= ( $now > $date ) ? true : false;
			$game[ 'time_formatted' ] 	= $date->format( 'h:i a' );
			$game[ 'date_formatted' ] 	= $date->format( 'F d, Y' );
			$game[ 'date_javascript' ]	= $date->format( 'F d, Y h:i:s' );
			$game[ 'pick' ] 			= $pick;
		}

		return $this->_setData( $week );
	}

	public function getData()
	{
		return $this->_data;
	}

	public function getError()
	{
		return $this->_error;
	}

	public function _setData( $data )
	{
		$this->_data = $data;
		return true;
	}

	private function _setError( $error )
	{
		$this->_error = $error;
		return false;
	}
}
