<?php
 $title = "airport database";

 include "include/init.inc";

 # run the redirect, after which we can safely assume that $rvar_pilot is a username.
 pilot_id_redirect($rvar_pilot);
 $rvar_pilot = pilot_lookup($rvar_pilot);

 if(!isset($rvar_pilot)) {
   $error_title = "No pilot specified";
   $error_text = "You must specify a pilot in order to view a logbook!";
 }

 include "include/head.inc";

?>

 <div id="logbook">
  <table>
   <tr>
    <th>Airport</th>
    <th>Location</th>
    <th>Visits</th>
    <th colspan="2" width="100%">Comments</th>
   </tr>

<?php

 $class = '';
 foreach(airport_search("") as $ident) {

  if( $class != "odd" ) {
    $class = "odd";
  } else {
    $class = "even";
  }

  $line = airport_detail($ident);

  if($line['ident']) {
    $detaillink="airport.php?pilot=$rvar_pilot&ident=" . $line['ident'];
  } else {
    $detaillink="airport.php?pilot=$rvar_pilot&ident=" . $ident;
  }
  if($line['image_url']) {
    $features = "Image";
  } else {
    $features = "&nbsp;";
  }
  if($line['fullname']) {
    $name = $line['fullname'] . " (" . $ident . ")";
  } else {
    $name = $ident;
  }
  if(strpos($line['detail'],"\n")) {
    $line['detail'] = substr($line['detail'],0,strpos($line['detail'],"\n"));
  }
  if(strlen($line['detail'])>60) {
    $line['detail'] = substr($line['detail'],0,60) . "...";
  }
?>

  <tr class="<?php print $class; ?>" onMouseOver=this.style.backgroundColor="#ffffff"
                                     onMouseOut=this.style.backgroundColor=""
                                     onclick="window.location.href='<?php print $detaillink; ?>'" >
   <td nowrap="nowrap"><?php print $name; ?></td>
   <td nowrap="nowrap"><?php print $line['city']; ?>&nbsp;</td>
   <td><?php print airport_visits($ident,$rvar_pilot); ?></td>
   <td><?php print $line['detail']; ?>&nbsp;</td>
   <td class="hidden"><?php print $features; ?></td>
  </tr>
<?php
 };

?>

  </table>
 </div>

<?
 include "include/foot.inc";
?>
