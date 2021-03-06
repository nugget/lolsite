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
    $pilotlist = pilot_search("", 10);
  }

  if(isset($rvar_asearch)) {
    $airportdesc = "Airports Matching [$rvar_asearch]";
    $airportlist = airport_search($rvar_asearch, 10 , 90);
  } else {
    $airportdesc = "Most Active Airports";
    $airportlist = airport_search("", 10, 90);
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
          $detaillink = $pilotlist[$i]['url']."pilot.php?pilot=".$pilotlist[$i]['username'];
          ?>
          <tr class="<?php print $class; ?>" onMouseOver=this.style.backgroundColor="#ffffff"
                                     onMouseOut=this.style.backgroundColor=""
                                     onclick="window.location.href='<?php print $detaillink; ?>'" >
           <td style="width: 100px;"><?php print $pilotlist[$i]['username']; ?></td>
           <td><?php print hobbs($pilotlist[$i]['hours'],0); ?></td>
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
        print "<table>\n<tr><th>Airport</th><th>Total</th><th>Recent</th></tr>";
        for($i=0; $i<count($airportlist); $i++) {
          $buf1 = $airportlist[$i];
          $buf2 = airport_detail($buf1['airport']);
          $detaillink = "airport.php?ident=" . $buf1['airport'];
          ?>
          <tr class="<?php print $class; ?>" onMouseOver=this.style.backgroundColor="#ffffff"
                                     onMouseOut=this.style.backgroundColor=""
                                     onclick="window.location.href='<?php print $detaillink; ?>'" >
           <td><?php print airport_name($buf1['airport']); ?></td>
           <td class="integer"><?php print $buf2['visits']; ?></td>
           <td class="integer"><?php print $buf1['visits']; ?></td>
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
          $buf3 = $flightslist[$i];
          $detaillink = $buf3['url']."detail_logbook.php?id=$buf3[logbook_id]";
          ?>
          <tr class="<?php print $class; ?>" onMouseOver=this.style.backgroundColor="#ffffff"
                                     onMouseOut=this.style.backgroundColor=""
                                     onclick="window.location.href='<?php print $detaillink; ?>'" >
           <td><?php print $buf3['date']; ?></td>
           <td><?php print $buf3['username']; ?></td>
           <td><?php print $buf3['route']; ?></td>
           <?php print split_decimal($buf3['hours']); ?>
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
