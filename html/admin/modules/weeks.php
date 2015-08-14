<?php

function Module_Head( &$db, &$user, &$settings, &$jquery )
{
	$jquery = '$.fn.load_weeks()';
	
	return true;
}

function Module_Content( &$db, &$user )
{
	print '<h1>Weeks</h1>';
	print '<div id="weeks_loading">Loading...</div>';
	
	return true;
}
