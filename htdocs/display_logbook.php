<?php
 $title = "logbook";

 include "include/init.inc";

 # run the redirect, after which we can safely assume that $rvar_pilot is a username.
 pilot_id_redirect($rvar_pilot);
 $rvar_pilot = pilot_lookup($rvar_pilot);

 # if(!isset($rvar_pilot)) {
 #   $error_title = "No pilot specified";
 #   $error_text = "You must specify a pilot in order to view a logbook!";
 # }

 if(!isset($rvar_limit)) {
   $rvar_limit = 16;
 }
 if(!isset($rvar_page)) {
   $rvar_page = 0;
 }

 include "include/head.inc";

 if(is_mine()) {
   ?>
   <div id="buttonbar">
    <form action="edit_logbook.php">
     <input type="hidden" value="0" name="id">
     <input type="hidden" value="<?php print $rvar_pilot; ?>" name="pilot">
     <input type="submit" value="Add Entry">
    </form>
   </div>
   <?php
 }

 $total_duration = 0;
 $total_landings_day = 0;
 $total_landings_night = 0;
 $total_instrument_approach = 0;

?>


<?php
 $whereclause = "pilot_id = $rvar_pilot";

 if(isset($rvar_ident)) {
   if($whereclause <> '') {
     $whereclause = $whereclause . " and ";
   }
   $whereclause = "(logbook.ident = '$rvar_ident')";
 }
 if(isset($rvar_route)) {
   if($whereclause <> '') {
     $whereclause = $whereclause . " and ";
   }
   $whereclause = "id in (select logbook_id from flight_route where airport = '$rvar_route')";
 }
 if(isset($rvar_pax)) {
   if($whereclause <> '') {
     $whereclause = $whereclause . " and ";
   }
   $whereclause = $whereclause . "(logbook.passengers like '%$rvar_pax%')";
 }

 $launch = logbook_launches($whereclause);
 $entries = logbook_entries($whereclause);

 $pages = (int) ((count($entries) + ($rvar_limit-1)) / $rvar_limit);
 if($pages == 0) {
   $pages = 1;
 }
 if($rvar_page == 0) {
   $rvar_page = $pages;
 }

?>

 <a href="logbook_rss.php?pilot=<?php echo pilot_name($rvar_pilot) ?>">
   <img src="images/xml.png" />
 </a>

 <div id="logbook">
  <table>
   <tr>
    <th colspan="20">
     <?php if($rvar_page > 1) { print "<a href=\"?pilot=" . pilot_name($rvar_pilot) . "&page=" . ($rvar_page - 1) . "\">Prev</a>"; } ?>
     Page <?php print $rvar_page; ?> of <?php print $pages; ?>
     <?php if($rvar_page < $pages) { print "<a href=\"?pilot=" . pilot_name($rvar_pilot) . "&page=" . ($rvar_page + 1) . "\">Next</a>"; } ?>
    </th>
   </tr>
   <tr>
    <th rowspan="2">Flight</th>
    <?php if(!isset($rvar_pilot)) { print "<th rowspan=\"2\">Pilot</th>"; } ?>
    <th rowspan="2">Date</th>
    <th rowspan="2">Aircraft</th>
    <?php if ($launch) echo '<th colspan="2">Launch</th>'; ?>
    <?php if ($launch) echo '<th colspan="2">Altitude</th>'; ?>
    <th colspan="3">Route of Flight</th>
    <th rowspan="2" colspan="2">Duration of Flight</th>
    <th colspan="2">Landings</th>
    <th rowspan="2">Inst<br />Apch</th>
    <th rowspan="2"># of<br />Pax</th>
    <th rowspan="2" colspan="2" width="100%">Remarks, Procedures, Maneuvers</th>
   </tr>

   <tr>
    <?php if ($launch) echo '<th>A</th><th>G</th>'; ?>
    <?php if ($launch) echo '<th>Release</th><th>Maximum</th>'; ?>
    <th>From</th>
    <th>Enroute</th>
    <th>To</th> 
    <th>D</th>
    <th>N</th>
   </tr>

<?php
 $class = "";
 $flightnum = 0;
 $pagenum = 0;
 $curpage = 1;
 for($ei=0; $ei<count($entries); $ei++) {
  $flightnum++;
  $pagenum++;
  if($pagenum > $rvar_limit) {
    $pagenum = 1;
    $curpage++;
  }
  if($curpage <> $rvar_page) {
    continue;
  }
  $line = logbook_detail($entries[$ei]);
  $ident = aircraft_equipment($line['ident']);

  if( $class != "odd" ) {
    $class = "odd";
  } else {
    $class = "even";
  }

  $detaillink="detail_logbook.php?id=" . $line['id'];
?>

  <tr class="<?php print $class; ?>" onMouseOver=this.style.backgroundColor="#ffffff"
                                     onMouseOut=this.style.backgroundColor=""
                                     onclick="window.location.href='<?php print $detaillink; ?>'" >
   <td class="integer"><?php echo $flightnum; ?></td>
   <?php if(!isset($rvar_pilot)) { print "<td>" . pilot_name($line['pilot_id']) . "</th>"; } ?>
   <td nowrap="nowrap"><?php echo $line['date']; ?></td>
   <td><?php echo $line['ident']; ?></td>
 
   <?php

    if ($launch) {
        if ($line['launch_type'] == 'A') {
            echo '<td align="center">&#x2022;</td>';
        } else {
            echo '<td>&nbsp;</td>';
        }
        if ($line['launch_type'] == 'G') {
            echo '<td align="center">&#x2022;</td>';
        } else {
            echo '<td>&nbsp;</td>';
        }
        if ($line['alt_release'] > 0) {
            echo "<td class=\"integer\">$line[alt_release]</td>";
        } else {
            echo '<td>&nbsp;</td>';
        }
        if ($line['alt_maximum'] > 0) {
            echo "<td class=\"integer\">$line[alt_maximum]</td>";
        } else {
            echo '<td>&nbsp;</td>';
        }
    }

    $hops = preg_split("/ +/",$line['route'],-1,PREG_SPLIT_NO_EMPTY);

    print "<td>$hops[0]</td>";
    $els = sizeof($hops) -1;
    if($els==0) {
      print "<td>&nbsp;</td><td>&nbsp</td>\n";
    } elseif($els==1) {
      print "<td>&nbsp;</td><td>$hops[1]</td>\n";
    } else {
      print "<td>";
      for($i=1;$i<$els;$i++) {
        print "$hops[$i]&nbsp;";
      }
      print "</td>";
      print "<td>$hops[$els]</td>";
    }

   $duration = logbook_hours($line['id']);
   split_decimal($duration);
   $total_duration = $total_duration + $duration;

   $total_landings_day += $line['landings_day'];
   $total_landings_night += $line['landings_night'];
   $total_instrument_approach += $line['instrument_approach'];

   ?> 
   <td class="integer"><?php echo $line['landings_day']; ?></td>
   <td class="integer"><?php echo $line['landings_night']; ?></td>
   <td class="integer"><?php echo $line['instrument_approach']; ?></td>
   <td class="integer"><?php echo count_elements($line['passengers']); ?></td>
   <td><?php echo $line['remarks']; ?>&nbsp;</td
  </tr>
<?php
 };

?>
   <tr class="totals">
    <td colspan="<?php echo $launch ? 10 : 6; ?>">Page <?php print $rvar_page; ?> of <?php print $pages; ?> totals:</td>
    <?php split_decimal($total_duration); ?>
    <td class="integer"><?php print $total_landings_day; ?></td>
    <td class="integer"><?php print $total_landings_night; ?></td>
    <td class="integer"><?php print $total_instrument_approach; ?></td>
    <td colspan="2">&nbsp;</td>
   </tr>


   </tr>

  </table>
 </div>


<?
 include "include/foot.inc";
?>
