<?php
 $title = "airplane: Edit";
 $cvs="\$Id$";
 $keywords="logbook online";

 include "include/init.inc";

 if(!isset($rvar_id)) {
   $error_title = "No ID Specified";
   $error_text = "I can't edit an airplane if you don't tell me which one!";
 } else {
   if($rvar_id == 0) {
     if(!isset($rvar_pilot)) {
       $error_title = "No pilot specified";
       $error_text = "You must specify a pilot in order to add a new airplane entry.";
     }
     $title = "airplane: Add";
     $ident = $rvar_ident;
   } else {
     $key = aircraft_key($rvar_id);
     if(isset($key['pilot_id'])) {
       $rvar_pilot = $key['pilot_id'];
       $ident = $key['ident'];
     }
   }
   $line = aircraft_detail($ident,$rvar_pilot);
 }
 if(!is_mine()) {
   $error_title = "Up To No Good";
   $error_text = "I can't edit an entry if you don't own it!";
   unset($rvar_aircraft_class);
 }

 if(isset($rvar_aircraft_class)) {
   # I see data, we need to insert/update as required.
   $rvar_aircraft_class = (int) $rvar_aircraft_class;
   if(!isset($rvar_complex)) { $rvar_complex = 0; } else { $rvar_complex = 1; }
   if(!isset($rvar_high_perf)) { $rvar_high_perf = 0; } else { $rvar_high_perf = 1; }
   if(!isset($rvar_tailwheel)) { $rvar_tailwheel = 0; } else { $rvar_tailwheel = 1; }
   $rvar_ident = strtoupper($rvar_ident);
   $rvar_home_field = strtoupper($rvar_home_field);
   if($rvar_id == 0) {
     # new logbiook entry
     $sql = "INSERT INTO aircraft VALUES " .
        "(NULL,'$rvar_ident', $rvar_pilot, '$rvar_makemodel', $rvar_aircraft_class, " .
        "$rvar_complex,$rvar_high_perf,$rvar_tailwheel, '$rvar_home_field', " .
        "'$rvar_image_url','$rvar_link_url','$rvar_detail')";
   } else {
     # editing an old entry
     $sql = "UPDATE aircraft SET " .
        "ident='$rvar_ident', makemodel='$rvar_makemodel', aircraft_class=$rvar_aircraft_class, " .
        "complex=$rvar_complex, high_perf=$rvar_high_perf, tailwheel=$rvar_tailwheel, home_field='$rvar_home_field', " .
        "image_url='$rvar_image_url', link_url='$rvar_link_url', detail='$rvar_detail' where id = $rvar_id";
   }

   $sql_response = lol_query($sql);

   $target = "detail_aircraft.php?ident=$rvar_ident&pilot=$rvar_pilot";
   header("Location: $target");
   exit;
 }

 if(!isset($rvar_id)) {
   $error_title = "Huh?";
   $error_text = "Hwaaaaaaaaa!";
 }

 include "include/head.inc";

 $classlist = "<select name=\"aircraft_class\">";
 for($i=1;$i<count($GLOBALS['classcode']);$i++) {
   $classlist = $classlist . "<option value=\"$i\">" . $GLOBALS['classname'][$i] . " (" . $GLOBALS['classcode'][$i] . ")</option>";
 }
 $classlist = $classlist . "</select>";

?>

 <form method="get" action="edit_aircraft.php">
 <div id="logbook">
 <table width="100%">

  <tr>
   <th>Ident</th>
   <th>Equipment</th>
   <th>Aircraft Class</th>
  </tr>

  <tr>
   <td><input type="text" name="ident" size="11" value="<?php print $line['ident']; ?>" /></td>
   <td><input type="text" name="makemodel" size="8" value="<?php print $line['makemodel']; ?>" /></td>
   <td><?php print $classlist; ?></td>
  </tr>

  <tr>
   <th>Home Airport</th>
   <th colspan="2">Categories</th>
  </tr>

  <tr>
   <td><input type="text" name="home_field" size="11" value="<?php print $line['home_field']; ?>" /></td>
   <td colspan="2">
    Complex: <input name="complex" type="checkbox" value="1" <?php if($line['complex']>0) { print "checked=\"checked\""; } ?> />
    High Performance: <input name="high_perf" type="checkbox" value="1" <?php if($line['high_perf']>0) { print "checked=\"checked\""; } ?> />
    Tailwheel: <input name="tailwheel" type="checkbox" value="1" <?php if($line['tailwheel']>0) { print "checked=\"checked\""; } ?> />
   </td>
  </tr>

  <tr>
   <th colspan="3">Image URL:</th>
  </tr><tr>
   <td colspan="3"><input type="text" name="image_url" size="80" value="<?php echo $line['image_url']; ?>" /></td>
  </tr>
  <tr>
   <th colspan="3">Image Link:</th>
  </tr><tr>
   <td colspan="3"><input type="text" name="link_url" size="80" value="<?php echo $line['link_url']; ?>" /></td>
  </tr>
  <tr>
   <th colspan="3">Detail:</th>
  </tr>
  <tr>
   <td colspan="3"><textarea name="detail" cols="60" rows="8"><?php print $line['detail']; ?></textarea></td>
  </tr>
  <tr>
   <td colspan="3" align="center"><input type="submit" value="Save Changes" /></td>
  </tr>
 </table>
 </div>
 <input type="hidden" name="id" value="<?php print $rvar_id; ?>" />
 <input type="hidden" name="pilot" value="<?php print $rvar_pilot; ?>" />
 </form>

<?
 include "include/foot.inc";
?>
