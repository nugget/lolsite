<?php
 $title = "Aircraft Detail";

 include "head.php";

 if($rvar_id != 0) {
 }

 $identlink = $line['ident'];
 if(preg_match("/^N/",$identlink)) {
   $identlink = substr($identlink,1,99);
 }

 if(isadmin($rvar_passwd)) {
   ?>
   <div id="buttonbar">
    <form action="edit_aircraft.php"><input type="hidden" value="<?php print $rvar_id; ?>" name="id"><input type="submit" value="Edit Entry"></form>
   </div>
   <?php
 }

?>
 <div id="logbook">
 <table>
  <tr>
   <th rowspan="7" class="link" width="1%">
     <?php if($line['image_url'] != '') { print "<img src=\"" . $line['image_url'] . "\" /><br />"; } ?>
     [<a href="http://162.58.35.241/acdatabase/NNumSQL.asp?NNumbertxt=<?php print $identlink; ?>">FAA</a>]
     <?php if($line['link_url'] != '') { print "[<a href=\"" . $line['link_url'] . "\">Link</a>]"; } ?>
     <?php if($totals['total_hours'] > 0) { print "[<a href=\"display_logbook.php?ident=" . $line['ident'] . "\">Log</a>]"; } ?>
   </th>
   <th>Ident</th>
   <th>Equipment</th>
   <th>Aircraft Class</th>
   <th>Last Flown</th>
   <th>Flights</th>
   <th colspan="2">Total Hours</th>
  </tr>

  <tr>
   <td><?php print $line['ident']; ?></td>
   <td><?php print $line['makemodel']; ?></td>
   <td><?php print $line['longname']; ?></td>
   <td><?php print $totals['last_flight']; ?></td>
   <td class="integer"><?php print $totals['flights']; ?></td>
   <?php split_decimal($totals['total_hours']); ?>
  </tr>

  <tr>
   <th>Home Airport</th>
   <th colspan="7">Categories</th>
  </tr>

  <tr>
   <td><?php print $line['home_field']; ?></td>
   <td colspan="6">
    Complex: <?php print yesno($line[complex]); ?>
    High Performance: <?php print yesno($line[high_perf]); ?>
    Tailwheel: <?php print yesno($line[tailwheel]); ?>
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
 include "foot.php";
?>
