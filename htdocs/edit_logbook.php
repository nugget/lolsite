<?php
 $title = "logbook: Edit";

 include "init.inc";

 if(!isset($rvar_id)) {
   $error_title = "No ID Specified";
   $error_text = "I can't edit an entry if you don't tell me which one!";
 } else {
   if($rvar_id == 0) {
     if(!isset($rvar_pilot)) {
       $error_title = "No pilot specified";
       $error_text = "You must specify a pilot in order to add a new logbook entry.";
     }
     $title = "logbook: Add";
   } else {
     $rvar_pilot = logbook_pilot($rvar_id);
   }
 }
 if(!is_mine()) {
   $error_title = "Up To No Good";
   $error_text = "I can't edit an entry if you don't own it!";
   unset($rvar_route);
 }
 if(isset($rvar_route)) {
   # I see data, we need to insert/update as required.
   $rvar_landings_day = sprintf("%0d",$rvar_landings_day);
   $rvar_landings_night = sprintf("%0d",$rvar_landings_night);
   $rvar_instrument_approach = sprintf("%0d",$rvar_instrument_approach);
   $rvar_conditions_night = sprintf("%02.1f",$rvar_conditions_night);
   $rvar_conditions_actualinstr = sprintf("%02.1f",$rvar_conditions_actualinstr);
   $rvar_conditions_simulinstr = sprintf("%02.1f",$rvar_conditions_simulinstr);
   $rvar_type_xc = sprintf("%02.1f",$rvar_type_xc);
   $rvar_type_cfi = sprintf("%02.1f",$rvar_type_cfi);
   $rvar_type_dual = sprintf("%02.1f",$rvar_type_dual);
   $rvar_type_pic = sprintf("%02.1f",$rvar_type_pic);
   $rvar_type_sic = sprintf("%02.1f",$rvar_type_sic);
   $rvar_cost = sprintf("%04.2f",$rvar_cost);
   if($rvar_id == 0) {
     # new logbiook entry
     $sql = "INSERT INTO logbook VALUES " .
        "(NULL,$rvar_pilot,'$rvar_date','$rvar_ident','$rvar_route','$rvar_passengers'," .
        "'$rvar_remarks',$rvar_landings_day,$rvar_landings_night,$rvar_instrument_approach," .
        "$rvar_conditions_night," .
        "$rvar_conditions_actualinstr,$rvar_conditions_simulinstr," .
        "$rvar_type_xc,$rvar_type_cfi,$rvar_type_dual,$rvar_type_pic,$rvar_type_sic," .
        "'$rvar_detail','$rvar_url',$rvar_cost)";
   } else {
     # editing an old entry
     $sql = "UPDATE logbook SET " .
        "date='$rvar_date', ident='$rvar_ident', route='$rvar_route', passengers='$rvar_passengers', " .
        "remarks='$rvar_remarks', landings_day=$rvar_landings_day, landings_night=$rvar_landings_night, " .
        "instrument_approach=$rvar_instrument_approach, " .
        "conditions_night=$rvar_conditions_night, " .
        "conditions_actualinstr=$rvar_conditions_actualinstr, conditions_simulinstr=$rvar_conditions_simulinstr, " .
        "type_xc=$rvar_type_xc, type_cfi=$rvar_type_cfi, type_dual=$rvar_type_dual, type_pic=$rvar_type_pic, " .
        "type_sic=$rvar_type_sic, detail='$rvar_detail', url='$rvar_url', cost=$rvar_cost WHERE id = $rvar_id";
   }

   $sql_response = mysql_query($sql);

   if($rvar_id > 0) {
     $target = "detail_logbook.php?id=$rvar_id";
   } else {
     $target = "display_logbook.php?pilot=$rvar_pilot";
   }
   header("Location: $target");
   exit;
 }

 include "head.inc";

?>

 <form method="get" action="edit_logbook.php">
 <div id="logbook">
 <table width="100%">

<?php

 $line = logbook_detail($rvar_id);

?>

  <tr>
   <th rowspan="2">Date</th>
   <th rowspan="2">Aircraft</th>
   <th rowspan="2">Route of Flight</th>
   <th colspan="2">Landings</th>
   <th rowspan="2">Inst<br />Appr</th>
  </tr>

  <tr>
   <th>Day</th>
   <th>Night</th>
  </tr>

  <tr>
   <td nowrap="nowrap"><input type="text" name="date" size="11" value="<?php if ($line['date']) echo $line['date']; else echo date("Y-m-d"); ; ?>"></td>
   <td><input type="text" name="ident" size="8" value="<?php print $line['ident']; ?>"></td>
   <td><input type="text" name="route" size="20" value="<?php print $line['route']; ?>"></td>

   <td class="integer"><input type="text" name="landings_day" size="3" value="<?php echo $line['landings_day']; ?>"></td>
   <td class="integer"><input type="text" name="landings_night" size="3" value="<?php echo $line['landings_night']; ?>"></td>
   <td class="integer"><input type="text" name="instrument_approach" size="3" value="<?php echo $line['instrument_approach']; ?>"></td>

  </tr>
 </table><table width="100%">
  <tr>
   <th colspan="3">Conditions of Flight</th>
   <th colspan="5">Type of Piloting Time</th>
   <th rowspan="2">URL</th>
   <th rowspan="2">Cost</th>
  </tr>

  <tr>
   <th>Night</th>
   <th>Actual Instr</th>
   <th>Simul Instr</th>
   <th>Dual</th>
   <th>Solo/PIC</th>
   <th>SIC</th>
   <th>X/C</th>
   <th>CFI</th>
  </tr>
  
  <tr>
   <td class="integer"><input type="text" name="conditions_night" size="4" value="<?php echo $line['conditions_night']; ?>"></td>
   <td class="integer"><input type="text" name="conditions_actualinstr" size="4" value="<?php echo $line['conditions_actualinstr']; ?>"></td>
   <td class="integer"><input type="text" name="conditions_simulinstr" size="4" value="<?php echo $line['conditions_simulinstr']; ?>"></td>
   <td class="integer"><input type="text" name="type_dual" size="4" value="<?php echo $line['type_dual']; ?>"></td>
   <td class="integer"><input type="text" name="type_pic" size="4" value="<?php echo $line['type_pic']; ?>"></td>
   <td class="integer"><input type="text" name="type_sic" size="4" value="<?php echo $line['type_sic']; ?>"></td>
   <td class="integer"><input type="text" name="type_xc" size="4" value="<?php echo $line['type_xc']; ?>"></td>
   <td class="integer"><input type="text" name="type_cfi" size="4" value="<?php echo $line['type_cfi']; ?>"></td>
   <td><input type="text" name="url" size="30" value="<?php echo $line['url']; ?>"></td>
   <td><input type="text" name="cost" size="7" value="<?php echo $line['cost']; ?>"></td>
  </tr>
 </table><table width="100%">
  <tr>
   <th>Remarks:</th>
   <td><input type="text" name="remarks" size="60" value="<?php echo $line['remarks']; ?>"></td>
  </tr>
  <tr>
   <th>Passengers:</th>
   <td><input type="text" name="passengers" size="60" value="<?php echo $line['passengers']; ?>"></td>
  </tr>
  <tr>
   <th>Detail:</th>
   <td><textarea name="detail" cols="60" rows="8"><?php echo $line['detail']; ?></textarea></td>
  </tr>
  <tr>
   <td colspan="2" align="center"><input type="submit" value="Save Changes" /></td>
  </tr>
 </table>
 </div>
 <input type="hidden" name="id" value="<?php print $rvar_id; ?>" />
 <input type="hidden" name="pilot" value="<?php print $rvar_pilot; ?>" />
 </form>

<?
 include "foot.inc";
?>
