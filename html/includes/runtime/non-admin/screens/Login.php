<?php

class Screen_Login extends Screen
{
	public function head()
	{
		print( <<<EOF
			<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
EOF );
		return true;
	}

	public function validate()
	{
		$email 		= $this->input()->value_str_POST( "email" );
		$password	= $this->input()->value_str_POST( "password" );
		$turnstile	= $this->input()->value_str_POST( "cf-turnstile-response" );
		$settings	= $this->settings();

		if ( Functions::Turnstile_Active( $settings ) && !Functions::Turnstile_Validate( $settings, $turnstile ) )
		{
			$this->addValidationError( "Invalid validation token" );
		}

		if ( !$this->auth()->validate_login( $email, $password, $user ) )
		{
			$this->addValidationError( 'Invalid email or password' );
		}
		else if ( !$user[ 'active' ] )
		{
			$this->addValidationError( sprintf( 'Your account is currently inactive: %s', $user[ 'message' ] ) );
		}

		if ( !$this->hasValidationErrors() )
		{
			$this->setValidationData( $user );
		}

		return true;
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
		$settings = $this->settings();

		if ( $this->auth()->getUserID() )
		{
			header( sprintf( 'Location: %s', $settings[ 'domain_url' ] ) );
			return true;
		}

		$email = $this->input()->value_str_POST( "email" );

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
			<div class="cf-turnstile" data-sitekey="<?php print htmlentities( $settings[ 'turnstile_sitekey' ] ); ?>" data-appearance="interaction-only"></div>
			<input type="hidden" name="update" value="1" />
			<input type="submit" name="login" id="login" value="Login" /><br />
			<a href="?screen=forgot_password" title="Forgotten Password?">Forgotten Password?</a>
	  </fieldset>
	</form>
<?php
		return true;
	}
}
