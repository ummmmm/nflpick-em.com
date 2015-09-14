<?php

require_once( "Database.php" );
require_once( "Authentication.php" );
require_once( "functions.php" );
require_once( "validation.php" );

interface iScreen
{
	function requirements();
	function content();
}

class Screen
{
	const FLAG_REQ_USER 			= 0x1;
	const FLAG_REQ_ADMIN			= 0x2;
	const FLAG_REQ_TOKEN			= 0x4;

	const FLAG_ERROR_NONE			= 0x0;
	const FLAG_ERROR_MISCONFIGURED	= 0x1;
	const FLAG_ERROR_VALIDATE		= 0x2;
	const FLAG_ERROR_UPDATE			= 0x3;
	const FLAG_ERROR_HEAD			= 0x4;
	const FLAG_ERROR_JQUERY			= 0x5;
	const FLAG_ERROR_CONTENT		= 0x6;

	private $_db;
	private $_auth;
	private $_screen;
	private $_error;
	private $_misconfigured;
	private $_error_level;

	private $_validation_data;
	private $_head_data;
	private $_jquery_data;
	private $_content_data;
	private $_execute_success;

	public function __construct()
	{
		$this->_db 					= new Database();
		$this->_auth				= new Authentication();

		$this->_screen				= null;
		$this->_error				= array();
		$this->_error_level			= 0x0;
		$this->_execute_success		= false;
		$this->_validation_errors	= null;

		$this->_validation_data 	= null;
		$this->_jquery_data			= null;
		$this->_head_data			= null;
		$this->_content_data		= null;

		$this->_update_message		= null;
		$this->_run_update			= false;
	}

	public function initialize( $admin, $screen, $update )
	{
		$this->_misconfigured = !$this->_configure( $admin, $screen, $update );
	}

	public function execute()
	{
		if ( $this->_misconfigured )
		{
			return $this->_setErrorLevel( self::FLAG_ERROR_MISCONFIGURED );
		}

		if ( $this->_run_update )
		{

			if ( !$this->_screen->validate() )
			{
				return $this->_setErrorLevel( self::FLAG_ERROR_VALIDATE );
			}

			if ( !$this->_validation_errors && !$this->_screen->update( $this->_validation_data ) )
			{
				return $this->_setErrorLevel( self::FLAG_ERROR_UPDATE );
			}
		}

		if ( method_exists( $this->_screen, "head" ) )
		{
			ob_start();
			if ( !$this->_screen->head() )	return $this->_setErrorLevel( self::FLAG_ERROR_HEAD );
			else							$this->_setHeadData( ob_get_contents() );
			ob_clean();
		}

		if ( method_exists( $this->_screen, "jquery" ) )
		{
			ob_start();
			if ( !$this->_screen->jquery() )	return $this->_setErrorLevel( self::FLAG_ERROR_JQUERY );
			else								$this->_setJQueryData( ob_get_contents() );
			ob_clean();
		}

		ob_start();
		if ( !$this->_screen->content() )
		{
			return $this->_setErrorLevel( self::FLAG_ERROR_CONTENT );
		}
		$this->_setContentData( ob_get_contents() );
		ob_clean();

		$this->_execute_success = true;

		return true;
	}

	public function head()
	{
		if ( $this->_execute_success )
		{
			return $this->_head_data;
		}
	}

	public function jquery_head()
	{
		if ( $this->_execute_success )
		{
			return $this->_jquery_data;
		}
	}

	public function content()
	{
		ob_start();
		switch ( $this->_error_level )
		{
			case self::FLAG_ERROR_HEAD			:
			case self::FLAG_ERROR_JQUERY		:
			case self::FLAG_ERROR_CONTENT		:
			case self::FLAG_ERROR_VALIDATE		:
			case self::FLAG_ERROR_UPDATE		:
			case self::FLAG_ERROR_MISCONFIGURED	:
			default								:
			{
				$this->_outputFatalError();
				break;
			}
			case self::FLAG_ERROR_NONE			:
			{
				$this->_outputUpdateMessage();
				$this->_outputValidationErrors();
				$this->_outputContentData();
				break;
			}
		}
		$data = ob_get_contents();
		ob_clean();

		return $data;
	}

	private function _configure( $admin, $screen, $run_update )
	{
		if ( $admin )	$path = "includes/runtime/admin/screens";
		else			$path = "includes/runtime/non-admin/screens";

		$screen			= ( $screen == "" ) ? "default" : $screen;
		$screen_name 	= $this->_screenName( $screen );
		$class 			= sprintf( "Screen_%s", $screen_name );
		$file_path		= sprintf( "%s/%s.php", $path, Functions::Strip_Nulls( $screen_name ) );

		if ( !file_exists( $file_path ) )
		{
			return $this->_setError( array( "#Error#", "Screen not found" ) );
		}
		else if ( !require_once( $file_path ) )
		{
			return $this->_setError( array( "#Error#", "Failed to load screen" ) );
		}

		if ( !class_exists( $class ) )
		{
			return $this->_setError( array( "#Error#", "Screen is miscongifured" ) );
		}

		$this->_screen = new $class( $this->_db, $this->_auth, $this );

		if ( !$this->_screen instanceof iScreen )
		{
			return $this->_setError( array( "#Error#", "Screen is missing required interface" ) );
		}

		$this->_getRequirements( $flags );

		if ( ( $flags & self::FLAG_REQ_USER ) && !$this->_auth->isUser() )
		{
			return $this->_setError( array( '#Error#', 'You must be a user to complete this action' ) );
		}

		if ( ( $flags & self::FLAG_REQ_ADMIN ) && !$this->_auth->isAdmin() )
		{
			return $this->_setError( array( '#Error#', 'You must be an administrator to complete this action' ) );
		}

		if ( ( $flags & self::FLAG_REQ_TOKEN ) && !$this->_auth->isValidToken( $token ) )
		{
			return $this->_setError( array( '#Error#', 'You must have a valid token to complete this action' ) );
		}

		if ( $run_update )
		{
			if ( !method_exists( $this->_screen, "validate" ) || !method_exists( $this->_screen, "update" ) )
			{
				return $this->_setError( array( "#Error#", "Screen is missing required methods" ) );
			}

			$this->_run_update = true;
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
		if ( !$this->_update_message )
		{
			return;
		}

		printf( "<p><b>%s</b></p>", htmlentities( $this->_update_message ) );
	}

	private function _outputContentData()
	{
		print $this->_content_data;
	}

	private function _outputFatalError()
	{
		@list( $code, $message ) = $this->_error;

		$code 		= ( !$code ) ? "UNKNOWN" : $code;
		$message 	= ( !$message ) ? "An unknown error has occurred." : $message;

		printf( "<h1>An error has occurred</h1>\n" );
		printf( "<div>Error Code: %s</div>\n", htmlentities( $code ) );
		printf( "<div>Error Message: %s</div>\n", htmlentities( $message ) );
	}

	private function _outputValidationErrors()
	{
		$errors = $this->_validation_errors;

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
		printf( "<span class=\"error_text_top\">The Following %s Ocurred!</span><br />\n", $title );
		printf( "<span class=\"error_text\">%s</span>\n", $message );
		printf( "</div>\n" );
	}

	private function _screenName( $screen )
	{
		return implode( "", array_map( function( $string ) { return ucfirst( $string ); }, explode( '_', $screen ) ) );
	}

	private function _getRequirements( &$flags )
	{
		$requirements	= $this->_screen->requirements();
		$flags			= 0x0;
		$flags			|= array_key_exists( 'user', 	$requirements ) && $requirements[ 'user' ] 	? self::FLAG_REQ_USER	: 0x0;
		$flags			|= array_key_exists( 'admin', 	$requirements ) && $requirements[ 'admin' ] ? self::FLAG_REQ_ADMIN	: 0x0;
		$flags			|= array_key_exists( 'token', 	$requirements ) && $requirements[ 'token' ] ? self::FLAG_REQ_TOKEN	: 0x0;
	}

	// public functions that the sub-classes can use to set data/errors

	public function setError( $error )
	{
		$this->_error = $error;

		return false;
	}

	public function setDBError()
	{
		return $this->_setError( $this->_db->Get_Error() );
	}

	public function setValidationData( $data )
	{
		$this->_validation_data = $data;

		return true;
	}

	public function setUpdateMessage( $message )
	{
		$this->_update_message = $message;

		return true;
	}

	public function setValidationErrors( $errors )
	{
		$this->_validation_errors = $errors;

		return true;
	}

	// stupid navigation stuff

	public function topNavigation()
	{
		$db_weeks 	= new Weeks( $this->_db );
		$user		= $this->_auth->getUser();

		if ( !$this->_auth->getUserID() )
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
		$db_weeks	= new Weeks( $this->_db );
		$weekid 	= $db_weeks->Previous();
		$admin 		= ( $this->_auth->isAdmin() ) ? '<li><a href="?view=admin" title="Admin Control Panel">Admin Control Panel</a></li>' : '';

		if ( $this->_auth->getUserID() )
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

		if ( !$this->_auth->getUserID() )
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
