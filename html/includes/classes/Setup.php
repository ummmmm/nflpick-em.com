<?php
require_once( 'Database.php' );
require_once( 'functions.php' );

class Setup
{
	private $_db;
	private $_error;

	public function __construct()
	{
		$this->_db = new Database();
	}

	public function Install()
	{
		if ( $this->Configured() )
		{
			return false;
		}

		if ( !$this->_Create_Tables() )
		{
			return false;
		}

		return true;
	}

	public function Uninstall( $email, $password )
	{
		$db_users = new Users( $this->_db );

		if ( !$db_users->validateLogin( $email, $password, $user ) )
		{
			return $this->_Set_Error( 'Invalid email / password' );
		}
		else if ( $user[ 'admin' ] != 1 )
		{
			return $this->_Set_Error( 'You must be an admin to uninstall the site' );
		}
		else if ( !$this->_Drop_Tables() )
		{
			return $this->_Set_Error( 'Failed to drop database' );
		}

		return true;
	}

	private function _Drop_Tables()
	{
		if ( $this->_Get_Tables( $tables ) === false )
		{
			return false;
		}

		foreach ( $tables as $table )
		{
			if ( $this->_db->query( sprintf( 'DROP TABLE %s', array_values( $table )[ 0 ] ) ) === false )
			{
				return $this->_Set_Error( sprintf( 'Failed droping table %s', $table ) );
			}
		}

		return true;
	}

	private function _Get_Tables( &$tables )
	{
		$count = $this->_db->select( 'SHOW TABLES', $tables );

		if ( $count === false )
		{
			return $this->_Set_Error( 'Failed to load tables' );
		}

		return $count;
	}

	public function Configured()
	{
		$count = $this->_Get_Tables( $null );

		if ( $count === false )
		{
			return true;
		}

		if ( $count > 0 )
		{
			$this->_Set_Error( 'NFL Pick-Em site has already been configured' );
			return true;
		}

		return false;
	}

	private function _Create_Tables()
	{
		$db_failed_logins	= new Failed_Logins( $this->_db );
		$db_games			= new Games( $this->_db );
		$db_news			= new News( $this->_db );
		$db_picks			= new Picks( $this->_db );
		$db_poll_answers	= new Poll_Answers( $this->_db );
		$db_poll_votes		= new Poll_Votes( $this->_db );
		$db_polls			= new Polls( $this->_db );
		$db_reset_passwords	= new Reset_Passwords( $this->_db );
		$db_sent_picks		= new Sent_Picks( $this->_db );
		$db_sessions		= new Sessions( $this->_db );
		$db_settings		= new Settings( $this->_db );
		$db_teams			= new Teams( $this->_db );
		$db_users			= new Users( $this->_db );
		$db_weeks			= new Weeks( $this->_db );

		if ( !$db_failed_logins->Create() )
		{
			return $this->_Set_Error( 'Failed to create the FailedLogin database table' );
		}

		if ( !$db_games->Create() )
		{
			return $this->_Set_Error( 'Failed to create the Games database table' );
		}

		if ( !$db_news->Create() )
		{
			return $this->_Set_Error( 'Failed to create the News database table' );
		}

		if ( !$db_picks->Create() )
		{
			return $this->_Set_Error( 'Failed to create the Picks database table' );
		}

		if ( !$db_polls->Create() )
		{
			return $this->_Set_Error( 'Failed to create the Polls database table' );
		}

		if ( !$db_poll_answers->Create() )
		{
			return $this->_Set_Error( 'Failed to create the PollAnswers database table' );
		}

		if ( !$db_poll_votes->Create() )
		{
			return $this->_Set_Error( 'Failed to create the PollVotes database table' );
		}

		if ( !$db_reset_passwords->Create() )
		{
			return $this->_Set_Error( 'Failed to create the ResetPassword database table' );
		}

		if ( !$db_sent_picks->Create() )
		{
			return $this->_Set_Error( 'Failed to create the SentPicks database table' );
		}

		if ( !$db_sessions->Create() )
		{
			return $this->_Set_Error( 'Failed to create the Settings database table' );
		}

		if ( !$db_settings->Create() )
		{
			return $this->_Set_Error( 'Failed to create the Settings database table' );
		}

		if ( !$db_teams->Create() )
		{
			return $this->_Set_Error( 'Failed to create the Teams database table' );
		}

		if ( !$db_users->Create() )
		{
			return $this->_Set_Error( 'Failed to create the Users database table' );
		}

		if ( !$db_weeks->Create() )
		{
			return $this->_Set_Error( 'Failed to create the Weeks database table' );
		}

		return true;
	}

	private function _Set_Error( $error )
	{
		$this->_error = $error;
		return false;
	}

	public function Get_Error()
	{
		return $this->_error;
	}
}
