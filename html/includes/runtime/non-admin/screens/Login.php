<?php

class Screen_Login extends Screen
{
	public function validate()
	{
		$db_users	= $this->db()->users();
		$email 		= Functions::Post( "email" );
		$password	= Functions::Post( "password" );

		if ( !$db_users->validateLogin( $email, $password, $user ) )
		{
			$db_settings		= $this->db()->settings();
			$db_failed_logins 	= $this->db()->failedlogins();
			$db_failed_logins->Insert( $email );

			if ( $db_settings->Load( $settings ) && $settings[ 'login_sleep' ] > 0 )
			{
				usleep( $settings[ 'login_sleep' ] * 1000 );
			}

			return $this->setValidationErrors( array( "Invalid email or password" ) );
		}

		if ( !$user[ 'active' ] )
		{
			return $this->setValidationErrors( array( $user[ 'message' ] ) );
		}

		return $this->setValidationData( $user );
	}

	public function update( $data )
	{
		$user 			= &$data;
		$db_sessions 	= $this->db()->sessions();

		$cookieid	= sha1( session_id() );
		$token		= sha1( uniqid( rand(), TRUE ) );
		$session	= array( 'token' => $token, 'cookieid' => $cookieid, 'userid' => $user[ 'id' ] );

		setcookie( 'session', $cookieid, time() + 60 * 60 * 24 * 30, INDEX, '', true, true );

		if ( !$db_sessions->Insert( $session ) )
		{
			return $this->setDBError();
		}

		if ( $user[ 'force_password' ] )
		{
			header( "Location: ?screen=forgot_password&action=changepassword" );
			die();
		}

		header( sprintf( 'Location: %s', INDEX ) );

		return true;
	}

	public function jquery()
	{
		print "$( '#loginEmail' ).focus();\n";

		return true;
	}

	public function content()
	{
		if ( $this->_auth->getUserID() )
		{
			header( sprintf( "Location: %s", INDEX ) );
			die();
		}

		$email = Functions::Post( "email" );

?>
<form action="?screen=login" method="post">
	  <fieldset>
			<legend>Enter Your Login Info</legend>
			<label for="email">Email Address</label>
			<input type="text" name="email" id="loginEmail" value="<?php print htmlentities( $email ); ?>" />
			<br />
			<label for="password">Password</label>
			<input type="password" name="password" id="loginPassword" value="" />
			<br />
			<input type="hidden" name="update" value="1" />
			<input type="submit" name="login" id="login" value="Login" /><br />
			<a href="?screen=forgot_password" title="Forgotten Password?">Forgotten Password?</a>
	  </fieldset>
	</form>
<?php
		return true;
	}
}
