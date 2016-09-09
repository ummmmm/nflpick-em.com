<?php

require_once( "includes/classes/Mail.php" );

class Screen_Contact extends Screen
{
	public function validate()
	{
		$name 		= Functions::Post( "name" );
		$email		= Functions::Post( "email" );
		$subject	= Functions::Post( "subject" );
		$message	= Functions::Post( "message" );
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

		if ( !empty( $errors ) )
		{
			return $this->setValidationErrors( $errors );
		}

		return $this->setValidationData( array( "name" => $name, "email" => $email, "subject" => $subject, "message" => $message ) );
	}

	public function update( $data )
	{
		$db_settings = new Settings( $this->_db );

		if ( !$db_settings->Load( $settings ) )
		{
			return $this->setDBError();
		}

		$message	= sprintf( "Name: %s<br />Email: %s<br /><br />Message: %s", $data[ 'name' ], $data[ 'email' ], $data[ 'message' ] );
		$mail 		= new Mail( $settings[ 'domain_email' ], $data[ 'subject' ], $message );

		$mail->replyto( $data[ 'email' ] );

		if ( $mail->send() === false )
		{
			return $this->setError( array( "#Error#", "Failed to send email.  Please try again later." ) );
		}

		return $this->setUpdateMessage( "Your message has been sent and you should receive a response within the next 24 hours." );
	}

	public function content()
	{
		$name 		= Functions::Post( 'name' );
		$email		= Functions::Post( 'email' );
		$subject	= Functions::Post( 'subject' );
		$message	= Functions::Post( 'message' );

		if ( $this->_auth->getUserID() )
		{
			$name	= ( !$name ) ? $this->_auth->getUser()[ 'name' ] : $name;
			$email	= ( !$email ) ? $this->_auth->getUser()[ 'email' ] : $email;
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
			<textarea name="message" cols="50" rows="10" id="message"><?php print htmlentities( $message ); ?></textarea>
			<br />
			<input type="hidden" name="update" value="1" />
			<input type="submit" name="contact" id="contact" value="Send" />
		</fieldset>
	</form>
<?php
		return true;
	}
}
