<?php

class Screen_News extends Screen_Admin
{
	public function jquery()
	{
		print "$.fn.load_news();";

		return true;
	}

	public function content()
	{
		print '<a href="javascript:;" onclick="$.fn.add_news();">Add News</a>';
		print '<h1>News</h1>';
		print '<div id="news_loading">Loading...</div>';
		print <<<EOT
				<div id="news_addedit">
					<div id="news_addedit_dialog">Add/Edit News</div>
					<table>
						<tr>
							<td><b>Title:</b></td>
							<td><input type="text" name="title" id="news_addedit_title" /></td>
						</tr>
						<tr>
							<td valign="top"><b>Message:</b></td>
							<td><textarea name="message" id="news_addedit_message" cols="50" rows="10"></textarea></td>
						</tr>
						<tr>
							<td valign="top"><b>Status:</b></td>
							<td><input type="radio" name="active" value="1" id="news_addedit_active" /> Active<br />
								<input type="radio" name="active" value="0" id="news_addedit_inactive" /> Inactive
							</td>
						</tr>
					</table>
					
					<div class="buttons_left">
						<input type="button" id="news_addedit_cancel" value="Cancel" />
						<input type="button" id="news_addedit_delete" value="Delete" />
					</div>
					<div class="buttons_right">
						<input type="button" id="news_addedit_update" value="Update News" />
					</div>
				</div>
EOT;

	return true;
	}
}
