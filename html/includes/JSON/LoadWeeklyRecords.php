<?php

class JSON_LoadWeeklyRecords implements iJSON
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
		if ( $this->_Weeks( $loaded_weeks ) === false || $this->_Users( $loaded_users ) === false )
		{
			return $this->_json->DB_Error();
		}
		
		foreach( $loaded_users as &$loaded_user )
		{
			$loaded_user[ 'weeks' ] = array();
			
			foreach( $loaded_weeks as $loaded_week )
			{
				if ( $this->_Wins( 		$loaded_user[ 'id' ], $loaded_week[ 'id' ], $wins )	 === false ||
					 $this->_Losses( 	$loaded_user[ 'id' ], $loaded_week[ 'id' ], $losses )	 === false )
				{
					return $this->_json->DB_Error();
				}

				if ( $wins[ 'total' ] === 0 && $losses[ 'total' ] === 0 )
				{
					if ( !Functions::Worst_Record_Calculated( $this->_db, $loaded_week[ 'id' ], $record ) )
					{
						return $this->_json->DB_Error();
					}

					$wins[ 'total' ] 	= $record[ 'wins' ];
					$losses[ 'total' ]	= $record[ 'losses' ];
				}
				
				array_push( $loaded_user[ 'weeks' ], array( 'id' => $loaded_week[ 'id' ], 'wins' => $wins[ 'total' ], 'losses' => $losses[ 'total' ] ) );
			}
		}
		
		return $this->_json->setData( $loaded_users );
	}

	// Helper functions

	function _Wins( $user_id, $week_id, &$record )
	{
		return $this->_db->single( 'SELECT COUNT( p.id ) AS total FROM picks p, games g WHERE p.winner_pick = g.winner AND p.user_id = ? AND p.week = ? AND p.game_id = g.id', $record, $user_id, $week_id );
	}

	function _Losses( $user_id, $week_id, &$record )
	{
		return $this->_db->single( 'SELECT COUNT( p.id ) AS total FROM picks p, games g WHERE p.user_id = ? AND p.week = ? AND p.game_id = g.id AND ( p.winner_pick = g.loser OR p.picked = 0 )', $record, $user_id, $week_id );
	}

	function _Users( &$users )
	{
		return $this->_db->select( 'SELECT id, CONCAT( fname, \' \', lname ) AS name, wins AS total_wins, losses AS total_losses FROM users ORDER BY fname ASC, lname ASC', $users );
	}

	function _Weeks( &$weeks )
	{
		return $this->_db->select( 'SELECT w.id, ( SELECT COUNT( g.id ) FROM games g WHERE g.week = w.id AND g.winner <> 0 ) AS total_games FROM weeks w WHERE locked = 1 ORDER BY id ASC', $weeks );
	}
}
