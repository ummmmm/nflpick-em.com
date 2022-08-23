<?php

class Screen_WeeklyRecords extends Screen_User
{
	public function jquery()
	{
		print "$.fn.load_weeklyrecords();\n";

		return true;
	}

	public function content()
	{
		print '<h1>User Records by Week</h1>';
		print '<div id="loading_weeklyrecords">Loading...</div>';
		
		return true;
	}
}
