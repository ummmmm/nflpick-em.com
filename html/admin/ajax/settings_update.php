<?php

function Module_JSON( &$db, &$user )
{
	$token			= Functions::Get( 'token' );
	$db_settings 	= new Settings( $db );
	$db_sessions	= new Sessions( $db );

	if ( !Sessions::Validate( $db, $user->id, $token ) )
	{
		return JSON_Response_Error( 'NFL-SETTINGS_UPDATE-0', 'You do not have a valid token to complete this action.' );
	}

	if ( !$db_settings->Load( $settings ) )
	{
		return JSON_Response_Error();
	}

	$settings[ 'email_validation' ]	= Functions::Post_Active( 'email_validation' );
	$settings[ 'registration' ]		= Functions::Post_Active( 'registration' );
	$settings[ 'poll_options' ]		= Functions::Post_Int( 'poll_options' );
	$settings[ 'max_news' ]			= Functions::Post_Int( 'max_news' );
	$settings[ 'online' ]			= Functions::Post_Int( 'online' );
	$settings[ 'login_sleep' ]		= Functions::Post_Int( 'login_sleep' );
	$settings[ 'domain_url' ]		= Functions::Post( 'domain_url' );
	$settings[ 'domain_email' ]		= Functions::Post( 'domain_email' );
	$settings[ 'site_title' ]		= Functions::Post( 'site_title' );

	if ( $settings[ 'poll_options' ] <= 0 )			return JSON_Response_Error( '#Error#', 'Poll Options must be greater than 0' );
	elseif ( $settings[ 'max_news' ] <= 0 )			return JSON_Response_Error( '#Error#', 'Max News must be greater than 0' );
	elseif ( $settings[ 'online' ] <= 0 )			return JSON_Response_Error( '#Error#', 'Online must be greater than 0' );
	elseif ( $settings[ 'login_sleep' ] <= 0 )		return JSON_Response_Error( '#Error#', 'Login Sleep must be greater than 0' );
	elseif ( $settings[ 'domain_url' ] === '' )		return JSON_Response_Error( '#Error#', 'Domain URL cannot be blank' );
	elseif ( $settings[ 'site_title' ] === '' )		return JSON_Response_Error( '#Error#', 'Site Title cannot be blank' );
	
	if ( !$db_settings->Update( $settings ) )
	{
		return JSON_Response_Error();
	}
	
	return JSON_Response_Success();
}
