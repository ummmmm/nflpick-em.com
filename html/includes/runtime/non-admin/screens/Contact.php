<?php

require_once( "includes/classes/Mail.php" );

class Screen_Contact extends Screen
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
		$settings	= $this->settings();

		$name 		= Functions::Post( "name" );
		$email		= Functions::Post( "email" );
		$subject	= Functions::Post( "subject" );
		$message	= Functions::Post( "message" );
		$turnstile	= Functions::Post( "cf-turnstile-response" );
		$errors		= array();

		if ( $name == "" )
		{
			array_push( $errors, "Please enter your name." );
		}

		if ( $email == "" )
		{
			array_push( $errors, "Please enter an email address." );
		}

		if ( $message == "" )
		{
			array_push( $errors, "Please enter a message." );
		}

		if ( $subject == "" )
		{
			$subject = "No Subject";
		}

		if ( Functions::Turnstile_Active( $settings ) && !Functions::Turnstile_Validate( $settings, $turnstile ) )
		{
			array_push( $errors, "Invalid validation token" );
		}

		if ( !empty( $errors ) )
		{
			return $this->setValidationErrors( $errors );
		}

		return $this->setValidationData( array( "name" => $name, "email" => $email, "subject" => $subject, "message" => $message ) );
	}

	public function update( $data )
	{
		$settings	= $this->settings();
		$message	= sprintf( "Name: %s<br />Email: %s<br /><br />Message: %s", $data[ 'name' ], $data[ 'email' ], $data[ 'message' ] );
		$mail 		= new Mail( $settings[ 'domain_email' ], $data[ 'subject' ], $message );

		$mail->replyto( $data[ 'email' ] );

		if ( $mail->send() === false )
		{
			throw new NFLPickEmException( 'Failed to send email, please try again later' );
		}

		return $this->setUpdateMessage( "Your message has been sent and you should receive a response within the next 24 hours." );
	}

	public function content()
	{
		$settings	= $this->settings();
		$name 		= Functions::Post( 'name' );
		$email		= Functions::Post( 'email' );
		$subject	= Functions::Post( 'subject' );
		$message	= Functions::Post( 'message' );

		if ( $this->auth()->getUserID() )
		{
			$name	= ( !$name ) ? $this->auth()->getUser()[ 'name' ] : $name;
			$email	= ( !$email ) ? $this->auth()->getUser()[ 'email' ] : $email;
		}
?>
		<form name="contact" action="?screen=contact" method="post" id="contact">
		<fieldset>
			<legend>Contact Form</legend>
			<label for="name">Your Name</label>
			<input type="text" name="name" id="name" value="<?php print htmlentities( $name ); ?>" />
			<br />
			<label for="email">Your Email</label>
			<input type="text" name="email" id="email" value="<?php print htmlentities( $email ); ?>" />
			<br />
			<label for="subject">Subject</label>
			<input type="text" name="subject" id="subject" value="<?php print htmlentities( $subject ); ?>" />
			<br />
			<label for="message">Message</label>
			<textarea name="message" cols="45" rows="10" id="message"><?php print htmlentities( $message ); ?></textarea>
			<br />
			<div class="cf-turnstile" data-sitekey="<?php print htmlentities( $settings[ 'turnstile_sitekey' ] ); ?>" data-appearance="interaction-only"></div>
			<input type="hidden" name="update" value="1" />
			<input type="submit" name="contact" id="contact" value="Send" />
		</fieldset>
	</form>
<?php
		return true;
	}
}
