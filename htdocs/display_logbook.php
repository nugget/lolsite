<?php
 $title = "logbook";

 include "init.inc";

 if(!isset($rvar_pilot)) {
   $error_title = "No pilot specified";
   $error_text = "You must specify a pilot in order to view a logbook!";
 }

 include "head.inc";

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
?>

 <div id="logbook">
  <table>
   <tr>
    <th rowspan="2">Date</th>
    <th rowspan="2">Aircraft</th>
    <th colspan="3">Route of Flight</th>
    <th rowspan="2" colspan="2">Duration of Flight</th>
    <th colspan="2">Landings</th>
    <th rowspan="2"># of<br />Pax</th>
    <th rowspan="2" colspan="2" width="100%">Remarks, Procedures, Maneuvers</th>
   </tr>

   <tr>
    <th>From</th>
    <th>Enroute</th>
    <th>To</th> 
    <th>D</th>
    <th>N</th>
   </tr>


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

 $sql = "SELECT * FROM logbook";
 if($whereclause <> '') {
   $sql = $sql . " WHERE $whereclause";
 }
 $sql = $sql . " ORDER BY 'date'";
 $sqlresponse = mysql_query($sql);

 $class = "";

 while ($line = mysql_fetch_array($sqlresponse)) {

  $ident = mysql_fetch_row(mysql_query("SELECT makemodel FROM aircraft WHERE ident = '".$line['ident']."';"));
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
   <td nowrap="nowrap"><?php echo $line['date']; ?></td>
   <td><?php echo $line['ident']; ?></td>
 
   <?php

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

   ?> 
   <td class="integer"><?php echo $line['landings_day']; ?></td>
   <td class="integer"><?php echo $line['landings_night']; ?></td>
   <td class="integer"><?php echo count_elements($line['passengers']); ?></td>
   <td><?php echo $line['remarks']; ?></td
  </tr>
<?php
 };

?>

  </table>
 </div>


<?
 include "foot.inc";
?>
