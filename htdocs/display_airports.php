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
   <th>Class Color Legend</th>
   <td><font color="blue"><strong>Bravo (i.e. LAX, ATL, IAH)</strong></font></td>
   <td><font color="purple"><strong>Charlie (i.e. JAX, AUS, OKC) </strong></font></td>
   <td><font color="blue">Delta (i.e. VNY, GNV, CLL) </font></td>
  </tr>
 </table>
</div>

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
 foreach(airport_search("") as $abuf) {
  $ident = $abuf['airport'];

  if( $class != "odd" ) {
    $class = "odd";
  } else {
    $class = "even";
  }

  $line = airport_detail($ident);

   if ($line['airspace'] == "D") {
    $classStyleOpen = "<font color=\"blue\">";
    $classStyleClose = "</font>";
   } elseif ($line['airspace'] == "B") {
    $classStyleOpen = "<font color=\"blue\"><strong>";
    $classStyleClose = "</font></strong>";
   } elseif ($line['airspace'] == "C") {
    $classStyleOpen = "<font color=\"purple\"><strong>";
    $classStyleClose = "</strong></font>";
   } else {
    $classStyleOpen = "";
    $classStyleClose = "";
   }

  if($line['ident']) {
    $detaillink="airport.php?pilot=$rvar_pilot&ident=" . $line['ident'];
  } else {
    $detaillink="airport.php?pilot=$rvar_pilot&ident=" . $ident;
  }
  if($line['image_url']) {
    $features = "<img src=\"images/image.gif\" height=\"15\" width=\"64\"/>";
  } else {
    $features = "&nbsp;";
  }
  if($line['fullname']) {
    $name = $classStyleOpen . $line['fullname'] . " (" . $ident . ")" . $classStyleClose;
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
