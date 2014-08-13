<?php
function Module_JSON( &$db, &$user )
{
	if ( !Settings::Load( $db, $settings ) )
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
	$token							= Functions::Get( 'token' );
	
	if ( !Sessions::Validate( $db, $user->id, $token ) )
	{
		return JSON_Response_Error( 'NFL-SETTINGS_UPDATE-0', 'You do not have a valid token to complete this action.' );
	}
	
	if ( !Settings::Update( $db, $settings ) )
	{
		return JSON_Response_Global_Error();
	}
	
	return JSON_Response_Success();
}
?>