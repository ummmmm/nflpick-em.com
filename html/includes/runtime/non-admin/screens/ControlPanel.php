<?php

class Screen_ControlPanel implements iScreen
{
	public function __construct( Database &$db, Authentication &$auth, Screen &$screen )
	{
		$this->_db		= $db;
		$this->_auth	= $auth;
		$this->_screen	= $screen;
	}

	public function requirements()
	{
		return array( "user" => true );
	}

	public function validate()
	{
		$action = Functions::Get( "action" );

		if ( $action == 'changeemail' )
		{
			$db_users	= new Users( $this->_db );
			$email 		= Functions::Post( "email" );
			$cemail 	= Functions::Post( "cemail" );
			$pass 		= Functions::Post( "pass" );
			$errors		= array();

			if ( !Validation::Email( $email ) )
			{
				array_push( $errors, 'Invalid email address.' );
			}
			else if ( $email != $cemail )
			{
				array_push( $errors, 'Email address\' do not match.' );
			}
			else if ( $db_users->Load_Email( $email, $null ) )
			{
				array_push( $errors, 'The email address is already in use.' );
			}

			if ( !Functions::VerifyPassword( $pass, $this->_auth->getUser()[ 'password' ] ) )
			{
				array_push( $errors, 'Invalid password.' );
			}

			if ( !empty( $errors ) )
			{
				return $this->_screen->setValidationErrors( $errors );
			}

			return $this->_screen->setValidationData( array( "email" => $email ) );
		}

		if ( $action == 'changepassword' )
		{
			$new_password 	= Functions::Post( "new_password" );
			$c_new_password = Functions::Post( "c_new_password" );
			$old_password	= Functions::Post( "old_password" );
			$errors			= array();

			if ( strlen( $new_password ) < 5 )
			{
				array_push( $errors, 'Password must be at least 5 characters.' );
			}
			else if ( $new_password !== $c_new_password )
			{
				array_push( $errors, 'Passwords do not match.' );
			}

			if ( !Functions::VerifyPassword( $old_password, $this->_auth->getUser()[ 'password' ] ) )
			{
				array_push( $errors, 'Your old password does not match the password on file.' );
			}

			if ( !empty( $errors ) )
			{
				return $this->_screen->setValidationErrors( $errors );
			}

			return $this->_screen->setValidationData( array( "password" => Functions::HashPassword( $new_password ) ) );
		}

		return true;
	}

	public function update( $data )
	{
		$db_users	= new Users( $this->_db );
		$action 	= Functions::Get( "action" );

		if ( $action == 'changeemail' )
		{
			$user 				= $this->_auth->getUser();
			$user[ 'email' ] 	= $data[ 'email' ];

			if ( !$db_users->Update( $user ) )
			{
				return $this->_screen->setDBError();
			}

			return $this->_screen->setUpdateMessage( "Your email adddress has been updated." );
		}

		if ( $action == 'changepassword' )
		{
			$user				= $this->_auth->getUser();
			$user[ 'password' ]	= $data[ 'password' ];

			if ( !$db_users->Update( $user ) )
			{
				return $this->_screen->setDBError();
			}

			return $this->_screen->setUpdateMessage( "Your password has been updated." );
		}

		return true;
	}

	public function content()
	{
		$action = Functions::Get( 'action' );

		if ( $action == 'changeemail' )			return $this->_ChangeEmail();
		else if ( $action == 'changepassword' )	return $this->_ChangePassword();
		
		return $this->_PageLayout();
	}

	private function _PageLayout()
	{
		print '<h1>Control Panel</h1>';
		print <<<EOT
			<p><a href="?screen=make_picks" title="Make Picks">Make Picks</a></p>
			<p><a href="?screen=control_panel&action=changepassword" title="Change Password">Change Password</a></p>
			<p><a href="?screen=control_panel&action=changeemail" title="Change Email">Change Email</a></p>
			<p><a href="?screen=contact" title="File Report">Support Help</a></p>
EOT;

		return true;
	}

	private function _ChangeEmail()
	{
		print <<<EOT
		<form name="email" action="" method="post" id="email">
			<fieldset>
				<legend>Reset Email</legend>
				<label for="email">New Email</label>
				<input type="text" name="email" id="newEmail" />
				<br />
				<label for="cemail">Confirm Email</label>
				<input type="text" name="cemail" id="confirmEmail" />
				<br />
				<label for="pass">Current Password</label>
				<input type="password" name="pass" id="pass" /><br />
				<input type="hidden" name="update" value="1" />
				<input type="hidden" name="action" value="update" />
				<input type="submit" name="changeEmail" id="changeEmail" value="Update Email" />
			</fieldset>
		</form>
EOT;
		return true;
	}

	private function _ChangePassword()
	{
		print <<<EOT
		<form name="change" action="" method="post" id="change">
			<fieldset>
				<legend>Change Password</legend>
				<label for="oldPass">Old Password</label>
				<input type="password" name="old_password" id="oldPass" />
				<br />
				<label for="newPass">New Password</label>
				<input type="password" name="new_password" id="newPass" />
				<br />
				<label for="confirmNewPass">Confirm New Password</label>
				<input type="password" name="c_new_password" id="confirmPass" />
				<br />
				<input type="hidden" name="update" value="1" />
				<input type="hidden" name="action" value="update" />
				<input type="submit" name="changePass" id="changePass" value="Update Password" />
			</fieldset>
		</form>
EOT;

		return true;
	}
}

function Module_Validate( &$db, &$user, &$validation )
{
	$action = trim( $_GET[ 'action' ] );

	if ( $action == 'changeemail' )
	{
		$email 	= trim( $_POST[ 'email' ] );
		$cemail = trim( $_POST[ 'cemail' ] );
		$pass 	= trim( $_POST[ 'pass' ] );
		$errors	= array();

		if ( !Validation::Email( $email ) )
		{
			array_push( $errors, 'Invalid email address.' );
		}
		else if ( $email != $cemail )
		{
			array_push( $errors, 'Email address\' do not match.' );
		}
		else if ( Functions::EmailExists( $db, $email ) )
		{
			array_push( $errors, 'The email address is already in use.' );
		}

		if ( !Functions::VerifyPassword( $pass, $user->account[ 'password' ] ) )
		{
			array_push( $errors, 'Invalid password.' );
		}

		if ( !empty( $errors ) )
		{
			return Functions::ValidationError( $errors );
		}

		$validation[ 'email' ] = $email;
	}

	if ( $action == 'changepassword' )
	{
		$new_password 	= trim( $_POST[ 'new_password' ] );
		$c_new_password = trim( $_POST[ 'c_new_password' ] );
		$old_password	= trim( $_POST[ 'old_password' ] );
		$errors			= array();

		if ( strlen( $new_password ) < 5 )
		{
			array_push( $errors, 'Password must be at least 5 characters.' );
		}
		else if ( $new_password !== $c_new_password )
		{
			array_push( $errors, 'Passwords do not match.' );
		}

		if ( !Functions::VerifyPassword( $old_password, $user->account[ 'password' ] ) )
		{
			array_push( $errors, 'Your old password does not match the password on file.' );
		}

		if ( !empty( $errors ) )
		{
			return Functions::ValidationError( $errors );
		}

		$validation[ 'password' ] = Functions::HashPassword( $new_password );
	}

	return true;
}

function Module_Update( &$db, &$user, $validation )
{
	$action = trim( $_GET[ 'action' ] );

	if ( $action == 'changeemail' )
	{
		$user->account[ 'email' ] = $validation[ 'email' ];

		if ( !$user->Update( $user->account ) )
		{
			return false;
		}

		return Functions::Module_Updated( 'Your email address has been updated.' );
	}

	if ( $action == 'changepassword' )
	{
		$user->account[ 'password' ] = $validation[ 'password' ];

		if ( !$user->Update( $user->account ) )
		{
			return false;
		}

		return Functions::Module_Updated( 'Your password has been updated.' );
	}

	return true;
}

function Module_Content( &$db, &$user )
{
	Validation::User( $user->id );

	$action = Functions::Get( 'action' );

	if ( $action == 'changeemail' )
	{
		return ChangeEmail();
	}
	else if ( $action == 'changepassword' )
	{
		return ChangePassword();
	}
	
	return PageLayout();
}

function PageLayout()
{
	print '<h1>Control Panel</h1>';
	print <<<EOT
		<p><a href="?module=makepicks" title="Make Picks">Make Picks</a></p>
		<p><a href="?module=controlpanel&action=changepassword" title="Change Password">Change Password</a></p>
		<p><a href="?module=controlpanel&action=changeemail" title="Change Email">Change Email</a></p>
		<p><a href="?module=contact" title="File Report">Support Help</a></p>
EOT;

	return true;
}

function ChangeEmail()
{
	Functions::HandleModuleErrors();
	Functions::HandleModuleUpdate();

	print <<<EOT
		<form name="email" action="" method="post" id="email">
			<fieldset>
				<legend>Reset Email</legend>
				<label for="email">New Email</label>
				<input type="text" name="email" id="newEmail" />
				<br />
				<label for="cemail">Confirm Email</label>
				<input type="text" name="cemail" id="confirmEmail" />
				<br />
				<label for="pass">Current Password</label>
				<input type="password" name="pass" id="pass" /><br />
				<input type="hidden" name="action" value="update" />
				<input type="submit" name="changeEmail" id="changeEmail" value="Change Email Now" />
			</fieldset>
		</form>
EOT;
	return true;
}

function ChangePassword()
{
	Functions::HandleModuleErrors();
	Functions::HandleModuleUpdate();

	print <<<EOT
		<form name="change" action="" method="post" id="change">
			<fieldset>
				<legend>Change Password</legend>
				<label for="oldPass">Old Password</label>
				<input type="password" name="old_password" id="oldPass" />
				<br />
				<label for="newPass">New Password</label>
				<input type="password" name="new_password" id="newPass" />
				<br />
				<label for="confirmNewPass">Confirm New Password</label>
				<input type="password" name="c_new_password" id="confirmPass" />
				<br />
				<input type="hidden" name="action" value="update" />
				<input type="submit" name="changePass" id="changePass" value="Change Password Now!" />
			</fieldset>
		</form>
EOT;
	return true;
}
?>
