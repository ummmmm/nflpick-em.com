<?php

class Users
{
	public $id 			= 0;
	public $account 	= array();
	public $logged_in 	= false;
	public $token 		= null;

	private $_db;

	public function __construct( Database &$db )
	{
		$this->_db = $db;
		$this->ValidateSession();
	}

	public function Create()
	{
		$sql = "CREATE TABLE users
				(
					id 					int( 11 ) AUTO_INCREMENT,
					fname 				varchar( 50 ),
					lname 				varchar( 50 ),
					email 				varchar( 255 ),
					password 			varchar( 255 ),
					admin 				tinyint( 1 ),
					sign_up 			datetime,
					last_on 			datetime,
					wins 				int( 11 ),
					losses 				int( 11 ),
					paid 				tinyint( 1 ),
					current_place 		int( 11 ),
					email_preference 	tinyint( 1 ),
					force_password 		tinyint( 1 ),
					PRIMARY KEY ( id )
				)";

		return $this->_db->query( $sql );
	}

	public function List_Load( &$users )
	{
		return $this->_db->select( 'SELECT *, CONCAT( fname, \' \', lname ) AS name FROM users ORDER BY fname', $users );
	}

	public function Load( $userid, &$user )
	{
		return $this->_db->single( 'SELECT *, CONCAT( fname, \' \', lname ) AS name FROM users WHERE id = ?', $user, $userid );
	}

	public function Load_Email( $email, &$user )
	{
		return $this->_db->single( 'SELECT *, CONCAT( fname, \' \', lname ) AS name FROM users WHERE email = ?', $user, $email );
	}

	public function Delete( $user_id )
	{
		$db_picks 			= new Picks( $this->_db );
		$db_polls			= new Polls( $this->_db );
		$db_reset_password	= new Reset_Passwords( $this->_db );
		$db_sent_picks		= new Sent_Picks( $this->_db );

		if ( !$db_picks->Delete_User( $user_id ) 			||
			 !Sessions::Delete_User( $this->_db, $user_id ) ||
			 !$db_polls->Delete_User( $user_id )			||
			 !$db_reset_password->Delete_User( $user_id )	||
			 !$db_sent_picks->Delete_User( $user_id )		||
			 !$this->Delete_LowLevel( $user_id ) )
		{
			return false;
		}

		return true;
	}

	private function Delete_LowLevel( $userid )
	{
		return $this->_db->query( 'DELETE FROM users WHERE id = ?', $userid );
	}

	private function UserActive_Update()
	{
		$date = Functions::Timestamp();

		return $this->_db->query( 'UPDATE users SET last_on = ? WHERE id = ?', $date, $this->id );
	}

	public function CreateSession()
	{
		if ( !$this->id )
		{
			return false;
		}

		$cookieid	= sha1( session_id() );
		$token		= sha1( uniqid( rand(), TRUE ) );

		setcookie( 'session', $cookieid, time() + 60 * 60 * 24 * 30, INDEX, '', false, true );

		if ( !Sessions::Insert( $this->_db, array( 'token' => $token, 'cookieid' => $cookieid, 'userid' => $this->id ) ) )
		{
			return false;
		}

		return true;
	}

	public function Insert( &$user )
	{
		$user[ 'password' ] = Functions::HashPassword( $user[ 'password' ] );

		if ( !$this->_db->insert( 'users', $user ) )
		{
			return false;
		}

		$this->id 		= $this->_db->insert_id;
		$user[ 'id' ] 	= $this->_db->insert_id;

		return true;
	}

	private function ValidateSession()
	{
		$cookieid 	= Functions::Cookie( 'session' );
		$count 		= $this->_db->single( 'SELECT s.userid, s.token FROM users u, sessions s WHERE s.cookieid = ? AND u.id = s.userid', $session, $cookieid );

		if ( !$count )
		{
			return false;
		}

		$this->id 			= $session[ 'userid' ];
		$this->token		= $session[ 'token' ];
		$this->logged_in 	= true;
		$this->Load( $this->id, $this->account );
		$this->UserActive_Update();
		Sessions::Update_Cookie_LastActive( $this->_db, $cookieid );

		return true;
	}

	public function LoginValidate( $email, $password )
	{
		$count = $this->_db->single( 'SELECT id, password, force_password FROM users WHERE email = ?', $login, $email );

		if ( !$count )
		{
			return false;
		}

		if ( $login[ 'force_password' ] === 1 )
		{
			$this->_db->single( 'SELECT password FROM reset_password WHERE userid = ?', $reset, $login[ 'id' ] );

			if ( !Functions::VerifyPassword( $password, $reset[ 'password' ] ) )
			{
				return false;
			}
		}
		else if ( !Functions::VerifyPassword( $password, $login[ 'password' ] ) )
		{
			return false;
		}

		$this->id = $login[ 'id' ];

		return true;
	}

	public function Update( $user )
	{
		return $this->_db->query( '	UPDATE
										users
									SET
										fname 				= ?,
										lname 				= ?,
										email 				= ?,
										password			= ?,
										last_on				= ?,
										wins				= ?,
										losses				= ?,
										paid				= ?,
										current_place		= ?,
										email_preference	= ?,
										force_password		= ?
									WHERE
										id					= ?',
								$user[ 'fname' ], $user[ 'lname' ], $user[ 'email' ], $user[ 'password' ], $user[ 'last_on' ],
								$user[ 'wins' ], $user[ 'losses' ],	$user[ 'paid' ], $user[ 'current_place' ], $user[ 'email_preference' ], $user[ 'force_password' ],
								$user[ 'id' ] );
	}

	public function Recalculate_Records( &$db )
	{
		$query = $this->_db->query( 'UPDATE
										users u
									 SET
										u.wins		= ( SELECT COUNT( p.id ) FROM picks p, games g WHERE p.game_id = g.id AND p.winner_pick = g.winner AND p.user_id = u.id AND g.winner != 0 ),
										u.losses	= ( SELECT COUNT( g.id ) FROM picks p, games g WHERE p.game_id = g.id AND p.loser_pick = g.winner AND p.user_id = u.id AND g.winner != 0 )' );

		if ( !$query )
		{
			return false;
		}

		if ( $this->List_Load( $users ) === false )
		{
			return false;
		}

		if ( !Functions::Fix_User_Records( $db, $users ) )
		{
			return false;
		}

		foreach( $users as $user )
		{
			if ( !$this->_db->single( 'SELECT COUNT( id ) + 1 AS place FROM users WHERE wins > ?', $current, $user[ 'wins' ] ) )
			{
				return false;
			}

			if ( !$this->_db->query( 'UPDATE users SET current_place = ? WHERE id = ?', $current[ 'place' ], $user[ 'id' ] ) )
			{
				return false;
			}
		}

		return true;
	}

	public function Update_Record( &$db, $userid, $wins, $losses )
	{
		return $this->_db->query( 'UPDATE
								users
							SET
								wins 	= ?,
								losses 	= ?
							WHERE
								id = ?', $wins, $losses, $userid );
	}
}