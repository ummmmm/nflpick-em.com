<?php

require_once( "Database.php" );
require_once( "Authentication.php" );
require_once( "functions.php" );

interface iScreen
{
	function content();
}

class Screen
{
	private $_db;
	private $_auth;
	private $_screen;
	private $_error;
	private $_misconfigured;

	private $_validation_data;
	private $_head_data;
	private $_content_data;

	public function __construct()
	{
		$this->_db 		= new Database();
		$this->_auth	= new Authentication();
		$this->_screen	= null;
		$this->_error	= null;

		$this->_validation_error	= null;
		$this->_validation_data 	= null;
		$this->_head_data			= null;
		$this->_content_data		= null;
	}

	public function initialize( $admin, $screen )
	{
		$this->_misconfigured = !$this->_configure( $admin, $screen );
	}

	public function execute( $update )
	{
		if ( $this->_misconfigured )
		{
			return false;
		}

		if ( $update )
		{
			if ( method_exists( $this->_screen, "validate" ) )
			{
				if ( $this->_screen->validate() )
				{
					if ( method_exists( $this->_screen, "update" ) )
					{
						if ( !$this->_screen->update( $this->_getValidationData() ) )
						{
							return false;
						}
					}
				}
			}
		}

		if ( method_exists( $this->_screen, "head" ) )
		{
			ob_start();
			if ( !$this->_screen->head() )
			{
				return false;
			}

			$this->_setHeadData( ob_get_contents() );
			ob_clean();
		}

		ob_start();
		if ( !$this->_screen->content() )
		{
			return false;
		}

		$this->_setContentData( ob_get_contents() );
		ob_clean();

		return true;
	}

	public function getValidationErrorsData()
	{
		$errors = $this->_validation_error;

		if ( is_null( $errors ) )
		{
			return;
		}

		if ( !is_array( $errors ) )
		{
			$errors = array( $errors );
		}

		$count = count( $errors );

		if ( $count > 0 )
		{
			$output 	= '';
			$message	= '';
			$title 		= ( $count === 1 ) ? 'Error Has' : $count . ' Errors Have';

			foreach( $errors as $error )
			{
				$message .= "- {$error}<br />";
			}

			print '<div class="error">';
			printf( '<span class="error_text_top">The Following %s Ocurred!</span><br />', htmlentities( $title ) );
			printf( '<span class="error_text">%s</span>', $message );
			print '</div>';
		}
	}

	public function setValidationErrors( $errors )
	{
		$this->_validation_error = $errors;

		return false;
	}

	private function _getValidationData()
	{
		return $this->_validation_data;
	}

	private function _setHeadData( $data )
	{
		$this->_head_data = $data;
	}

	private function _setContentData( $data )
	{
		$this->_content_data = $data;
	}

	public function getContentData()
	{
		return $this->_content_data;
	}

	public function getHeadData()
	{
		return $this->_head_data;
	}

	private function _configure( $admin, $screen )
	{
		if ( $admin )	$path = "includes/runtime/admin/screens";
		else			$path = "includes/runtime/non-admin/screens";

		$class 		= sprintf( "Screen_%s", $screen );
		$file_path	= sprintf( "%s/%s.php", $path, Functions::Strip_Nulls( $screen ) );

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

		return true;
	}

	private function _setError( $error )
	{
		$this->_error = $error;

		return false;
	}

	public function getError()
	{
		return $this->_error;
	}

	public function setValidationData( $data )
	{
		$this->_validation_data = $data;
	}


}
