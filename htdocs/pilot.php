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

    <form action="index.php" method ="get">
      Find a pilot: <input type="text" name="psearch" size="20" />
    </form>

<?php

  if(isset($rvar_psearch)) {
    print "<table>\n";
    for($i=0; $i<count($pilotlist); $i++) {
      print "<tr>";
      print "<td><a href=\"pilot.php?pilot=$pilotlist[$i]\">" . pilot_name($pilotlist[$i]) . "</a></td>";
      print "<td>" . hobbs(pilot_hours($pilotlist[$i])) . "</td>";
      print "</tr>";
    }
    print "</table>\n";
  }

?>

<?php

  include "foot.inc";

?>
