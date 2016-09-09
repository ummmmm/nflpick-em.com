<?php

class Screen_Weeks extends Screen_Admin
{
	public function jquery()
	{
		print "$.fn.load_weeks();";

		return true;
	}

	public function content()
	{
		print '<h1>Weeks</h1>';
		print '<div id="weeks_loading">Loading...</div>';

	return true;
	}
}
