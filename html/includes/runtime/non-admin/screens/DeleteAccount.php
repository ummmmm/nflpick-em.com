<?php

class Screen_DeleteAccount extends Screen_User
{
	public function requirements()
	{
		return array( 'user' => true, 'token' => true );
	}

	public function validate()
	{
		$db_users = $this->db()->users();
		$password = Functions::Post( 'password' );

		if ( !$this->auth()->validate_login( $this->_auth->getUser()[ 'email' ], $password, $null ) )
		{
			return $this->setValidationErrors( 'Invalid password' );
		}

		return true;
	}

	public function update( $data )
	{
		$settings	= $this->settings();
		$db_users	= $this->db()->users();

		$this->auth()->logout();

		$db_users->Delete( $this->_auth->getUserID() );

		header( sprintf( 'Location: %s', $settings[ 'domain_url' ] ) );

		return true;
	}

	public function content()
	{
		$token = htmlentities( $this->_auth->getToken() );

		print <<<EOT
		<form action="" method="post">
			<fieldset>
				<legend>Delete Account</legend>
				<div style="color: red; font-weight: bold;">THIS ACTION CANNOT BE UNDONE!</div>
				<div style="color: red; font-weight: bold;">THIS ACTION CANNOT BE UNDONE!</div>
				<div style="color: red; font-weight: bold;">THIS ACTION CANNOT BE UNDONE!</div><br />
				<label for="password">Password</label>
				<input type="password" name="password" /><br />
				<input type="hidden" name="update" value="1" />
				<input type="hidden" name="token" value="$token" />
				<input type="submit" name="deleteAccount" value="Delete Account" />
			</fieldset>
		</form>
EOT;

		return true;
	}
}
