<?php

class Screen_Default extends Screen_Admin
{
	public function content()
	{
		print '<h1>Admin Panel</h1>';
		print '<p><a href="?view=admin&screen=games">Games</a></p>';
		print '<p><a href="?view=admin&screen=users">Users</a></p>';
		print '<p><a href="?view=admin&screen=weeks">Weeks</a></p>';
		print '<p><a href="?view=admin&screen=settings">Settings</a></p>';
		print '<p><a href="?view=admin&screen=news">News</a></p>';
		print '<p><a href="?view=admin&screen=polls">Polls</a></p>';
		print '<p><a href="?view=admin&screen=update_scores">Update Scores</a></p>';
		print '<p><a href="?view=admin&screen=weekly_records">Weekly Records</a></p>';
		print '<p><a href="?view=admin&screen=perfect_weeks">Perfect Weeks</a></p>';
		print '<p><a href="?view=admin&screen=failed_logins">Failed Logins</a></p>';

		return true;
	}
}
