<?php
session_start();
ob_start();

require_once( 'includes/classes/functions.php' );
require_once( 'includes/classes/Database.php' );
require_once( 'includes/classes/validation.php' );
require_once( "includes/classes/Screen.php" );

$screen 			= new Screen();
$db					= new Database();
$users				= new Users( $db );
$settings			= new Settings( $db );

$admin 		= Functions::Get( "view" ) == "admin" ? true : false;
$screen1	= Functions::Get( "screen" ) == "" ? "index" : Functions::Get( "screen" );
$update		= Functions::Post( "update" ) == "update" ? true : false;

$screen->initialize( $admin, ucfirst( $screen1 ) );
$success			= $screen->execute( $update );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="utf-8" />
<title><?php printf( '%s', htmlentities( $settings->site_title ) ); ?></title>
<base href="<?php print $settings->domain_url; ?>" />
<link rel="icon" type="image/x-icon" href="static/favicon.ico" />
<link rel="stylesheet" type="text/css" href="static/css/styles.css" media="screen" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js" type="text/javascript"></script>
<script src="static/javascript/jqueryui.js" type="text/javascript"></script>
<script src="static/javascript/javascript.js" type="text/javascript"></script>
<script type="text/javascript">
	var json_url = <?php print json_encode( sprintf( "json.php?token=%s", urlencode( $users->token ) ) ); ?>;
	var token = <?php print json_encode( $users->token ); ?>;
</script>
<script type="text/javascript">$( document ).ready( function() { $.fn.load_poll(); } );</script>
<?php
	if ( $admin )
	{
		print '<script type="text/javascript src="static/javascript/admin.js"></script>';
	}

	if ( $success )
	{
		print $screen->getHeadData();
	}
?>
<script>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	ga('create', 'UA-2438906-1', 'auto');
	ga('send', 'pageview');
</script>
</head>
<body>
<div class="container">
  <div class="header">
    <div class="title">
      <h1><a href="" title="<?php printf( "%s", htmlentities( $settings->site_title ) ); ?>"><?php printf( "%s", htmlentities( $settings->site_title ) ); ?></a></h1>
    </div>
  </div>
  <div class="navigation">
    <?php Functions::TopNavigation( $db, $users ); ?>
  </div>
  <div class="main">
    <div class="content">
	<?php
		if ( $success )
		{
			print $screen->getValidationErrorsData();
			print $screen->getContentData();
		}
		else
		{
			Functions::OutputError( $screen->getError() );
		}
	?>
    </div>
    <div class="sidenav">
      <?php Functions::UserNavigation( $db, $users ); ?>
      <h1>Quick Links</h1>
      <ul>
      <?php
      	if ( $users->logged_in === false )
      	{
      		print '<li><a href="?screen=register" title="Register">Register</a></li>';
      		print '<li><a href="?screen=login" title="Login">Login</a></li>';
      	}
      ?>
        <li><a href="?screen=schedule" title="View Schedule">Schedule</a></li>
        <li><a href="?screen=contact" title="Contact Us">Contact Us</a></li>
        <li><a href="?screen=online" title="Online Users">Online Users</a></li>
      </ul>
      <h1>Poll</h1>
		<div id="loading_polls_nav"></div>
    </div>
    <br clear="all" />
  </div>
  <div class="footer">
	  &copy; 2007-2015 <a href="">NFLPick-Em.com</a>. Template design by <a href="http://templates.arcsin.se" target="_blank" title="Designed By Arcsin">Arcsin</a>.
  </div>
</div>
</body>
</html>
<?php
ob_end_flush();
?>
