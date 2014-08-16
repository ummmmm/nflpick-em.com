<?php
session_start();
ob_start();

require_once( 'includes/classes/functions.php' );
require_once( 'includes/classes/database.php' );
require_once( 'includes/classes/validation.php' );
require_once( 'includes/classes/user.php' );
require_once( 'includes/classes/settings.php' );
require_once( 'includes/classes/mail.php' );

$db					= new Database( $connection );
$user				= new User( $db );
$settings			= new Settings1( $db );

$screen_validation 	= Functions::ValidateScreen( $db, $user, $extra_screen_content );
$module_head		= true;
$module_content		= false;

if ( $screen_validation )
{
	if ( function_exists( 'Module_Head' ) )
	{
		ob_start();
		$module_head = call_user_func_array( 'Module_Head', array( &$db, &$user, &$settings, &$jquery ) );
		$module_head_output = ob_get_contents();
		ob_clean();
	}

	ob_start();
	$module_content = call_user_func_array( 'Module_Content', array( &$db, &$user, &$settings ) );
	$module_content_output = ob_get_contents();
	ob_clean();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
<title><?php print $settings->site_title; ?></title>
<link rel="icon" type="image/x-icon" href="/favicon.ico" />
<link rel="stylesheet" type="text/css" href="/static/css/styles.css" media="screen" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js" type="text/javascript"></script>
<script src="/static/javascript/jqueryui.js" type="text/javascript"></script>
<script src="/static/javascript/javascript.js" type="text/javascript"></script>
<script type="text/javascript">$( document ).ready( function() { $.fn.load_poll(); } );</script>
<?php
	if ( Functions::Get( 'view' ) === 'admin' && $user->account[ 'admin' ] )
	{
		print '<script src="/static/javascript/admin.js" type="text/javascript"></script>';
	}

	if ( $module_head === true && $module_content === true )
	{
		print $module_head_output;
	}

	print '<script type="text/javascript">';
	print "\nvar json_url = '/json.php?token={$user->token}';\n";
	print "var token = '{$user->token}';\n";
	print "\$( document ).ready( function() { {$jquery} } );";
	print "</script>\n";
?>
<script type="text/javascript">
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', 'UA-2438906-1']);
	_gaq.push(['_trackPageview']);

	(function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
</script>
</head>
<body>
<div class="container">
  <div class="header">
    <div class="title">
      <h1><a href="/" title="<?php print $settings->site_title; ?>"><?php print $settings->site_title; ?></a></h1>
    </div>
  </div>
  <div class="navigation">
    <?php Functions::TopNavigation( $db, $user ); ?>
  </div>
  <div class="main">
    <div class="content">
	<?php
		if ( $module_head === true && $module_content === true )
		{
			print $module_content_output;
			print $extra_screen_content;
		} else {
			Functions::OutputError();
		}
	?>
    </div>
    <div class="sidenav">
      <?php Functions::UserNavigation( $db, $user ); ?>
      <h1>Quick Links</h1>
      <ul>
        <li><a href="/?module=register" title="Register">Register</a></li>
        <li><a href="/?module=login" title="Login">Login</a></li>
        <li><a href="/?module=schedule" title="View Schedule">Schedule</a></li>
        <li><a href="/?module=contact" title="Contact Us">Contact Us</a></li>
        <li><a href="/?module=online" title="Online Users">Online Users</a></li>
      </ul>
      <h1>Poll</h1>
		<div id="loading_polls_nav"></div>
    </div>
    <br clear="all" />
  </div>
  <div class="footer">
	  &copy; 2007-2012 <a href="/">NFLPick-Em.com</a>. Template design by <a href="http://templates.arcsin.se" target="_blank" title="Designed By Arcsin">Arcsin</a>.
  </div>
</div>
</body>
</html>
<?php
ob_end_flush();
?>
