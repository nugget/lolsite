<?php
 $title = "passenger: Edit";
 $cvs="\$Id$";

 include "include/init.inc";

 if(!isset($rvar_id)) {
   $error_title = "No ID Specified";
   $error_text = "I can't edit a passenger if you don't tell me which one!";
 } else {
   if($rvar_id == 0) {
     if(!isset($rvar_pilot)) {
       $error_title = "No pilot specified";
       $error_text = "You must specify a pilot in order to add a new passenger entry.";
     }
     $title = "passenger: Add";
     $alias = $rvar_alias;
   } else {
     $key = pax_key($rvar_id);
     if(isset($key['pilot_id'])) {
       $rvar_pilot = $key['pilot_id'];
       $alias = $key['alias'];
     }
   }
   $line = pax_detail($alias,$rvar_pilot,"");
 }
 if(!is_mine()) {
   $error_title = "Up To No Good";
   $error_text = "I can't edit an entry if you don't own it!";
   unset($rvar_fullname);
 }

 if(isset($rvar_fullname)) {
   # I see data, we need to insert/update as required.
   if($rvar_id == 0) {
     # new logbook entry
     $sql = "INSERT INTO passengers VALUES " .
        "(NULL,'$alias', $rvar_pilot, '$rvar_fullname', " .
        "'$rvar_image_url','$rvar_link_url','$rvar_detail')";
   } else {
     # editing an old entry
     $sql = "UPDATE passengers SET " .
        "fullname='$rvar_fullname', " .
        "image_url='$rvar_image_url', link_url='$rvar_link_url', detail='$rvar_detail' where id = $rvar_id";
   }

   $sql_response = lol_query($sql);

   $target = "detail_pax.php?alias=$alias&pilot=$rvar_pilot";
   header("Location: $target");
   exit;
 }

 include "include/head.inc";

?>

 <form method="get" action="edit_pax.php">
 <div id="logbook">
 <table>

<?php

 if($rvar_id != 0) {
   $sql = "SELECT * FROM passengers WHERE id = $rvar_id";
   $sqlresponse = lol_query($sql);
   $line = lol_fetch_array($sqlresponse);
 }

?>

  <tr>
   <th>Alias</th>
   <th>Name</th>
  </tr>

  <tr>
   <td><strong><?php print $line['alias']; ?></strong></td>
   <td width="100%"><input type="text" name="fullname" size="40" value="<?php print $line['fullname']; ?>"></td>
  </tr>

  <tr>
   <th colspan="2">Image URL:</th>
  </tr><tr>
   <td colspan="2"><input type="text" name="image_url" size="80" value="<?php echo $line['image_url']; ?>"></td>
  </tr>
  <tr>
   <th colspan="2">Image Link:</th>
  </tr><tr>
   <td colspan="2"><input type="text" name="link_url" size="80" value="<?php echo $line['link_url']; ?>"></td>
  </tr>
  <tr>
   <th colspan="2">Detail:</th>
  </tr>
  <tr>
   <td colspan="2"><textarea name="detail" cols="60" rows="8"><?php print $line['detail']; ?></textarea></td>
  </tr>
  <tr>
   <td colspan="2" align="center"><input type="submit" value="Save Changes" /></td>
  </tr>
 </table>
 </div>
 <input type="hidden" name="id" value="<?php print $rvar_id; ?>" />
 <input type="hidden" name="alias" value="<?php print $alias; ?>" />
 <input type="hidden" name="pilot" value="<?php print $rvar_pilot; ?>" />
 </form>

<?
 include "include/foot.inc";
?>
