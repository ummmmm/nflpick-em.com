<?php

class Screen_Login extends Screen
{
	public function validate()
	{
		$db_users	= $this->db()->users();
		$email 		= Functions::Post( "email" );
		$password	= Functions::Post( "password" );

		if ( !$this->auth()->validate_login( $email, $password, $user ) )
		{
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
		$settings	= $this->settings();
		$user		= &$data;

		$this->auth()->login( $user[ 'id' ] );

		if ( !$user[ 'force_password' ] )	header( sprintf( 'Location: %s', $settings[ 'domain_url' ] ) );
		else								header( sprintf( 'Location: %s?screen=forgot_password&action=changepassword', $settings[ 'domain_url' ] ) );

		return true;
	}

	public function jquery()
	{
		print "$( '#loginEmail' ).focus();\n";

		return true;
	}

	public function content()
	{
		if ( $this->auth()->getUserID() )
		{
			$settings = $this->settings();

			header( sprintf( 'Location: %s', $settings[ 'domain_url' ] ) );
			return true;
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
