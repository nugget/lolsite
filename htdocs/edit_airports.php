<?php
 $title = "airport: Edit";
 $cvs="\$Id$";

 include "include/init.inc";

 if(isset($rvar_scrape)) {
   header("Location: scrape_airports.php?ident=$rvar_ident");
   exit;
 }

 if(!isset($rvar_ident)) {
   $error_title = "No ident Specified";
   $error_text = "I can't continue if you don't tell me which airport!";
 } else {
   if($rvar_ident == '') {
     if(!isset($rvar_pilot)) {
       $error_title = "No pilot specified";
       $error_text = "You must specify a pilot in order to add a new airport entry.";
     }
     $title = "airport: Submit";
   }
   $ident = $rvar_ident;
   $line = airport_detail($ident);
 }

 if((isset($rvar_ident)) and (!is_mine()) and (!is_admin())) {
   $error_title = "Up To No Good";
   $error_text = "I can't edit an entry if you don't own it!";
   unset($rvar_submit);
 }

 if(isset($rvar_ident)) {
   $rvar_ident=strtoupper($rvar_ident);
   if(!isset($rvar_link_url)) {
     $rvar_link_url = "http://www.airnav.com/airport/" . $rvar_ident;
   }
 }
   
 if(isset($rvar_submit)) {
   # I see data, we need to insert/update as required.
   if(!isset($rvar_tower)) { $rvar_tower = 'false'; } else { $rvar_tower = 'true'; }
   if(!airport_exists($rvar_ident)) {
     # new logbook entry
     $sql = "INSERT INTO airports VALUES " .
        "('$rvar_ident',$rvar_pilot,'$rvar_fullname', '$rvar_city', " .
        "'$rvar_timezone',$rvar_tower, " .
        "'$rvar_image_url','$rvar_link_url','$rvar_detail')";
   } else {
     # editing an old entry
     $sql = "UPDATE airports SET " .
        "pilot_id=$rvar_pilot, fullname='$rvar_fullname', city='$rvar_city', " .
        "timezone='$rvar_timezone',tower=$rvar_tower, " .
        "image_url='$rvar_image_url', link_url='$rvar_link_url', detail='$rvar_detail' where ident = '$rvar_ident'";
   }
   $sql_response = lol_query($sql);

   $target = "airport.php?ident=$rvar_ident&pilot=$rvar_pilot";
   header("Location: $target");

   include "include/foot.inc";
   exit;
 }

 include "include/head.inc";

?>

 <form method="get" action="edit_airports.php">
 <div id="logbook">
 <table width="100%">
  <tr>
   <th>Ident</th>
   <th>Name</th>
   <th>City</th>
  </tr>

  <tr>
   <td><strong><?php print $rvar_ident; ?></strong></td>
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
    Tower Controlled: <input name="tower" type="checkbox" value="true" <?php if($line['tower']=='t') { print "checked=\"checked\""; } ?>/>
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
    <?php if(!isset($rvar_id)) { ?><input name="scrape" type="submit" value="Scrape Data from AirNav.com"><?php } ?>
   </td>
  </tr>
 </table>
 </div>
 <input type="hidden" name="pilot" value="<?php print $rvar_pilot; ?>" />
 <input type="hidden" name="ident" value="<?php print $rvar_ident; ?>" />
 </form>

<?
 include "include/foot.inc";
?>
