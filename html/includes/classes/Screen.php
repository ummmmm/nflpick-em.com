<?php

require_once( "Database.php" );
require_once( "Authentication.php" );
require_once( "Security.php" );
require_once( "Input.php" );
require_once( "functions.php" );
require_once( "validation.php" );

abstract class Screen
{
	private $_screen_manager;
	private $_validation_errors;
	private $_validation_data;
	private $_update_message;

	public function __construct( ScreenManager &$screen_manager )
	{
		$this->_screen_manager		= $screen_manager;

		$this->_validation_errors	= array();
		$this->_validation_data		= null;
		$this->_update_message		= null;
	}

	abstract public function content();

	public function requirements()
	{
		return array();
	}

	public function validate()
	{
		return true;
	}

	public function update( $data )
	{
		return true;
	}

	public function head()
	{
		return true;
	}

	public function jquery()
	{
		return true;
	}

	protected function auth()
	{
		return $this->_screen_manager->auth();
	}

	protected function input()
	{
		return $this->_screen_manager->input();
	}

	protected function db()
	{
		return $this->_screen_manager->db();
	}

	protected function settings()
	{
		return $this->_screen_manager->settings();
	}

	protected function addValidationError( string $message )
	{
		array_push( $this->_validation_errors, $message );
	}

	protected function setValidationData( $data )
	{
		$this->_validation_data = $data;

		return true;
	}

	protected function setUpdateMessage( $message )
	{
		$this->_update_message = $message;

		return true;
	}

	public function hasValidationErrors()
	{
		return count( $this->_validation_errors ) > 0;
	}

	public function getValidationErrors()
	{
		return $this->_validation_errors;
	}

	public function getValidationData()
	{
		return $this->_validation_data;
	}

	public function getUpdateMessage()
	{
		return $this->_update_message;
	}

	protected function outputInformation( string $h1, string $p )
	{
		printf( "<h1>%s</h1>\n", $h1 );
		printf( "<p>%s</p>\n", $p );

		return true;
	}
}

abstract class Screen_User extends Screen
{
	public function requirements()
	{
		return array( "user" => true );
	}
}

abstract class Screen_Admin extends Screen
{
	public function requirements()
	{
		return array( "admin" => true );
	}
}

class ScreenManager
{
	const FLAG_ERROR_NONE			= 0x0;
	const FLAG_ERROR_MISCONFIGURED	= 0x1;
	const FLAG_ERROR_VALIDATE		= 0x2;
	const FLAG_ERROR_UPDATE			= 0x3;
	const FLAG_ERROR_HEAD			= 0x4;
	const FLAG_ERROR_JQUERY			= 0x5;
	const FLAG_ERROR_CONTENT		= 0x6;

	private $_db_manager;
	private $_auth;
	private $_input;
	private $_screen;
	private $_error;
	private $_error_level;
	private $_settings;

	private $_head_data;
	private $_jquery_data;
	private $_content_data;

	private $_run_update;

	public function __construct()
	{
		$this->_db_manager 			= new DatabaseManager();
		$this->_auth				= new Authentication( $this->_db_manager );
		$this->_input				= new RawInput();

		$this->_screen				= null;
		$this->_error				= null;
		$this->_error_level			= 0x0;

		$this->_settings			= null;

		$this->_jquery_data			= null;
		$this->_head_data			= null;
		$this->_content_data		= null;

		$this->_run_update			= false;
	}

	public function db()
	{
		return $this->_db_manager;
	}

	public function auth()
	{
		return $this->_auth;
	}

	public function input()
	{
		return $this->_input;
	}

	public function initialize()
	{
		$this->db()->initialize();
		$this->auth()->initialize();

		$this->_build();
	}

	private function _build_head()
	{
		try
		{
			ob_start();
			$this->_screen->head();
			$this->_setHeadData( ob_get_contents() );
		}
		catch ( NFLPickEmException $e )
		{
			$this->_setError( $e->getMessage() );
			return $this->_setErrorLevel( self::FLAG_ERROR_HEAD );
		}
		finally
		{
			ob_end_clean();
		}

		return true;
	}

	private function _build_jquery()
	{
		try
		{
			ob_start();
			$this->_screen->jquery();
			$this->_setJQueryData( ob_get_contents() );
		}
		catch ( NFLPickEmException $e )
		{
			$this->_setError( $e->getMessage() );
			return $this->_setErrorLevel( self::FLAG_ERROR_JQUERY );
		}
		finally
		{
			ob_end_clean();
		}

		return true;
	}

	private function _build_content()
	{
		try
		{
			ob_start();
			$this->_screen->content();
			$this->_setContentData( ob_get_contents() );
		}
		catch ( NFLPickEmException $e )
		{
			$this->_setError( $e->getMessage() );
			return $this->_setErrorLevel( self::FLAG_ERROR_CONTENT );
		}
		finally
		{
			ob_end_clean();
		}

		return true;
	}

	private function _run_update()
	{
		if ( $this->_run_update )
		{
			try
			{
				$this->_screen->validate();
			}
			catch ( NFLPickEmException $e )
			{
				$this->_setError( $e->getMessage() );
				return $this->_setErrorLevel( self::FLAG_ERROR_VALIDATE );
			}

			if ( !$this->_screen->hasValidationErrors() )
			{
				try
				{
					$this->_screen->update( $this->_screen->getValidationData() );
				}
				catch ( NFLPickEmException $e )
				{
					$this->_setError( $e->getMessage() );
					return $this->_setErrorLevel( self::FLAG_ERROR_UPDATE );
				}
			}
		}

		return true;
	}

	public function settings()
	{
		if ( $this->_settings == null )
		{
			if ( !$this->db()->settings()->Load( $this->_settings ) )
			{
				throw new NFLPickEmException( 'Settings do not exist' );
			}
		}

		return $this->_settings;
	}

	public function _build()
	{
		if ( !$this->_configure() )
		{
			return $this->_setErrorLevel( self::FLAG_ERROR_MISCONFIGURED );
		}

		if ( !$this->_run_update() )
		{
			return false;
		}

		if ( !$this->_build_head() || !$this->_build_jquery() || !$this->_build_content() )
		{
			return false;
		}
	}

	private function _valid_configuration()
	{
		return $this->_error_level == 0x0;
	}

	public function head()
	{
		if ( $this->_valid_configuration() )
		{
			return $this->_head_data;
		}
	}

	public function jquery_head()
	{
		if ( $this->_valid_configuration() )
		{
			return $this->_jquery_data;
		}
	}

	public function content()
	{
		ob_start();
		switch ( $this->_error_level )
		{
			case self::FLAG_ERROR_NONE			:
			{
				$this->_outputUpdateMessage();
				$this->_outputValidationErrors();
				$this->_outputContentData();

				break;
			}
			case self::FLAG_ERROR_HEAD			:
			case self::FLAG_ERROR_JQUERY		:
			case self::FLAG_ERROR_CONTENT		:
			case self::FLAG_ERROR_VALIDATE		:
			case self::FLAG_ERROR_UPDATE		:
			case self::FLAG_ERROR_MISCONFIGURED	:
			{
				$this->_outputFatalError();

				break;
			}
		}
		$data = ob_get_contents();
		ob_clean();

		return $data;
	}

	private function _configure()
	{
		$admin	= $this->input()->value_GET_str( 'view' ) == 'admin';
		$screen	= strlen( $this->input()->value_GET_str( 'screen' ) ) ? $this->input()->value_GET_str( 'screen' ) : 'default';

		try
		{
			if ( $admin )	$path = "includes/runtime/admin/screens";
			else			$path = "includes/runtime/non-admin/screens";

			$screen_name 	= $this->_screenName( $screen );
			$class 			= sprintf( "Screen_%s", $screen_name );
			$file_path		= sprintf( "%s/%s.php", $path, Functions::Strip_Nulls( $screen_name ) );

			if ( !file_exists( $file_path ) )		throw new NFLPickEmException( 'Screen not found' );
			else if ( !require_once( $file_path ) )	throw new NFLPickEmException( 'Failed to load screen' );
			else if ( !class_exists( $class ) )		throw new NFLPickEmException( 'Screen is miscongifured' );

			$this->_screen = new $class( $this );

			if ( !$this->_screen instanceof Screen )
			{
				throw new NFLPickEmException( 'Screen is missing required inheritance' );
			}

			$requirements	= $this->_screen->requirements();
			$require_user	= $requirements[ 'user' ] ?? false;
			$require_admin	= $requirements[ 'admin' ] ?? false;
			$require_token	= $requirements[ 'token' ] ?? false;

			if ( $require_user && !$this->auth()->isUser() )		throw new NFLPickEmException( 'You must be a user to view this screen' );
			else if ( $require_admin && !$this->auth()->isAdmin() )	throw new NFLPickEmException( 'You must be an administrator to view this screen' );

			if ( $this->input()->value_POST_bool( 'update' ) )
			{
				$token = $this->input()->value_POST_str( 'token' );

				if ( $require_token && !$this->auth()->isValidToken( $token ) )
				{
					throw new NFLPickEmException( 'You must have a valid token to complete this action' );
				}

				$this->_run_update = true;
			}
		}
		catch ( NFLPickEmException $e )
		{
			$this->_setError( $e->getMessage() );
			return false;
		}

		return true;
	}

	private function _setError( $error )
	{
		$this->_error = $error;

		return false;
	}

	private function _setErrorLevel( $level )
	{
		$this->_error_level = $level;

		return false;
	}

	private function _setJQueryData( $data )
	{
		$this->_jquery_data = $data;
	}

	private function _setHeadData( $data )
	{
		$this->_head_data = $data;
	}

	private function _setContentData( $data )
	{
		$this->_content_data = $data;
	}

	private function _outputUpdateMessage()
	{
		if ( !$this->_screen->getUpdateMessage() )
		{
			return;
		}

		printf( "<p><b>%s</b></p>", htmlentities( $this->_screen->getUpdateMessage() ) );
	}

	private function _outputContentData()
	{
		print $this->_content_data;
	}

	private function _outputFatalError()
	{
		$code 		= '#Error#';
		$message 	= $this->_error ?? 'An unknown error has occurred.';

		printf( "<h1>An error has occurred</h1>\n" );
		printf( "<div>Error Code: %s</div>\n", htmlentities( $code ) );
		printf( "<div>Error Message: %s</div>\n", htmlentities( $message ) );
	}

	private function _outputValidationErrors()
	{
		$errors = $this->_screen->getValidationErrors();

		if ( !$errors )
		{
			return;
		}
		else if ( !is_array( $errors ) )
		{
			$errors = array( $errors );
		}

		$count 		= count( $errors );
		$title 		= ( $count == 1 ) ? "Error Has" : sprintf( "%d Errors Have", $count );
		$message	= "";

		foreach( $errors as $error )
		{
			$message .= sprintf( "- %s<br />\n", htmlentities( $error ) );
		}

		printf( "<div class=\"error\">\n" );
		printf( "<span class=\"error_text_top\">The Following %s Occurred!</span><br />\n", $title );
		printf( "<span class=\"error_text\">%s</span>\n", $message );
		printf( "</div>\n" );
	}

	private function _screenName( $screen )
	{
		return implode( "", array_map( function( $string ) { return ucfirst( $string ); }, explode( '_', $screen ) ) );
	}

	// stupid navigation stuff

	public function topNavigation()
	{
		$db_weeks 	= $this->db()->weeks();
		$user		= $this->auth()->getUser();

		if ( !$this->auth()->getUserID() )
		{
			printf( "<span>Welcome, Guest. Please login to start making your picks for week %d.</span>\n", $db_weeks->Current() );
		}
		else
		{
			printf( "<span>Welcome, %s!  You have %d wins and %d losses and currently in %s place.</span>\n", htmlentities( $user[ 'name' ] ), $user[ 'wins' ], $user[ 'losses' ], Functions::Place( $user[ 'current_place' ] ) );
		}
	}

	public function sideNavigation()
	{
		$db_weeks 	= $this->db()->weeks();
		$weekid 	= $db_weeks->Previous();
		$admin 		= ( $this->auth()->isAdmin() ) ? '<li><a href="?view=admin" title="Admin Control Panel">Admin Control Panel</a></li>' : '';

		if ( $this->auth()->getUserID() )
		{
			print <<<EOT
<h1>User Links</h1>
<ul>
	<li><a href="" title="Home">Home Page</a></li>
	<li><a href="?screen=control_panel" title="User Control Panel">User Control Panel</a></li>
	{$admin}
	<li><a href="?screen=make_picks" title="Make Picks">Make Picks</a></li>
	<li><a href="?screen=view_picks&week={$weekid}" title="User Picks">View Other's Picks</a></li>
	<li><a href="?screen=weekly_records" title="Weekly User Records">View Weekly Records</a></li>
	<li><a href="?screen=leaderboard" title="Leader Board">View Leader Board</a></li>
	<li><a href="?screen=logout" title="Logout" id="logout">Logout</a></li>
</ul>
EOT;
		}

		print "<h1>Quick Links</h1>\n";
		print "<ul>";

		if ( !$this->auth()->getUserID() )
		{
			print "<li><a href=\"?screen=register\" title=\"Register\">Register</a></li>";
			print "<li><a href=\"?screen=login\" title=\"Login\">Login</a></li>";
		}

		print <<<EOT
<li><a href="?screen=schedule" title="View Schedule">Schedule</a></li>
<li><a href="?screen=contact" title="Contact Us">Contact Us</a></li>
<li><a href="?screen=online" title="Online Users">Online Users</a></li>
</ul>
EOT;
	}

}
