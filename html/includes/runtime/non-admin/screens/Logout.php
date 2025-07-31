<?php

class Screen_Logout extends Screen
{
	public function content()
	{
		$settings = $this->settings();

		$this->auth()->logout();

		header( sprintf( 'location: %s', $settings[ 'domain_url' ] ) );

		return true;
	}
}
