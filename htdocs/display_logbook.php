<?php
 $title = "logbook";

 include "include/init.inc";

 if(!isset($rvar_pilot)) {
   $error_title = "No pilot specified";
   $error_text = "You must specify a pilot in order to view a logbook!";
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
   $whereclause = "(logbook.route like '%$rvar_route%')";
 }
 if(isset($rvar_pax)) {
   if($whereclause <> '') {
     $whereclause = $whereclause . " and ";
   }
   $whereclause = $whereclause . "(logbook.passengers like '%$rvar_pax%')";
 }

 $launch = logbook_launches($whereclause);

 $sql = "SELECT * FROM logbook";
 if($whereclause <> '') {
   $sql = $sql . " WHERE $whereclause";
 }
 $sql = $sql . " ORDER BY date, id";
 $sqlresponse = pg_query($sql);

?>

 <div id="logbook">
  <table>
   <tr>
    <th rowspan="2">Flight</th>
    <th rowspan="2">Date</th>
    <th rowspan="2">Aircraft</th>
    <?php if ($launch) echo '<th colspan="2">Launch</th>'; ?>
    <?php if ($launch) echo '<th colspan="2">Altitude</th>'; ?>
    <th colspan="3">Route of Flight</th>
    <th rowspan="2" colspan="2">Duration of Flight</th>
    <th colspan="2">Landings</th>
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
 $flightnum = 1;
 while ($line = pg_fetch_array($sqlresponse)) {

  $ident = pg_fetch_row(pg_query("SELECT makemodel FROM aircraft WHERE ident = '".$line['ident']."';"));
  $ident = $ident[0];

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
   <td class="integer"><?php echo $flightnum++; ?></td>
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

   ?> 
   <td class="integer"><?php echo $line['landings_day']; ?></td>
   <td class="integer"><?php echo $line['landings_night']; ?></td>
   <td class="integer"><?php echo count_elements($line['passengers']); ?></td>
   <td><?php echo $line['remarks']; ?></td
  </tr>
<?php
 };

?>
   <tr class="totals">
    <td colspan="<?php echo $launch ? 10 : 6; ?>">&nbsp;</td>
    <?php split_decimal($total_duration); ?>
    <td class="integer"><?php print $total_landings_day; ?></td>
    <td class="integer"><?php print $total_landings_night; ?></td>
    <td colspan="3">&nbsp;</td>
   </tr>


   </tr>

  </table>
 </div>


<?
 include "include/foot.inc";
?>
