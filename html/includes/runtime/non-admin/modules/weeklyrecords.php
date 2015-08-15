<?php
function Module_Head( &$db, &$user, &$settings, &$jquery )
{
	$jquery = '$.fn.load_weeklyrecords();';
	
	return true;
}

function Module_Content( &$db, &$user )
{
	Validation::User( $user->id );

	print '<h1>User Records by Week</h1>';
	print '<div id="loading_weeklyrecords">Loading...</div>';
	
	return true;
}
?>
