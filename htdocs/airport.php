<?php
 $title = "Airport Detail";

 include "init.inc";

 if(isset($rvar_ident)) {
   if(!$line = airport_detail($rvar_ident)) {
     $notice_title ="Airport not in Database";
     $notice_text = "$rvar_ident is not in the lol database yet.";
   } else {
     $line['detail'] = preg_replace("/\n/","<br />",$line['detail']);
   }
 } else {
   $error_title ="No Airport Specified";
   $error_text = "You must specify an Airport Code";
 }

 include "head.inc";

 if(is_admin()) {
   ?>
   <div id="buttonbar">
    <form action="edit_airports.php"><input type="hidden" value="<?php print $rvar_id; ?>" name="id"><input type="submit" value="Edit Entry"></form>
   </div>
   <?php
 }

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
   <td><?php print $totals['last_visit']; ?></td>
   <td class="integer"><?php print $totals['visits']; ?></td>
  </tr>

  <tr>
   <th>Timezone</th>
   <th colspan="6">Attributes</th>
  </tr>

  <tr>
   <td><?php print $line['timezone']; ?></td>
   <td colspan="6">
    Tower-Controlled: <?php print yesno($line[tower]); ?>
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
