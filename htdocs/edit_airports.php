<?php
 $title = "Edit Airport";

 include "init.inc";

 import_request_variables("gpc","rvar_");
 if(isset($rvar_scrape)) {
   header("Location: scrape_airports.php?ident=$rvar_ident");
   exit;
 }
 $rvar_id = (int) $rvar_id;
 if($rvar_id == 0) {
   $title = "Add New Airport";
 }
 if(!$rvar_pilot = is_user()) {
   $error_title = "Must be logged in";
   $error_text = "You must be logged in to submit an airport.";
   unset($rvar_pilot);
 }
 if(isset($rvar_ident)) {
   $rvar_ident=strtoupper($rvar_ident);
   if(!$rvar_link_url) {
     $rvar_link_url = "http://www.airnav.com/airport/" . $rvar_ident;
   }
 }
   
 if(isset($rvar_submit)) {
   # I see data, we need to insert/update as required.
   $rvar_tower = (int) $rvar_tower;
   if($rvar_id == 0) {
     # new logbook entry
     $sql = "INSERT INTO airports VALUES " .
        "(NULL,'$rvar_ident', $rvar_pilot, '$rvar_fullname', '$rvar_city', " .
        "'$rvar_timezone',$rvar_tower, " .
        "'$rvar_image_url','$rvar_link_url','$rvar_detail')";
   } else {
     # editing an old entry
     $sql = "UPDATE airports SET " .
        "ident='$rvar_ident', pilot_id=$rvar_pilot, fullname='$rvar_fullname', city='$rvar_city', " .
        "timezone='$rvar_timezone',tower=$rvar_tower, " .
        "image_url='$rvar_image_url', link_url='$rvar_link_url', detail='$rvar_detail' where id = $rvar_id";
   }
   $sql_response = mysql_query($sql);

   if($rvar_id > 0) {
      $target = "detail_airports.php?id=$rvar_id";
   } else {
      $target = "display_airports.php";
   }
   header("Location: target");

   include "foot.php";
   exit;
 }

 include "head.inc";

?>

 <form method="get" action="edit_airports.php">
 <div id="logbook">
 <table width="100%">

<?php

 $line = airport_detail($rvar_id);

?>

  <tr>
   <th>Ident</th>
   <th>Name</th>
   <th>City</th>
  </tr>

  <tr>
   <td><input type="text" name="ident" size="11" value="<?php print $line['ident']; ?>"></td>
   <td><input type="text" name="fullname" size="40" value="<?php print $line['fullname']; ?>"></td>
   <td><input type="text" name="city" size="40" value="<?php print $line['city']; ?>"></td>
  </tr>

  <tr>
   <th>Timezone</th>
   <th colspan="2">Attributes</th>
  </tr>

  <tr>
   <td><input type="text" name="timezone" size="11" value="<?php print $line['timezone']; ?>"></td>
   <td colspan="2">
    Tower Controlled: <input name="tower" type="checkbox" value="1" <?php if($line['tower']>0) { print "checked=\"checked\""; } ?>/>
   </td>
  </tr>

  <tr>
   <th colspan="3">Image URL:</th>
  </tr><tr>
   <td colspan="3"><input type="text" name="image_url" size="80" value="<?php echo $line['image_url']; ?>"></td>
  </tr>
  <tr>
   <th colspan="3">Image Link:</th>
  </tr><tr>
   <td colspan="3"><input type="text" name="link_url" size="80" value="<?php echo $line['link_url']; ?>"></td>
  </tr>
  <tr>
   <th colspan="3">Detail:</th>
  </tr>
  <tr>
   <td colspan="3"><textarea name="detail" cols="60" rows="8"><?php print $line['detail']; ?></textarea></td>
  </tr>
  <tr>
   <td colspan="3" align="center">
    <input name="submit" type="submit" value="Save Changes" />
    <?php if($rvar_id == 0) { ?><input name="scrape" type="submit" value="Scrape Data from AirNav.com"><?php } ?>
   </td>
  </tr>
 </table>
 </div>
 <input type="hidden" name="id" value="<?php print $rvar_id; ?>" />
 </form>

<?
 include "foot.php";
?>
