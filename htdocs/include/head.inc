<?php
  // $Id$

  $utime_start = microtime();

  print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

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
<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
     <a href=".">Main</a>
     <?php if(is_user()) print "<a href=\"logout.php\">Logout</a>"; ?>
     <?php if(!is_user()) print "<a href=\"login.php\">Login/Register</a>"; ?>
   </div>

<?php
  if(isset($notice_title)) {
    print "
    <div class=\"warning\">
      <h2>$notice_title</h2>
      <p>$notice_text</p>
    </div>";
  }
?>
