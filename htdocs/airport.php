<?php
 $title = "Airport Detail";

 include "init.inc";

 if(!isset($rvar_ident)) {
   $error_title ="No Airport Specified";
   $error_text = "You must specify an Airport Code";
 }
 $line = airport_detail($rvar_ident);
 $line['detail'] = preg_replace("/\n/","<br />",$line['detail']);

 include "head.inc";

 if(is_mine()) {
   if(is_user()) {
     if($line['record_exists'] == 0) {
     ?>
     <div id="buttonbar">
      <form action="edit_airports.php">
       <input type="hidden" value="<?php print $rvar_pilot; ?>" name="pilot">
       <input type="hidden" value="<?php print $line['ident']; ?>" name="ident">
       <input type="submit" value="Submit Info"></form>
     </div>
     <?php
     } else {
       $line['detail'] = $line['detail'] . "\n\n<strong>Information for this airport has been submitted and is pending administrative approval.</strong>";
     }
   }
 } else {
   if(is_admin()) {
     ?>
     <div id="buttonbar">
      <form action="edit_airports.php">
       <input type="hidden" value="<php print $rvar_pilot; ?>" name="pilot">
       <input type="hidden" value="<php print $line['ident']; ?>" name="ident">
       <input type="submit" value="Submit Info"></form>
     </div>
     <?php
   }
 }
  $line['detail'] = preg_replace("/\n/","<br />",$line['detail']);
?>
 <div id="logbook">
 <table>
  <tr>
   <th rowspan="7" class="link" width="1%">
     <?php if($line['image_url'] != '') { print "<img src=\"" . $line['image_url'] . "\" /><br />"; } ?>
     <?php if($line['link_url'] != '') { print "[<a href=\"" . $line['link_url'] . "\">Link</a>]"; } ?>
     <?php if(!strpos($line['link_url'],"airnav.com")) { print "[<a href=\"http://www.airnav.com/airport/" . $line['ident'] . "\">AirNav</a>]"; } ?>
     <?php print "[<a href=\"display_logbook.php?route=" . $line['ident'] . "\">Log</a>]";  ?>
   </th>
   <th>Ident</th>
   <th>Full Name</th>
   <th>Location</th>
   <th>Last Visit</th>
   <th>Visits</th>
  </tr>

  <tr>
   <td><?php print $line['ident']; ?></td>
   <td><?php print $line['fullname']; ?></td>
   <td><?php print $line['city']; ?></td>
   <td><?php print $line['last_visit']; ?></td>
   <td class="integer"><?php print $line['visits']; ?></td>
  </tr>

  <tr>
   <th>Timezone</th>
   <th colspan="6">Attributes</th>
  </tr>

  <tr>
   <td><?php print $line['timezone']; ?></td>
   <td colspan="6">
    Tower-Controlled: <?php print yesno($line['tower']); ?>
   </td>
  </tr>

  <tr>
   <th colspan="7">Detail:</th>
  </tr>
  <tr>
   <td colspan="7"><?php print $line['detail']; ?>&nbsp;</td>
  </tr>
 </table>
 </div>

<?
 include "foot.inc";
?>
