<?php
function Module_Head( &$db, &$user, &$settings, &$jquery )
{
	$jquery = '$.fn.load_polls();';
	
	return true;
}

function Module_Content( &$db, &$user )
{
	print '<a href="javascript:;" onclick="$.fn.add_poll();">Add Poll</a>';
	print '<div id="polls_loading">Loading...</div>';
	print <<<EOT
			<div id="polls_addedit">
				<div id="polls_addedit_dialog">Add/Edit Provider</div>
				<a href="javascript:;" onclick="$.fn.add_poll_answer( null );">Add Answer</a>
				<table>
					<tr>
						<td><b>Question:</b></td>
						<td><input type="text" name="question" id="polls_addedit_question" /></td>
					</tr>
					<tr>
						<td valign="top"><b>Status:</b></td>
						<td><input type="radio" name="active" value="1" id="polls_addedit_active" /> Active<br />
							<input type="radio" name="active" value="0" id="polls_addedit_inactive" /> Inactive
						</td>
					</tr>
				</table>
				
				<div class="buttons_left">
					<input type="button" id="polls_addedit_cancel" value="Cancel" />
					<input type="button" id="polls_addedit_delete" value="Delete" />
				</div>
				<div class="buttons_right">
					<input type="button" id="polls_addedit_update" value="Update Poll" />
				</div>
			</div>
EOT;
	
	return true;
}
?>