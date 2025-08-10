<?php

class Screen_ControlPanel extends Screen_User
{
	public function validate()
	{
		$action = $this->input()->value_GET_str( "action" );

		if ( $action == 'changeemail' )
		{
			$db_users	= $this->db()->users();
			$email 		= $this->input()->value_POST_str( "email" );
			$cemail 	= $this->input()->value_POST_str( "cemail" );
			$pass 		= $this->input()->value_POST_str( "pass" );
			$errors		= array();

			if ( !Validation::Email( $email ) )
			{
				array_push( $errors, 'Invalid email address.' );
			}
			else if ( $email != $cemail )
			{
				array_push( $errors, 'Email address\' do not match.' );
			}
			else if ( $db_users->Load_Email( $email, $loaded_user ) && $loaded_user[ 'id' ] != $this->auth()->getUserID() )
			{
				array_push( $errors, 'The email address is already in use.' );
			}

			if ( !password_verify( $pass, $this->auth()->getUser()[ 'password' ] ) )
			{
				array_push( $errors, 'Invalid password.' );
			}

			if ( !empty( $errors ) )
			{
				return $this->setValidationErrors( $errors );
			}

			return $this->setValidationData( array( "email" => $email ) );
		}

		if ( $action == 'changepassword' )
		{
			$new_password 	= $this->input()->value_POST_str( "new_password" );
			$c_new_password = $this->input()->value_POST_str( "c_new_password" );
			$old_password	= $this->input()->value_POST_str( "old_password" );
			$errors			= array();

			if ( strlen( $new_password ) < 5 )
			{
				array_push( $errors, 'Password must be at least 5 characters.' );
			}
			else if ( $new_password !== $c_new_password )
			{
				array_push( $errors, 'Passwords do not match.' );
			}

			if ( !password_verify( $old_password, $this->auth()->getUser()[ 'password' ] ) )
			{
				array_push( $errors, 'Your old password does not match the password on file.' );
			}

			if ( !empty( $errors ) )
			{
				return $this->setValidationErrors( $errors );
			}

			return $this->setValidationData( array( "password" => password_hash( $new_password, PASSWORD_DEFAULT ) ) );
		}

		return true;
	}

	public function update( $data )
	{
		$db_users	= $this->db()->users();
		$action		= $this->input()->value_GET_str( "action" );

		if ( $action == 'changeemail' )
		{
			$user 				= $this->auth()->getUser();
			$user[ 'email' ] 	= $data[ 'email' ];

			$db_users->Update( $user );

			return $this->setUpdateMessage( "Your email adddress has been updated." );
		}

		if ( $action == 'changepassword' )
		{
			$user				= $this->auth()->getUser();
			$user[ 'password' ]	= $data[ 'password' ];

			$db_users->Update( $user );

			return $this->setUpdateMessage( "Your password has been updated." );
		}

		return true;
	}

	public function content()
	{
		$action = $this->input()->value_GET_str( 'action' );

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
			<p><a href="?screen=delete_account" title="Delete Account">Delete Account</a></p>
			<p><a href="?screen=user_preferences" title="User Preferences">User Preferences</a></p>
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
