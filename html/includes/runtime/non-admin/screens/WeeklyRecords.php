<?php

class Screen_WeeklyRecords extends Screen
{
	public function requirements()
	{
		return array( "user" => true );
	}

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
