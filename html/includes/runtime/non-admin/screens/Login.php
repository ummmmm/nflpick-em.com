<?php

class Screen_Login implements iScreen
{
	public function __construct( Database &$db, Authentication &$auth, Screen &$screen )
	{
		$this->_db		= $db;
		$this->_auth	= $auth;
		$this->_screen	= $screen;
	}

	public function validate()
	{
		$db_users	= new Users( $this->_db );
		$email 		= Functions::Post( "email" );
		$password	= Functions::Post( "password" );

		if ( !$db_users->LoginValidate( $email, $password ) )
		{
			$db_settings		= new Settings( $this->_db );
			$db_failed_logins 	= new Failed_Logins( $this->_db );
			$db_failed_logins->Insert( $email );

			if ( $db_settings->Load( $settings ) && $settings[ 'login_sleep' ] > 0 )
			{
				usleep( $settings[ 'login_sleep' ] * 1000 );
			}

			return $this->_screen->setValidationErrors( "Invalid email or password" );
		}

		$this->_screen->setValidationData( array( "email" => $email ) );

		return true;
	}

	public function update( $validation_data )
	{
		return true;
	}

	public function head()
	{
		return true;
	}

	public function content()
	{
		if ( $this->_auth->authenticated )
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
			<input type="hidden" name="update" value="update" />
			<input type="submit" name="login" id="login" value="Login Now!" /><br />
			<a href="?module=forgotpassword" title="Forgotten Password?">Forgotten Password?</a>
	  </fieldset>
	</form>
<?php
		return true;
	}
}
