<?php
  $title = "pilot page";
  $cvs="\$Id$";
  $keywords="logbook online";

  include "include/init.inc";

  if(!isset($rvar_pilot)) {
    $error_title = "No pilot specified";
    $error_text = "You must specify a pilot in order to view a logbook!";
  }

  include "include/head.inc";

?>

  <div class="content">

   <h3>Stuff Goes Here</h3>

  </div>

<?php

  include "include/foot.inc";

?>
