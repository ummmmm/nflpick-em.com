<?php
function Module_Head( &$db, &$user, &$settings, &$jquery )
{
	//$jquery = '$.fn.load_polls();';
	
	return true;
}

function Module_Content( &$db, &$user )
{
	print '<h1>Polls</h1>';
	print '<div id="loading_polls">Coming soon... Just kidding... Probably never</div>';
	
	return true;
}
?>
