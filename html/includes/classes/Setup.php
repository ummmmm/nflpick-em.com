<?php
require_once( 'database.php' );
require_once( 'functions.php' );
require_once( 'Sessions.php' );
require_once( 'user.php' );

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
		if ( $this->_Configured() )
		{
			return false;
		}

		if ( !$this->_Create_Tables() )
		{
			return false;
		}

		return true;
	}

	private function _Configured()
	{
		if ( Settings::Load( $this->_db, $null ) )
		{
			return $this->_Set_Error( 'NFL Pick-Em site has already been configured' );
		}

		return false;
	}

	private function _Create_Tables()
	{
		$sessions	= new Sessions();
		$users		= new User();

		if ( !FailedLogin::Create( $this->_db ) )
		{
			return $this->_Set_Error( 'Failed to create the FailedLogin database table' );
		}

		if ( !Games::Create( $this->_db ) )
		{
			return $this->_Set_Error( 'Failed to create the Games database table' );
		}

		if ( !News::Create( $this->_db ) )
		{
			return $this->_Set_Error( 'Failed to create the News database table' );
		}

		if ( !Picks::Create( $this->_db ) )
		{
			return $this->_Set_Error( 'Failed to create the Picks database table' );
		}

		if ( !Polls::Create( $this->_db ) )
		{
			return $this->_Set_Error( 'Failed to create the Polls database table' );
		}

		if ( !PollAnswers::Create( $this->_db ) )
		{
			return $this->_Set_Error( 'Failed to create the PollAnswers database table' );
		}

		if ( !PollVotes::Create( $this->_db ) )
		{
			return $this->_Set_Error( 'Failed to create the PollVotes database table' );
		}

		if ( !ResetPassword::Create( $this->_db ) )
		{
			return $this->_Set_Error( 'Failed to create the ResetPassword database table' );
		}

		if ( !SentPicks::Create( $this->_db ) )
		{
			return $this->_Set_Error( 'Failed to create the SentPicks database table' );
		}

		if ( !$sessions->Create() )
		{
			return $this->_Set_Error( 'Failed to create the Settings database table' );
		}

		if ( !Settings::Create( $this->_db ) )
		{
			return $this->_Set_Error( 'Failed to create the Settings database table' );
		}

		if ( !Teams::Create( $this->_db ) )
		{
			return $this->_Set_Error( 'Failed to create the Teams database table' );
		}

		if ( !$users->Create() )
		{
			return $this->_Set_Error( 'Failed to create the Users table' );
		}

		if ( !Weeks::Create( $this->_db ) )
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
