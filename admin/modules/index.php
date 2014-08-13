<?php
function Module_Content( &$db, &$user )
{
	print '<h1>Admin Panel</h1>';
	print '<p><a href="/?view=admin&module=games">Games</a></p>';
	print '<p><a href="/?view=admin&module=users">Users</a></p>';
	print '<p><a href="/?view=admin&module=weeks">Weeks</a></p>';
	print '<p><a href="/?view=admin&module=settings">Settings</a></p>';
	print '<p><a href="/?view=admin&module=news">News</a></p>';
	print '<p><a href="/?view=admin&module=polls">Polls</a></p>';
	print '<p><a href="/?view=admin&module=update_scores">Update Scores</a></p>';

	return true;
}
?>
