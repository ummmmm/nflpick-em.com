<?php

class Screen_Users extends Screen_Admin
{
	public function jquery()
	{
		print "$.fn.sort( 'LoadUsers', 'name', $.fn.sort_user_callback );";

		return true;
	}

	public function content()
	{
		print '<h1>Users</h1>';
		print '<div class="sortby">Sort By: ';
		print '<a href="javascript:;" id="name" direction="asc" onclick="$.fn.sort( \'LoadUsers\', \'name\', $.fn.sort_user_callback );">Name</a>';
		print '<a href="javascript:;" id="current_place" direction="asc" onclick="$.fn.sort( \'LoadUsers\', \'current_place\', $.fn.sort_user_callback );">Current Place</a>';
		print '<a href="javascript:;" id="last_on" direction="desc" onclick="$.fn.sort( \'LoadUsers\', \'last_on\', $.fn.sort_user_callback );">Last Active</a>';
		print '<a href="javascript:;" id="paid" direction="asc" onclick="$.fn.sort( \'LoadUsers\', \'paid\', $.fn.sort_user_callback );">Paid</a>';
		print '<a href="javascript:;" id="pw_opt_out" direction="asc" onclick="$.fn.sort( \'LoadUsers\', \'pw_opt_out\', $.fn.sort_user_callback );">Perfect Week</a>';
		print '<a href="javascript:;" id="failed_logins" direction="asc" onclick="$.fn.sort( \'LoadUsers\', \'failed_logins\', $.fn.sort_user_callback );">Failed Logins</a>';
		print '<a href="javascript:;" id="active_sessions" direction="desc" onclick="$.fn.sort( \'LoadUsers\', \'active_sessions\', $.fn.sort_user_callback );">Active Sessions</a>';
		print '<a href="javascript:;" id="remaining" direction="asc" onclick="$.fn.sort( \'LoadUsers\', \'remaining\', $.fn.sort_user_callback );">Remaining Picks</a>';
		print '</div>';
		print '<div id="users_loading">Loading...</div>';
		print '<div id="user_edit">';
		print '<div id="user_edit_dialog">Edit User</div>';
		print '<table>';
		print '<tr>';
		print '<td valign="top" nowrap>First Name:</td>';
		print '<td width="100%"><input type="text" id="user_edit_first_name" /></td>';
		print '</tr>';
		print '<tr>';
		print '<td valign="top" nowrap>Last Name:</td>';
		print '<td width="100%"><input type="text" id="user_edit_last_name" /></td>';
		print '</tr>';
		print '<tr>';
		print '<td valign="top" nowrap>Password:</td>';
		print '<td width="100%"><input type="password" id="user_edit_password" /></td>';
		print '</tr>';
		print '<tr>';
		print '<td valign="top" nowrap>Verify Password:</td>';
		print '<td width="100%"><input type="password" id="user_edit_verify_password" /></td>';
		print '</tr>';
		print '<tr>';
		print '<td valign="top" nowrap>Message:</td>';
		print '<td width="100%"><textarea id="user_edit_message" cols="50"></textarea><br />Enter any message to deactivate the user</td>';
		print '</tr>';
		print '</table>';
		print '<div class="buttons_left">
			<input type="button" id="user_edit_cancel" value="Cancel" />
		</div>
		<div class="buttons_right">
			<input type="button" id="user_edit_update" value="Update" />
		</div>';
		print '</div>';
		print '<div id="user_delete">';
		print '<div id="user_delete_dialog">Delete User</div>';
		print '<p>Are you absolutely sure you wish to delete this user?  This action cannot be undone.</p>';
		print '<table>';
		print '<tr>';
		print '<tr>';
		print '<td valign="top" nowrap>Password:</td>';
		print '<td width="100%"><input type="password" id="user_delete_password" /></td>';
		print '</tr>';
		print '</table>';
		print '<div class="buttons_left">
			<input type="button" id="user_delete_cancel" value="Cancel" />
		</div>
		<div class="buttons_right">
			<input type="button" id="user_delete_delete" value="Delete" />
		</div>';
		print '</div>';

		return true;
	}
}
