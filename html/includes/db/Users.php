<?php

class DatabaseTableUsers extends DatabaseTable
{
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
					sign_up 			int( 11 ),
					last_on 			int( 11 ),
					wins 				int( 11 ),
					losses 				int( 11 ),
					paid 				tinyint( 1 ),
					current_place 		int( 11 ),
					email_preference 	tinyint( 1 ),
					force_password 		tinyint( 1 ),
					active				tinyint( 1 ),
					message				varchar( 255 ),
					pw_opt_out			tinyint( 1 ),
					PRIMARY KEY ( id )
				)";

		return $this->query( $sql );
	}

	public function List_Load( &$users )
	{
		return $this->select( 'SELECT *, CONCAT( fname, \' \', lname ) AS name FROM users ORDER BY fname', $users );
	}

	public function Load( $userid, &$user )
	{
		return $this->single( 'SELECT *, CONCAT( fname, \' \', lname ) AS name FROM users WHERE id = ?', $user, $userid );
	}

	public function Load_Email( $email, &$user )
	{
		return $this->single( 'SELECT *, CONCAT( fname, \' \', lname ) AS name FROM users WHERE email = ?', $user, $email );
	}

	public function Delete( $user_id )
	{
		$db_perfect_week_paid	= $this->db()->perfectweekpaid();
		$db_picks 				= $this->db()->picks();
		$db_poll_votes			= $this->db()->pollvotes();
		$db_reset_password		= $this->db()->resetpasswords();
		$db_sent_picks			= $this->db()->sentpicks();
		$db_sessions			= $this->db()->sessions();
		$db_weekly_records		= $this->db()->weeklyrecords();

		if ( !$db_perfect_week_paid->Delete_User( $user_id )	||
			 !$db_picks->Delete_User( $user_id ) 				||
			 !$db_poll_votes->Delete_User( $user_id )			||
			 !$db_reset_password->Delete_User( $user_id )		||
			 !$db_sent_picks->Delete_User( $user_id )			||
			 !$db_sessions->Delete_User( $user_id )				||
			 !$db_weekly_records->Delete_User( $user_id ) )
		{
			return false;
		}

		return $this->_Delete_LowLevel( $user_id );
	}

	private function _Delete_LowLevel( $userid )
	{
		return $this->query( 'DELETE FROM users WHERE id = ?', $userid );
	}

	public function Update_Last_Active( $id )
	{
		$date = time();

		return $this->query( 'UPDATE users SET last_on = ? WHERE id = ?', $date, $id );
	}

	public function Insert( &$user )
	{
		$db_weekly_records = $this->db()->weeklyrecords();

		if ( !$this->_Insert_LowLevel( $user ) )
		{
			return false;
		}

		if ( !$db_weekly_records->Insert_User( $user[ 'id' ] ) )
		{
			return false;
		}

		return true;
	}

	private function _Insert_LowLevel( &$user )
	{
		$result = $this->query( 'INSERT INTO users
								 ( fname, lname, email, password, admin, sign_up, last_on, wins, losses, paid, current_place, email_preference, force_password, active, message, pw_opt_out )
								 VALUES
								 ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )',
								 $user[ 'fname' ], $user[ 'lname' ], $user[ 'email' ], $user[ 'password' ], $user[ 'admin' ], $user[ 'sign_up' ], $user[ 'last_on' ],
								 $user[ 'wins' ], $user[ 'losses' ], $user[ 'paid' ], $user[ 'current_place' ], $user[ 'email_preference' ], $user[ 'force_password' ],
								 $user[ 'active' ], $user[ 'message' ], $user[ 'pw_opt_out' ] );

		if ( !$result )
		{
			return false;
		}

		$user[ 'id' ] = $this->insertID();

		return true;
	}

	public function Update( $user )
	{
		return $this->query( '	UPDATE
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
										force_password		= ?,
										active				= ?,
										message				= ?,
										pw_opt_out			= ?
									WHERE
										id					= ?',
								$user[ 'fname' ], $user[ 'lname' ], $user[ 'email' ], $user[ 'password' ], $user[ 'last_on' ],
								$user[ 'wins' ], $user[ 'losses' ],	$user[ 'paid' ], $user[ 'current_place' ], $user[ 'email_preference' ],
								$user[ 'force_password' ], $user[ 'active' ], $user[ 'message' ], $user[ 'pw_opt_out' ],
								$user[ 'id' ] );
	}

	public function Recalculate_Records()
	{
		return Functions::Update_User_Records( $this->db() );
	}

	public function Update_Record( $userid, $wins, $losses )
	{
		return $this->query( 'UPDATE
									users
								   SET
									wins 	= ?,
									losses 	= ?
								   WHERE
									id		= ?', $wins, $losses, $userid );
	}
}
