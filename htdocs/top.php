<?php
  $cvs="\$Id$";

  include "include/init.inc";

  header("Content-Type: text/xml");

  $n = 10;
  if (isset($rvar_n)) {
    $n = $rvar_n;
  }

?>
<?xml version="1.0"?>
<lol>
  <pilots>
    <?php
      $pilotlist = pilot_search("");
      usort($pilotlist,"pilot_cmp");
      $pilotlist = array_slice($pilotlist,0,10);
      for ($i = 0; $i < count($pilotlist); $i++) {
        print "<pilot name=\"".pilot_name($pilotlist[$i])."\" hours=\"".pilot_hours($pilotlist[$i], 0)."\" />\n";
      }
    ?>
  </pilots>
  <airports>
    <?php
      $airportlist = airport_search("", $n);
      for ($i = 0; $i < count($airportlist); $i++) {
        $buf = airport_detail($airportlist[$i]);
        print "<airport ident=\"$airportlist[$i]\" visits=\"$buf[visits]\" />\n";
      }
    ?>
  </airports>
  <flights>
    <?php
      $flightslist = logbook_recent();
      for ($i = 0; $i < count($flightslist); $i++) {
        $buf = logbook_detail($flightslist[$i]);
        print "<flight pilot=\"".pilot_name($buf['pilot_id'])."\" date=\"$buf[date]\" route=\"$buf[route]\" hours=\"".logbook_hours($flightslist[$i])."\" />\n";
      }
    ?>
  </flights>
</lol>
