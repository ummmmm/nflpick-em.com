<?php

class JSON_HighlightPicks implements iJSON
{
	public function __construct( Database &$db, Authentication &$auth, JSON &$json )
	{
		$this->_db		= $db;
		$this->_auth	= $auth;
		$this->_json	= $json;
	}

	public function requirements()
	{
		return array( 'user' => true );
	}

	public function execute()
	{
		$db_weeks	= new Weeks( $this->_db );
		$userid 	= Functions::Post( 'userid' );
		$week		= Functions::Post( 'week' );
		
		if ( !$db_weeks->IsLocked( $week ) )
		{
			return $this->_json->setError( array( "#ERROR#", sprintf( "Week '%d' has not been locked yet.", $week ) ) );
		}

		$diff_picks = array();

		if ( $userid != 0 )
		{	
			if ( $userid == $this->_auth->getUserID() )
			{
				return $this->_json->setError( array( "#Error#", "You cannot view picks you have different from yourself" ) );
			}
			else if ( ( $count = $this->_Load_Different_Picks( $this->_auth->getUserID(), $userid, $week, $picks ) ) === false )
			{
				return $this->_json->DB_Error();
			}
			else if ( $count === 0 )
			{
				return true;
			}

			array_push( $diff_picks, array( 'userid' => $userid, 'games' => explode( ',', $picks[ 'game_ids' ] ) ) );
		}
		else
		{
			if ( ( $count = $this->_Load_Different_Picks( $this->_auth->getUserID(), NULL, $week, $users_picks ) ) === false )
			{
				return $this->_json->DB_Error();
			}
			else if ( $count === 0 )
			{
				return true;
			}

			foreach( $users_picks as &$user_picks )
			{
				array_push( $diff_picks, array( 'userid' => $user_picks[ 'user_id' ], 'games' => explode( ',', $user_picks[ 'game_ids' ] ) ) );
			}
		}

		return $this->_json->setData( $diff_picks );
	}

	// Helper functions

	private function _Load_Different_Picks( $userid1, $userid2, $weekid, &$picks )
	{
		$sign 	= '=';
		$single	= true;

		if ( is_null( $userid2 ) )
		{
			$sign 		= '<>';
			$userid2 	= $userid1;
			$single		= false;
		}

		$query = "SELECT
					GROUP_CONCAT( p2.game_id ) AS game_ids,
					p2.user_id
				  FROM
					picks p1,
					picks p2
				  WHERE
					p1.user_id 		= 		? 			AND
					p2.user_id 		{$sign} ? 			AND
					p1.week 		= 		? 			AND
					p1.game_id 		= 		p2.game_id 	AND
					p2.picked		= 		1			AND
					p1.winner_pick	<> 		p2.winner_pick
				  GROUP BY
					p2.user_id";

		if ( $single )	$result = $this->_db->single( $query, $picks, $userid1, $userid2, $weekid );
		else			$result = $this->_db->select( $query, $picks, $userid1, $userid2, $weekid );

		return $result;
	}
}
