<?php
  // $Id$

  $utime_start = microtime();

  if(!isset($template)) $template = "default";
  if(!isset($keywords)) $keywords = "logbook online ityt.com";
  if(isset($rvar_pilot)) {
    $pilot_name = pilot_name($rvar_pilot);
    $working_title = $pilot_name . "'s " . $title;
  } else {
    $pilot_name = '';
    $working_title = $title;
  }


?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
 <head>
  <title><?php print $GLOBALS['server_tag'] . " lol :: " . $working_title; ?></title>
  <meta http-equiv="CVS" content="<?php print $cvs ?>" />
  <meta http-equiv="pragma" content="no-cache" />
  <meta name="keywords" content="<?php print $keywords ?>" />
  <meta name="copyright" content="Copyright 2002-<?php print date ("Y"); ?> Daniel A. Baker and David C. McNett.  All Rights Reserved." />
  <link rel="stylesheet" href="<?php print "$GLOBALS[baseurl]/css/$template.css" ?>" type="text/css" />
  <?php if(isset($cssstyle)) { print "<link rel=\"stylesheet\" href=\"$GLOBALS[baseurl]/css/$cssstyle.css\" type=\"text/css\" />\n"; } ?>
  <?php if(isset($refresh)) { print "<meta http-equiv=\"refresh\" content=\"$refresh\">\n"; } ?>
 </head>

 <body>
   <div class="headerleft">
     <p class="header">
       <strong><?php print $GLOBALS['server_tag']; ?> lol</strong>
       <br />
       logbook online
     </p>
   </div>
   <div class="headerright">
     <?php
       if(isset($rvar_pilot)) {
         print hobbs(pilot_hours($rvar_pilot));
       }
     ?>
   </div>


   <h1><?php print $working_title ?></h1>
   <div id="menu">
     <a class="global" href=".">Main</a>
     <?php if(is_user()) print "<a class=\"global\" href=\"logout.php\">Logout</a>"; ?>
     <?php if(!is_user()) print "<a class=\"global\" href=\"login.php\">Login</a>"; ?>

     <?php if(is_valid()) print "&nbsp;"; ?>

     <?php if(is_valid()) print "<a href=\"pilot.php?pilot=$rvar_pilot\">" . pilot_name($rvar_pilot)  . "'s lol</a>"; ?>
     <?php if(is_valid()) print "<a href=\"display_logbook.php?pilot=$rvar_pilot\">Logbook</a>"; ?>
     <?php if(is_valid()) print "<a href=\"display_aircraft.php?pilot=$rvar_pilot\">Aircraft</a>"; ?>
     <?php if(is_valid()) print "<a href=\"display_airports.php?pilot=$rvar_pilot\">Airports</a>"; ?>
     <?php if(is_valid()) print "<a href=\"display_pax.php?pilot=$rvar_pilot\">Passengers</a>"; ?>
     <?php if(is_valid()) print "<a href=\"statistics.php?pilot=$rvar_pilot\">Statistics</a>"; ?>
   </div>

<?php
  if(isset($notice_title)) {
    print "
    <div class=\"warning\">
      <h2>$notice_title</h2>
      <p>$notice_text</p>
    </div>";
  }
  if(isset($error_title)) {
    print "
    <div class=\"warning\">
      <h2>$error_title</h2>
      <p>$error_text</p>
    </div>";
    include "foot.inc";
    exit;
  }
?>
