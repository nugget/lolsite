<?php
  $title = "pilot page";
  $cvs="\$Id$";
  $keywords="logbook online";

  include "init.inc";

  if(isset($rvar_psearch)) {
    $pilotlist = pilot_search($rvar_psearch);
    if(count($pilotlist) == 1) {
      header("Location: pilot.php?pilot=$pilotlist[0]");
      exit;
    }
  } 

  include "head.inc";

?>

  <div class="content">

   <h3>Stuff Goes Here</h3>

  </div>

<?php

  include "foot.inc";

?>
