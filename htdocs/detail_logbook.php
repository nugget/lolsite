<?php
 $title = "Logbook Detail";
 include "include/init.inc";

 if(isset($rvar_id)) {
   $rvar_id = (int) $rvar_id;
   $rvar_pilot = logbook_pilot($rvar_id);
 } else {
   $notice_title = "No Entry Selected";
   $notice_text = "You must supply a logbook id.";
 }

 include "include/head.inc";

 if(!isset($rvar_id)) {
   include "include/foot.inc";
   exit;
 }

 if(is_mine()) {
   ?>
   <div id="buttonbar">
    <form action="edit_logbook.php">
     <input type="hidden" value="<?php print $rvar_id; ?>" name="id">
     <input name="edit" type="submit" value="Edit Entry">
     <input name="delete" type="submit" value="Delete Entry">
    </form>
   </div>
   <?php
 }

?>

 <div id="logbook">
 <table>

<?php
 $sql = "SELECT * FROM logbook WHERE id = $rvar_id";
 $sqlresponse = lol_query($sql);

 $num = '';
 while ($line = lol_fetch_array($sqlresponse)) {

    $equipment = aircraft_equipment($line['ident']);

   if($num==1) {
              $class="row1";
                $num = 0;
        } else {
                $class="row2";
                $num++; 
        }

?>

  <tr>
   <th rowspan="2">Date</th>
   <th colspan="2">Aircraft</th>
   <th colspan="3">Route of Flight</th>
   <th colspan="2">Landings</th>
   <th rowspan="2">Inst<br />Appr</th>
   <th colspan="10">Aircraft Category And Class</th>
   <th colspan="2" rowspan="2">Flight Training Device</th>
   <?php if ($line['launch_type']) echo '<th colspan="2">Launch</th>'; ?>
   <?php if ($line['launch_type']) echo '<th colspan="2">Altitude</th>'; ?>
   <th rowspan="2">Passengers</th>
  </tr>

  <tr>
   <th>Equipment</th>
   <th>Ident</th>
   <th>From</th>
   <th>Enroute</th>
   <th>To</th> 
   <th>Day</th>
   <th>Night</th>
   <th colspan="2">ASEL</th>
   <th colspan="2">AMEL</th>
   <th colspan="2">ASES</th>
   <th colspan="2">AMES</th>
   <th colspan="2">Glider</th>
   <?php if ($line['launch_type']) echo '<th>A</th><th>G</th>'; ?>
   <?php if ($line['launch_type']) echo '<th>Release</th><th>Maximum</th>'; ?>
  </tr>

  <tr>
   <td nowrap="nowrap"><?php echo $line['date']; ?></td>
   <td><?php echo $equipment; ?></td>
   <td onMouseOver=this.style.backgroundColor="#ffffff" onMouseOut=this.style.backgroundColor="" onclick="window.location.href='<?php print "detail_aircraft.php?ident=$line[ident]&pilot=$rvar_pilot" ?>'"><?php echo $line['ident']; ?></td>
 
   <?php
    $hops = preg_split("/ +/",$line['route'],-1,PREG_SPLIT_NO_EMPTY);

    $asel = class_time("ASEL",$rvar_id);
    $amel = class_time("AMEL",$rvar_id);
    $ases = class_time("ASES",$rvar_id);
    $ames = class_time("AMES",$rvar_id);
    $glider = class_time("Glider", $rvar_id);
    $sim  = class_time("Sim",$rvar_id);

    print "<td onMouseOver=this.style.backgroundColor=\"#ffffff\" onMouseOut=this.style.backgroundColor=\"\" onclick=\"window.location.href='airport.php?ident=$hops[0]&pilot=$rvar_pilot'\">$hops[0]</td>";
    $els = sizeof($hops) -1;
    if($els==0) {
      print "<td>&nbsp;</td><td>&nbsp</td>\n";
    } elseif($els==1) {
      print "<td>&nbsp;</td><td onMouseOver=this.style.backgroundColor=\"#ffffff\" onMouseOut=this.style.backgroundColor=\"\" onclick=\"window.location.href='airport.php?ident=$hops[1]&pilot=$rvar_pilot'\">$hops[1]</td>\n";
    } else {
      print "<td>";
      for($i=1;$i<$els;$i++) {
        print "<span onMouseOver=this.style.backgroundColor=\"#ffffff\" onMouseOut=this.style.backgroundColor=\"\" onclick=\"window.location.href='airport.php?ident=$hops[$i]&pilot=$rvar_pilot'\">$hops[$i]</span>&nbsp;";
      }
      print "</td>";
      print "<td onMouseOver=this.style.backgroundColor=\"#ffffff\" onMouseOut=this.style.backgroundColor=\"\" onclick=\"window.location.href='airport.php?ident=$hops[$els]&pilot=$rvar_pilot'\">$hops[$els]</td>";
    }
   ?>
   <td class="integer"><?php echo $line['landings_day']; ?></td>
   <td class="integer"><?php echo $line['landings_night']; ?></td>
   <td class="integer"><?php echo $line['instrument_approach']; ?></td>
   <?php split_decimal($asel); ?>
   <?php split_decimal($amel); ?>
   <?php split_decimal($ases); ?>
   <?php split_decimal($ames); ?>
   <?php split_decimal($glider); ?>
   <?php split_decimal($sim); ?>
   <?php
    if ($line['launch_type']) {
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
   ?>
   <td width="100%">
   <?php
        $pax = preg_split("/ +/", $line['passengers'], -1, PREG_SPLIT_NO_EMPTY);
        for ($i = 0; $i < sizeof($pax); $i++) {
            print "<span onMouseOver=this.style.backgroundColor=\"#ffffff\" onMouseOut=this.style.backgroundColor=\"\" onclick=\"window.location.href='detail_pax.php?alias=$pax[$i]&pilot=$rvar_pilot'\">$pax[$i]</span> ";
        }
   ?></td>
  </tr>
  </table><table>
  <tr>
   <th colspan="6">Conditions of Flight</th>
   <th colspan="10">Type of Piloting Time</th>
   <th rowspan="2">Remarks, Procedures, Maneuvers</th>
   <th rowspan="2" colspan="2">Cost</th>
  </tr>

  <tr>
   <th colspan="2">Night</th>
   <th colspan="2">Actual Instr</th>
   <th colspan="2">Simul Instr</th>
   <th colspan="2">Dual</th>
   <th colspan="2">Solo/PIC</th>
   <th colspan="2">SIC</th>
   <th colspan="2">X/C</th>
   <th colspan="2">CFI</th>
  </tr>
  
  <tr>
   <?php split_decimal($line['conditions_night']); ?>
   <?php split_decimal($line['conditions_actualinstr']); ?>
   <?php split_decimal($line['conditions_simulinstr']); ?>
   <?php split_decimal($line['type_dual']); ?>
   <?php split_decimal($line['type_pic']); ?>
   <?php split_decimal($line['type_sic']); ?>
   <?php split_decimal($line['type_xc']); ?>
   <?php split_decimal($line['type_cfi']); ?>
   <td width="100%"><?php echo $line['remarks']; ?></td>
   <td><?php print "$"; printf("%.2f",$line['cost']); ?></td>
  </tr>
<?php
   if($line['detail']) {
     $line['detail'] = preg_replace("/\n/","<br />",$line['detail']);
     print "<tr><th colspan=\"18\">Details</th></tr><tr><td colspan=\"18\">$line[detail]</td></tr>\n";
   }
 };

?>
 </table>
 </div>

<?
 include "include/foot.inc";
?>
