<?php

class JSON_UpdateSettings extends JSONAdminAction
{
	public function execute()
	{
		$settings		= $this->settings();
		$db_settings	= $this->db()->settings();
		$db_sessions	= $this->db()->sessions();

		$settings[ 'email_validation' ]		= Functions::Post_Active( 'email_validation' );
		$settings[ 'registration' ]			= Functions::Post_Active( 'registration' );
		$settings[ 'max_news' ]				= Functions::Post_Int( 'max_news' );
		$settings[ 'online' ]				= Functions::Post_Int( 'online' );
		$settings[ 'login_sleep' ]			= Functions::Post_Int( 'login_sleep' );
		$settings[ 'domain_url' ]			= Functions::Post( 'domain_url' );
		$settings[ 'domain_email' ]			= Functions::Post( 'domain_email' );
		$settings[ 'site_title' ]			= Functions::Post( 'site_title' );
		$settings[ 'turnstile_sitekey' ]	= Functions::Post( 'turnstile_sitekey' );
		$settings[ 'turnstile_secretkey' ]	= Functions::Post( 'turnstile_secretkey' );

		if ( $settings[ 'max_news' ] <= 0 )			throw new NFLPickEmException( 'Max News must be greater than 0' );
		elseif ( $settings[ 'online' ] <= 0 )		throw new NFLPickEmException( 'Online must be greater than 0' );
		elseif ( $settings[ 'login_sleep' ] <= 0 )	throw new NFLPickEmException( 'Login Sleep must be greater than 0' );
		elseif ( $settings[ 'domain_url' ] === '' )	throw new NFLPickEmException( 'Domain URL cannot be blank' );
		elseif ( $settings[ 'site_title' ] === '' )	throw new NFLPickEmException( 'Site Title cannot be blank' );

		$db_settings->Update( $settings );

		return true;
	}
}
