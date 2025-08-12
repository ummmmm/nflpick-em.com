<?php

class JSON_UpdateSettings extends JSONAdminAction
{
	public function execute()
	{
		$settings		= $this->settings();
		$db_settings	= $this->db()->settings();
		$db_sessions	= $this->db()->sessions();

		$settings[ 'registration' ]			= $this->input()->value_bool( 'registration', int: true );
		$settings[ 'max_news' ]				= $this->input()->value_int( 'max_news' );
		$settings[ 'online' ]				= $this->input()->value_int( 'online' );
		$settings[ 'login_sleep' ]			= $this->input()->value_int( 'login_sleep' );
		$settings[ 'domain_url' ]			= $this->input()->value_str( 'domain_url' );
		$settings[ 'domain_email' ]			= $this->input()->value_str( 'domain_email' );
		$settings[ 'site_title' ]			= $this->input()->value_str( 'site_title' );
		$settings[ 'turnstile_sitekey' ]	= $this->input()->value_str( 'turnstile_sitekey' );
		$settings[ 'turnstile_secretkey' ]	= $this->input()->value_str( 'turnstile_secretkey' );

		if ( $settings[ 'max_news' ] <= 0 )			throw new NFLPickEmException( 'Max News must be greater than 0' );
		elseif ( $settings[ 'online' ] <= 0 )		throw new NFLPickEmException( 'Online must be greater than 0' );
		elseif ( $settings[ 'login_sleep' ] <= 0 )	throw new NFLPickEmException( 'Login Sleep must be greater than 0' );
		elseif ( $settings[ 'domain_url' ] === '' )	throw new NFLPickEmException( 'Domain URL cannot be blank' );
		elseif ( $settings[ 'site_title' ] === '' )	throw new NFLPickEmException( 'Site Title cannot be blank' );

		$db_settings->Update( $settings );

		return true;
	}
}
