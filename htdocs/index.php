<?php
  $title = "main page";
  $cvs="\$Id$";
  $keywords="logbook online";

  include "init.inc";

  if(isset($rvar_psearch)) {
    $pilotdesc = "Pilots Matching [$rvar_psearch]";
    $pilotlist = pilot_search($rvar_psearch);
  } else {
    $pilotdesc = "Top Pilots";
    $pilotlist = pilot_search("");
  }
  usort($pilotlist,"pilot_cmp");
  if(count($pilotlist) == 1) {
    header("Location: pilot.php?pilot=$pilotlist[0]");
    exit;
  }

  include "head.inc";

?>

  <div id="block1">
    <h3><?php print $pilotdesc; ?></h3>
    <form action="index.php" method ="get">
      Search: <input type="text" name="psearch" size="20" />
    </form>
    <?php
      if(isset($pilotlist)) {
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
  </div>

  <div id="block2">
    <h3>Top Airports</h3>
    <form action="index.php" method ="get">
      Search: <input type="text" name="asearch" size="20" />
    </form>
  </div>

<?php

  include "foot.inc";

?>
