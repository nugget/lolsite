<?php
 $title = "Airport Detail";

 include "include/init.inc";

 if(!isset($rvar_ident)) {
   $error_title ="No Airport Specified";
   $error_text = "You must specify an Airport Code";
 }
 $line = airport_detail($rvar_ident);
 $line['detail'] = preg_replace("/\n/","<br />",$line['detail']);

 include "include/head.inc";

 if(is_user()) {
   $rvar_pilot = is_user();
   if($line['record_exists'] == 0) {
     if(is_mine()) {
       ?>
       <div id="buttonbar">
         <form action="edit_airports.php">
         <input type="hidden" value="<?php print $rvar_pilot; ?>" name="pilot">
         <input type="hidden" value="<?php print $line['ident']; ?>" name="ident">
         <input type="submit" value="Add Airport"></form>
       </div>
       <?php
     }
   } else {
     if(is_mine() or is_admin()) {
       ?>
       <div id="buttonbar">
        <form action="edit_airports.php">
         <input type="hidden" value="<?php print $rvar_pilot; ?>" name="pilot">
         <input type="hidden" value="<?php print $line['ident']; ?>" name="ident">
         <input type="submit" value="Edit Airport"></form>
       </div>
       <?php
     }
   }
 }
 $line['detail'] = preg_replace("/\n/","<br />",$line['detail']);
?>
 <div id="logbook">
 <table>
  <tr>
   <th rowspan="7" class="link" width="1%">
     <?php if($line['image_url'] != '') { print "<img src=\"" . $line['image_url'] . "\" /><br />"; } ?>
     <?php if($line['link_url'] != '') { print "[<a class=\"sidebar\" href=\"" . $line['link_url'] . "\">Link</a>]"; } ?>
     <?php if(!strpos($line['link_url'],"flightaware.com/resources")) { print "[<a class=\"sidebar\" href=\"http://flightaware.com/resources/airport/" . $line['ident'] . "\">Info</a>]"; } ?>
     <?php if(!strpos($line['link_url'],"flightaware.com/live")) { print "[<a class=\"sidebar\" href=\"http://flightaware.com/live/airport/" . $line['ident'] . "\">Tracking</a>]"; } ?>
     <?php print "[<a class=\"sidebar\" href=\"display_logbook.php?pilot=$pilot_name&route=" . $line['ident'] . "\">Log</a>]";  ?>
   </th>
   <th>Ident</th>
   <th>Full Name</th>
   <th>Location</th>
   <th>Last Visit</th>
   <th>Visits</th>
  </tr>

  <tr>
   <td><?php print $line['ident']; ?></td>
   <td><?php print $line['fullname']; ?>&nbsp;</td>
   <td><?php print $line['city']; ?>&nbsp;</td>
   <td><?php print $line['last_visit']; ?></td>
   <td class="integer"><?php print $line['visits']; ?></td>
  </tr>

  <tr>
   <th>Timezone</th>
   <th colspan="6">Attributes</th>
  </tr>

  <tr>
   <td><?php print $line['timezone']; ?>&nbsp;</td>
   <td colspan="6">
     <?php if($line['airspace']<>'') { print "Class " . $line['airspace']; }; ?>
     <?php if($line['tower']=='t') { print ", tower controlled"; }; ?>
     &nbsp;
   </td>
  </tr>

  <tr>
   <th colspan="7">Detail:</th>
  </tr>
  <tr>
   <td colspan="7">
    <?php print $line['detail']; ?>&nbsp;
   </td>
  </tr>
 </table>
 </div>

<?
 include "include/foot.inc";
?>
