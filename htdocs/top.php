<?php
  $cvs="\$Id$";

  include "include/init.inc";

  header("Content-Type: text/xml");

  $n = 10;
  if (isset($rvar_n)) {
    $n = $rvar_n;
  }

  print "<?xml version=\"1.0\"?>\n";
?>
<lol>
  <pilots>
    <?php
      $pilotlist = pilot_search("", 0);
      for ($i = 0; $i < count($pilotlist); $i++) {
        if ($pilotlist[$i]['publish_global'] == 't' && !$pilotlist[$i]['peer_tag']) {
          print "<pilot name=\"".$pilotlist[$i]['username']."\" hours=\"".$pilotlist[$i]['hours']."\" />\n";
        }
      }
    ?>
  </pilots>
  <airports>
    <?php
      $airportlist = airport_search("", $n, 90);
      for ($i = 0; $i < count($airportlist); $i++) {
        $buf1 = $airportlist[$i];
        $buf2 = airport_detail($buf1['airport']);
        print "<airport ident=\"" . $buf1['airport'] . "\" recent=\"" . $buf1['visits'] . "\" visits=\"" . $buf2['visits'] . "\" fullname=\"" . $buf2['fullname'] . "\" />\n";
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
