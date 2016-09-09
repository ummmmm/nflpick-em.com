<?php

require_once( "includes/classes/Mail.php" );

class Screen_ForgotPassword extends Screen
{
	public function validate()
	{
		$action = Functions::Get( "action" );

		if ( $action == "changepassword" )
		{
			if ( !$this->_auth->getUserID() )
			{
				return $this->setError( array( "#Error#", "You must be logged in" ) );
			}

			$password	= Functions::Post( "password" );
			$cpassword	= Functions::Post( "cpassword" );
			$errors		= array();

			if ( strlen( $password ) < 5 )
			{
				array_push( $errors, 'Password must be at least 5 characters.' );
			}
			else if ( $password !== $cpassword )
			{
				array_push( $errors, 'Passwords do not match.' );
			}

			if ( !empty( $errors ) )
			{
				return $this->setValidationErrors( $errors );
			}

			return $this->setValidationData( $password );
		}

		if ( $action == '' )
		{
			$db_users	= new Users( $this->_db );
			$email 		= Functions::Post( "email" );
			$count		= $db_users->Load_Email( $email, $user );

			if ( $count === false )
			{
				return $this->setDBError();
			}
			else if ( $count === 0 )
			{
				return $this->setValidationErrors( "Email not found" );
			}

			return $this->setValidationData( $user );
		}

		return $this->setValidationErrors( array( "Invalid action" ) );
	}

	public function update( $data )
	{
		$db_users			= new Users( $this->_db );
		$db_reset_passwords = new Reset_Passwords( $this->_db );
		$action				= Functions::Get( "action" );

		if ( $action == "changepassword" )
		{
			$user 						= $this->_auth->getUser();
			$user[ 'password' ]			= Functions::HashPassword( $data );
			$user[ 'force_password' ]	= 0;

			if ( !$db_users->Update( $user ) )
			{
				return $this->setDBError();
			}

			if ( !$db_reset_passwords->Delete_User( $this->_auth->getUserID() ) )
			{
				return $this->setDBError();
			}

			return $this->setUpdateMessage( "Your password has been updated" );
		}

		if ( $action == '' )
		{
			$user 			= $data;
			$temp_password	= Functions::Random( 10 );
			$record			= array( 'userid' => $user[ 'id' ], 'password' => Functions::HashPassword( $temp_password ) );

			if ( !$db_reset_passwords->Delete_User( $user[ 'id' ] ) )
			{
				return $this->setDBError();
			}

			if ( !$db_reset_passwords->Insert( $record ) )
			{
				return $this->setDBError();
			}

			$user[ 'force_password' ]	= 1;

			if ( !$db_users->Update( $user ) )
			{
				return $this->setDBError();
			}

			$email = new Mail( $user[ 'email' ], "Forgot Password", sprintf( 'Your temporary password is <span style="font-weight: bold;">%s</span>', $temp_password ) );

			if ( !$email->send() )
			{
				return $this->setError( array( "#Error#", "Failed to send email.  Please try again later."  ) );
			}

			return $this->setUpdateMessage( "A temporary password has been emailed to you." );
		}

		return $this->setError( array( "#Error#", "Invalid action" ) );
	}

	public function content()
	{
		$user = $this->_auth->getUser();

		if ( $this->_auth->getUserID() && !$user[ 'force_password' ] )
		{
			header( sprintf( "Location: %s", INDEX ) );
			die();
		}

		$action = Functions::Get( "action" );

		if ( $action == "changepassword" )	return $this->_ChangePassword();
		else if ( $action == '' )			return $this->_ForgotPassword();

		return $this->setError( array( "#Error#", "Invalid action" ) );
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
}
