<?php
 $title = "certificate: Edit";
 $cvs="\$Id$";
 $keywords="logbook online";

 include "include/init.inc";

 if(!isset($rvar_pilot)) {
   $error_title = "No pilot specified";
   $error_text = "You must specify a pilot in order to work with certificates!";
 } else {
   pilot_id_redirect($rvar_pilot);
   $rvar_username = $rvar_pilot;
   $rvar_pilot = pilot_lookup($rvar_pilot);
   if ($rvar_pilot == 0) {
     $error_title = "Pilot not found";
     $error_text = "You must specify a known pilot";
   }
 }

 if(!is_mine()) {
   $error_title = "Up To No Good";
   $error_text = "You can't edit an entry if you don't own it!";
 }

 if(isset($rvar_delrating)) {
   if($rvar_id > 0) {
     $sql = "delete from pilot_rating where id = $rvar_id and pilot_id = $rvar_pilot";
     $sql_response = lol_query($sql);
   }
   $target = "certs.php?pilot=$rvar_username";
   header("Location: $target");
   exit;
 }

 if(isset($rvar_delcert)) {
   if($rvar_id > 0) {
     $sql = "delete from pilot_certificate where id = $rvar_id and pilot_id = $rvar_pilot";
     $sql_response = lol_query($sql);
   }
   $target = "certs.php?pilot=$rvar_username";
   header("Location: $target");
   exit;
 }

 if(isset($rvar_submit)) {
   # I see data, we need to insert/update as required.
   if($rvar_id == 0) {
     $sql = "INSERT INTO pilot_certificate VALUES " .
        "(default,$rvar_pilot,'$rvar_code','$rvar_number','$rvar_issued',true)";
     $sql_response = lol_query($sql);
   }

   if(isset($rvar_rating_code)) {
     $sql = "INSERT INTO pilot_rating VALUES " .
        "(default,$rvar_pilot,'$rvar_code','$rvar_rating_code',null,'$rvar_issued',true)";
     $sql_response = lol_query($sql);
   }

   $target = "certs.php?pilot=$rvar_username";
   header("Location: $target");

   include "include/foot.inc";
   exit;
 }

 if(!isset($rvar_id)) {
   $error_title = "No ID Specified";
   $error_text = "I can't edit a certificate if you don't tell me which one!";
 } else {
   if($rvar_id == 0) {
     $title = "certificate: Add";
     $line['number'] = '';
     if($rvar_code == 'Student') {
       $line['number'] = 'FF-';
     }
     $line['issued'] = '';
   } else {
     $line = pilot_certificate_detail($rvar_id);
   }
   $cert = certificate_detail($rvar_code);
 }

 include "include/head.inc";

?>

 <form method="get" action="edit_cert.php">
 <div id="logbook">
 <table width="100%">
  <tr>
   <th colspan="2">
    <font size="+2">
     <?php if ($rvar_id > 0) print "Add Rating to"; else print "Add"; ?>
     <?php print $cert['name']; ?> Certificate
     <?php print "(14 CFR &sect; " . $cert['far_part']; ?>)
    </font>
   </th>
  </tr>

  <?php if(strlen($cert['details']) > 0) { ?>
  <tr>
   <th colspan="2">
    <?php print $cert['details']; ?>
   </th>
  </tr>
  <?php } ?>

  <?php if($rvar_id == 0) { ?>
  <tr>
   <th>Number:</th>
   <td><input type="text" name="number" size="20" value="<?php print $line['number']; ?>" /></td>
  </tr>
  <tr>
   <th>Issued:</th>
   <td><input type="text" size="11" name="issued" value="<?php if ($line['issued']) echo $line['issued']; else echo date("Y-m-d"); ; ?>"></td>
  </tr>
  <?php } else { ?>
    <input type="hidden" name="issued" value="<?php print $line['issued']; ?>" />
  <?php } ?>

  <?php

    if(($cert['code'] != 'Student') and ($cert['code'] != 'ATP')) {
      $ratings = certificate_valid_ratings($cert['code']);
      $ratinglist = "<select name=\"rating_code\">";
      for($i=0; $i<count($ratings); $i++) {
        $ratinglist .= "<option value=\"" . $ratings[$i]['code'] . "\">" . $ratings[$i]['name'] . "</>\n";
      }
      $ratinglist .= '</select>';

      print "<tr><th>Rating:</th><td>$ratinglist</td></tr>\n";

    }

  ?>
  <tr>
   <td colspan="2" align="center"><input type="submit" name="submit" value="Save Changes" /></td>
  </tr>
 </table>
 </div>
 <input type="hidden" name="id" value="<?php print $rvar_id; ?>" />
 <input type="hidden" name="pilot" value="<?php print $rvar_pilot; ?>" />
 <input type="hidden" name="code" value="<?php print $cert['code']; ?>" />
 </form>

<?
 include "include/foot.inc";
?>
