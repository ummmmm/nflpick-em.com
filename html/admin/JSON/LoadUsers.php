<?php
class JSON_LoadUsers implements iJSON
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
		return array( 'admin' => true );
	}

	public function execute()
	{
		$sort		= Functions::Post( 'sort' );
		$direction	= Functions::Post( 'direction' );
		
		if ( !$this->_Load_Users( $sort, $direction, $users ) )
		{
			return JSON_Response_Error();
		}
		
		foreach( $users as &$loaded_user )
		{
			$loaded_user[ 'last_on' ] 			= Functions::FormatDate( $loaded_user[ 'last_on' ] );
			$loaded_user[ 'current_place' ] 	= Functions::Place( $loaded_user[ 'current_place' ] ); 
		}
		
		return $this->_setData( $users );
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

	// Helper functions

	private function _Load_Users( $sort, $direction, &$users )
	{
		$db_weeks	= new Weeks( $this->_db );
		$current	= $db_weeks->Current();
		$direction 	= ( $direction === 'asc' ) ? 'ASC' : 'DESC';
		$sql 		= "SELECT
							u.*,
							CONCAT( u.fname, ' ', u.lname ) AS name,
							( SELECT COUNT( * ) FROM failed_logins WHERE email = u.email ) AS failed_logins,
							( SELECT COUNT( * ) FROM sessions WHERE userid = u.id ) AS active_sessions,
							( SELECT COUNT( * ) FROM picks p WHERE p.user_id = u.id AND p.week = ? AND p.picked = 0 ) AS remaining
						FROM
							users u
						ORDER BY
							{$sort} {$direction}";
		return $this->_db->select( $sql, $users, $current );
	}
}
