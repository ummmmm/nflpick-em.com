<?php

class Screen_UserPreferences extends Screen_User
{
	public function requirements()
	{
		return array( "user" => true, "token" => true );
	}

	public function validate()
	{
		$email_preference = $this->input()->value_bool_POST( 'email_preference', int: true );

		return $this->setValidationData( array( 'email_preference' => $email_preference ) );
	}

	public function update( $data )
	{
		$db_users					= $this->db()->users();
		$user						= $this->auth()->getUser();
		$user[ 'email_preference' ]	= $data[ 'email_preference' ];

		$db_users->Update( $user );

		$this->auth()->forceUserReload();

		return $this->setUpdateMessage( "Preferences saved." );
	}

	public function content()
	{
		$checked = $this->auth()->getUser()[ 'email_preference' ] ? 'checked' : '';

		$token = htmlentities( $this->auth()->getToken() );

		print <<<EOT
		<h1>User Preferences</h1>
		<form method="post">
			<label><input type="checkbox" name="email_preference" value="1" {$checked} /> Email Pick Reminders</label><br />
			<input type="hidden" name="update" value="1" />
			<input type="hidden" name="token" value="{$token}" />
			<input type="submit" value="Update" />
		</form>
EOT;
		return true;
	}
}
