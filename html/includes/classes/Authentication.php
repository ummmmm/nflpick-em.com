<?php

require_once( "Database.php" );
require_once( "functions.php" );

class Authentication
{
	private $_db_manager;
	private $_user;
	private $_session;
	private $_userID;
	private $_token;
	private $_reload;

	public function __construct( DatabaseManager &$db_manager )
	{
		$this->_db_manager	= $db_manager;
		$this->_user		= null;
		$this->_session		= null;
		$this->_userID		= 0;
		$this->_token		= 0;
		$this->_reload		= false;
	}

	public function db()
	{
		return $this->_db_manager;
	}

	public function initialize()
	{
		$session_cookie = Functions::Cookie( 'session' );

		if ( $session_cookie != '' )
		{
			if ( $this->_load_session( $session_cookie ) )
			{
				$this->db()->users()->Update_Last_Active( $this->_userID );
				$this->db()->sessions()->Update_Cookie_Last_Active( $this->_session[ 'cookieid' ] );
			}
		}

		return true;
	}

	private function _load_session( $session_cookie )
	{
		if ( !$this->db()->sessions()->Load( $session_cookie, $session ) || !$this->db()->users()->Load( $session[ 'userid' ], $user ) )
		{
			return false;
		}

		$this->_user 	= $user;
		$this->_session	= $session;
		$this->_userID	= $user[ 'id' ];
		$this->_token	= $session[ 'token' ];

		return true;
	}

	public function validate_login( $email, $password, &$user )
	{
		$validate = function() use ( $email, $password, &$user )
		{
			if ( !$this->db()->users()->Load_Email( $email, $loaded_user ) )
			{
				return false;
			}

			if ( $loaded_user[ 'force_password' ] == 0 )
			{
				if ( !password_verify( $password, $loaded_user[ 'password' ] ) )
				{
					return false;
				}
			}
			else
			{
				$db_reset_password = $this->db()->resetpasswords();

				if ( !$db_reset_password->Load_User( $loaded_user[ 'id' ], $reset_password ) || !password_verify( $password, $reset_password[ 'password'] ) )
				{
					return false;
				}
			}

			$user = $loaded_user;

			return true;
		};
		

		if ( !$validate( $email, $password, $user ) )
		{
			$db_settings		= $this->db()->settings();
			$db_failed_logins 	= $this->db()->failedlogins();
			$db_failed_logins->Insert( $email );

			if ( $db_settings->Load( $settings ) && $settings[ 'login_sleep' ] > 0 )
			{
				usleep( $settings[ 'login_sleep' ] * 1000 );
			}

			return false;
		}

		return true;
	}

	public function login( $user_id )
	{
		$db_sessions	= $this->db()->sessions();

		$cookieid		= hash( 'sha256', openssl_random_pseudo_bytes( 64 ) );
		$token			= hash( 'sha256', openssl_random_pseudo_bytes( 64 ) );
		$session		= array( 'token' => $token, 'cookieid' => $cookieid, 'userid' => $user_id );

		setcookie( 'session', $cookieid, time() + 60 * 60 * 24 * 30, '', '', true, true );

		$db_sessions->Insert( $session );

		return true;
	}

	public function logout()
	{
		$db_sessions = $this->db()->sessions();
		$db_sessions->Delete_Cookie( $this->_session[ 'cookieid' ] );

		$this->_user	= null;
		$this->_session	= null;
		$this->_userID	= 0;
		$this->_token	= 0;
		$this->_reload	= false;

		setcookie( 'session', '', -1, '' );
	}

	public function forceUserReload()
	{
		$this->_reload = true;
	}

	public function getUserID()
	{
		return $this->_userID;
	}

	public function getUser()
	{
		if ( $this->_reload )
		{
			$this->_reload = false;
			$this->db()->users()->Load( $this->_userID, $this->_user );
		}

		return $this->_user;
	}

	public function getToken()
	{
		return $this->_token;
	}

	public function isUser()
	{
		return $this->_userID ? true : false;
	}

	public function isAdmin()
	{
		return $this->_userID && $this->_user[ 'admin' ];
	}

	public function isValidToken( $token )
	{
		$db_sessions	= $this->db()->sessions();
		$count 			= $db_sessions->Load_User_Token( $this->_userID, $token, $null );

		return $count ? true : false;
	}
}
