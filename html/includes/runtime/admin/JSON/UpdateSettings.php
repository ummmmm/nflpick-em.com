<?php

class JSON_UpdateSettings extends JSONAdminAction
{
	public function execute()
	{
		$db_settings = new Settings( $this->_db );
		$db_sessions = new Sessions( $this->_db );

		if ( !$db_settings->Load( $settings ) )
		{
			return $this->setDBError();
		}

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

		if ( $settings[ 'max_news' ] <= 0 )						return $this->setError( array( '#Error#', 'Max News must be greater than 0' ) );
		elseif ( $settings[ 'online' ] <= 0 )					return $this->setError( array( '#Error#', 'Online must be greater than 0' ) );
		elseif ( $settings[ 'login_sleep' ] <= 0 )				return $this->setError( array( '#Error#', 'Login Sleep must be greater than 0' ) );
		elseif ( $settings[ 'domain_url' ] === '' )				return $this->setError( array( '#Error#', 'Domain URL cannot be blank' ) );
		elseif ( $settings[ 'site_title' ] === '' )				return $this->setError( array( '#Error#', 'Site Title cannot be blank' ) );
		elseif ( $settings[ 'turnstile_sitekey' ] === '' )		return $this->setError( array( '#Error#', 'Turnstile site key cannot be blank' ) );
		elseif ( $settings[ 'turnstile_secretkey' ] === '' )	return $this->setError( array( '#Error#', 'Turnstile secret key cannot be blank' ) );
		
		if ( !$db_settings->Update( $settings ) )
		{
			return $this->setDBError();
		}
		
		return true;
	}
}
