<?php
 $title = "Logbook Detail";
 include "init.inc";
 include "head.inc";

 if(!$rvar_id) {
   print "<h2>No entry selected</h2>";
   include "foot.inc";
   exit;
 }

 $rvar_id = (int) $rvar_id;

 if(is_mine()) {
   ?>
   <div id="buttonbar">
    <form action="edit_entry.php"><input type="hidden" value="<?php print $rvar_id; ?>" name="id"><input type="submit" value="Edit Entry"></form>
   </div>
   <?php
 }

?>

 <div id="logbook">
 <table>

<?php
 $sql = "SELECT * FROM logbook WHERE id = $rvar_id";
 $sqlresponse = mysql_query($sql);

 $num = '';
 while ($line = mysql_fetch_array($sqlresponse)) {

  $ident = mysql_fetch_row(mysql_query("SELECT makemodel FROM aircraft WHERE ident = '".$line['ident']."';"));
  $ident = $ident[0];

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
   <th colspan="8">Aircraft Category And Class</th>
   <th colspan="2" rowspan="2">Flight Training Device</th>
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
  </tr>

  <tr>
   <td nowrap="nowrap"><?php echo $line['date']; ?></td>
   <td><?php echo $ident; ?></td>
   <td><?php echo $line['ident']; ?></td>
 
   <?php
    $hops = preg_split("/ +/",$line['route'],-1,PREG_SPLIT_NO_EMPTY);

    $asel = class_time("ASEL",$rvar_id);
    $amel = class_time("AMEL",$rvar_id);
    $ases = class_time("ASES",$rvar_id);
    $ames = class_time("AMES",$rvar_id);
    $sim  = (int) class_time("Sim",$rvar_id);

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
   ?>
   <td class="integer"><?php echo $line['landings_day']; ?></td>
   <td class="integer"><?php echo $line['landings_night']; ?></td>
   <td class="integer"><?php echo $line['instrument_approach']; ?></td>
   <?php split_decimal($asel); ?>
   <?php split_decimal($amel); ?>
   <?php split_decimal($ases); ?>
   <?php split_decimal($ames); ?>
   <?php split_decimal($sim); ?>
   <td width="100%"><?php echo $line['passengers']; ?></td>
  </tr>
  </table><table>
  <tr>
   <th colspan="6">Conditions of Flight</th>
   <th colspan="8">Type of Piloting Time</th>
   <th rowspan="2">Remarks, Procedures, Maneuvers</th>
   <th rowspan="2" colspan="2">Cost</th>
  </tr>

  <tr>
   <th colspan="2">Night</th>
   <th colspan="2">Actual Instr</th>
   <th colspan="2">Simul Instr</th>
   <th colspan="2">Dual</th>
   <th colspan="2">Solo/PIC</th>
   <th colspan="2">X/C</th>
   <th colspan="2">CFI</th>
  </tr>
  
  <tr>
   <?php split_decimal($line['conditions_night']); ?>
   <?php split_decimal($line['conditions_actualinstr']); ?>
   <?php split_decimal($line['conditions_simulinstr']); ?>
   <?php split_decimal($line['type_dual']); ?>
   <?php split_decimal($line['type_pic']); ?>
   <?php split_decimal($line['type_xc']); ?>
   <?php split_decimal($line['type_cfi']); ?>
   <td width="100%"><?php echo $line['remarks']; ?></td>
   <td><?php print "$"; printf("%.2f",$line['cost']); ?></td>
  </tr>
<?php
   if($line[detail]) {
     print "<tr><th colspan=\"16\">Details</th></tr><tr><td colspan=\"16\">$line[detail]</td></tr>\n";
   }
 };

?>
 </table>
 </div>

<?
 include "foot.inc";
?>
