<?php
require_once( 'includes/classes/Setup.php' );

$setup = new Setup();

if ( !$setup->Install() )
{
	die( $setup->Get_Error() );
}

die( 'The NFL Pick-Em site has successfully been configured' );

?>
