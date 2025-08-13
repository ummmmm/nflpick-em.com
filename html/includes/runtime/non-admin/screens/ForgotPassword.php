<?php

require_once( "includes/classes/Mail.php" );

class Screen_ForgotPassword extends Screen
{
	public function validate()
	{
		$action = $this->input()->value_GET_str( "action" );

		if ( $action == "changepassword" )
		{
			if ( !$this->auth()->getUserID() )
			{
				throw new NFLPickEmException( 'You must be logged in to complete this action' );
			}

			$password	= $this->input()->value_POST_str( "password" );
			$cpassword	= $this->input()->value_POST_str( "cpassword" );

			if ( strlen( $password ) < 5 )
			{
				$this->addValidationError( 'Password must be at least 5 characters.' );
			}
			else if ( $password !== $cpassword )
			{
				$this->addValidationError( 'Passwords do not match.' );
			}

			if ( !$this->hasValidationErrors() )
			{
				$this->setValidationData( $password );
			}

			return true;
		}

		if ( $action == '' )
		{
			$db_users	= $this->db()->users();
			$email 		= $this->input()->value_POST_str( "email" );
			$user		= null;

			$db_users->Load_Email( $email, $user );

			return $this->setValidationData( $user );
		}

		throw new NFLPickEmException( 'Invalid action' );
	}

	public function update( $data )
	{
		$db_users			= $this->db()->users();
		$db_reset_passwords = $this->db()->resetpasswords();
		$action				= $this->input()->value_GET_str( "action" );

		if ( $action == "changepassword" )
		{
			$user 						= $this->auth()->getUser();
			$user[ 'password' ]			= Security::password_hash( $data );
			$user[ 'force_password' ]	= 0;

			$db_users->Update( $user );
			$db_reset_passwords->Delete_User( $this->auth()->getUserID() );

			return $this->setUpdateMessage( "Your password has been updated" );
		}

		if ( $action == '' )
		{
			$settings = $this->settings();

			usleep( $settings[ 'login_sleep' ] * 1000 );

			if ( $data == null )
			{
				return $this->setUpdateMessage( "A temporary password has been emailed to you." );
			}

			$user 			= $data;
			$temp_password	= $this->_generateRandomString( 12 );
			$record			= array( 'userid' => $user[ 'id' ], 'password' => Security::password_hash( $temp_password ) );

			$db_reset_passwords->Delete_User( $user[ 'id' ] );
			$db_reset_passwords->Insert( $record );

			$user[ 'force_password' ]	= 1;

			$db_users->Update( $user );

			$email = new Mail( $user[ 'email' ], "Forgot Password", sprintf( 'Your temporary password is <span style="font-weight: bold;">%s</span>', $temp_password ) );

			if ( !$email->send() )
			{
				throw new NFLPickEmException( 'Failed to send email, please try again later' );
			}

			return $this->setUpdateMessage( "A temporary password has been emailed to you." );
		}

		throw new NFLPickEmException( 'Invalid action' );
	}

	public function content()
	{
		$settings	= $this->settings();
		$user		= $this->auth()->getUser();

		if ( $this->auth()->getUserID() && !$user[ 'force_password' ] )
		{
			header( sprintf( 'Location: %s', $settings[ 'domain_url' ] ) );
			die();
		}

		$action = $this->input()->value_GET_str( "action" );

		if ( $action == "changepassword" )	return $this->_ChangePassword();
		else if ( $action == '' )			return $this->_ForgotPassword();

		throw new NFLPickEmException( 'Invalid action' );
	}

	private function _ForgotPassword()
	{
		print <<<EOT
		<form name="forgotPass" action="" method="post" id="forgotPass">
			<fieldset>
				<legend>Forgotten Password</legend>
				<label for="email">Email Address</label>
				<input type="text" name="email" id="email" />
				<br />
				<input type="hidden" name="update" value="1" />
				<input type="submit" name="forgotPass" id="forgotPass" value="Get Password Now!" />
			</fieldset>
		</form>
EOT;
		return true;
	}

	private function _ChangePassword()
	{
		print <<<EOT
		<form name="update" action="" method="post" id="update">
			<fieldset>
				<legend>Update Password</legend>
				<label for="pass">New Password</label>
				<input type="password" name="password" id="newPass" />
				<br />
				<label for="cpass">Confirm Passowrd</label>
				<input type="password" name="cpassword" id="cUpdatePass" />
				<br />
				<input type="hidden" name="update" value="1" />
				<input type="submit" name="updatePass" id="updatePass" value="Update Password Now!" />
			</fieldset>
		</form>
EOT;

		return true;
	}

	function _generateRandomString( int $length ): string
	{
		$characters			= 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
		$charactersLength	= strlen( $characters );
		$randomString		= '';

		for ( $i = 0; $i < $length; $i++ )
		{
			$randomString	.= $characters[ random_int( 0, $charactersLength - 1 ) ];
		}

		return $randomString;
	}
}
