<?php
  $title = "main page";
  $cvs="\$Id$";
  $keywords="logbook online";

  include "include/init.inc";

  if(isset($rvar_psearch)) {
    $pilotdesc = "Pilots Matching [$rvar_psearch]";
    $pilotlist = pilot_search($rvar_psearch);
  } else {
    $pilotdesc = "Top Pilots";
    $pilotlist = pilot_search("");
  }
  usort($pilotlist,"pilot_cmp");
  $pilotlist = array_slice($pilotlist,0,5);

  if(isset($rvar_asearch)) {
    $airportdesc = "Airports Matching [$rvar_asearch]";
    $airportlist = airport_search($rvar_asearch);
  } else {
    $airportdesc = "Top Airports";
    $airportlist = airport_search("");
  }
  if(isset($airportlist)) {
    usort($airportlist,"airport_cmp");
    $airportlist = array_slice($airportlist,0,10);
  }

  include "include/head.inc";

  $flightslist = logbook_recent();
?>

  <div id="block">
  <div id="block1">
    <h3><?php print $pilotdesc; ?></h3>
    <?php
      if(isset($pilotlist)) {
        print "<table>\n<tr><th>Pilot</th><th>Hours</th></tr>";
        for($i=0; $i<count($pilotlist); $i++) {
          $detaillink = "pilot.php?pilot=$pilotlist[$i]";
          ?>
          <tr class="<?php print $class; ?>" onMouseOver=this.style.backgroundColor="#ffffff"
                                     onMouseOut=this.style.backgroundColor=""
                                     onclick="window.location.href='<?php print $detaillink; ?>'" >
           <td style="width: 100px;"><?php print pilot_name($pilotlist[$i]); ?></td>
           <td><?php print hobbs(pilot_hours($pilotlist[$i])); ?></td>
          </tr>
          <?php
        }
        print "</table>\n";
      }
    ?>
    <form action="index.php" method ="get">
      Search: <input type="text" name="psearch" size="10" />
    </form>
  </div>

  <div id="block2">
    <h3><?php print $airportdesc; ?></h3>
    <?php
      if(isset($airportlist)) {
        print "<table>\n<tr><th>Airport</th><th>Visits</th></tr>";
        for($i=0; $i<count($airportlist); $i++) {
          $buf2 = airport_detail($airportlist[$i]);
          $detaillink = "airport.php?ident=$airportlist[$i]";
          ?>
          <tr class="<?php print $class; ?>" onMouseOver=this.style.backgroundColor="#ffffff"
                                     onMouseOut=this.style.backgroundColor=""
                                     onclick="window.location.href='<?php print $detaillink; ?>'" >
           <td><?php print airport_name($airportlist[$i]); ?></td>
           <td><?php print $buf2['visits']; ?></td>
          </tr>
          <?php
        }
        print "</table>\n";
      }
    ?>
    <form action="index.php" method ="get">
      Search: <input type="text" name="asearch" size="10" />
    </form>
  </div>

  <div id="block3">
    <h3>Most Recent Flights</h3>
    <?php
      if(isset($flightslist)) {
        print "<table>\n<tr><th>Date</th><th>Pilot</th><th>Route</th><th colspan=\"2\">Hours</th></tr>";
        for($i=0; $i<count($flightslist); $i++) {
          $buf3 = logbook_detail($flightslist[$i]);
          $detaillink = "detail_logbook.php?id=$flightslist[$i]";
          ?>
          <tr class="<?php print $class; ?>" onMouseOver=this.style.backgroundColor="#ffffff"
                                     onMouseOut=this.style.backgroundColor=""
                                     onclick="window.location.href='<?php print $detaillink; ?>'" >
           <td><?php print $buf3['date']; ?></td>
           <td><?php print pilot_name($buf3['pilot_id']); ?></td>
           <td><?php print $buf3['route']; ?></td>
           <?php print split_decimal(logbook_hours($flightslist[$i])); ?>
          </tr>
          <?php
        }
        print "</table>\n";
      }
    ?>
  </div>
  </div>

<?php

  include "include/foot.inc";

?>
