<?php
 $title = "Passenger Detail";

 $year = date("Y");

 include "init.inc";
 if(!isset($rvar_alias)) {
   $error_title = "No ID supplied";
   $error_text = "No can do!";
 }
 $line = pax_detail($rvar_alias,$rvar_pilot,"");

 include "head.inc";

 if(is_mine()) {
   ?>
   <div id="buttonbar">
    <form action="edit_pax.php">
     <input type="hidden" value="<?php print $line['id']; ?>" name="id">
     <input type="hidden" value="<?php print $rvar_pilot; ?>" name="pilot">
     <input type="hidden" value="<?php print $rvar_alias; ?>" name="alias">
     <input type="submit" value="Edit Entry">
    </form>
   </div>
   <?php
 }

 $line['detail'] = preg_replace("/\n/","<br />",$line['detail']);
?>

 <div id="logbook">
 <table>
  <tr>
   <th rowspan="7" class="link" width="1%">
     <?php if($line['image_url'] != '') { print "<img src=\"" . $line['image_url'] . "\" /><br />"; } ?>
     <?php if($line['link_url'] != '') { print "[<a href=\"" . $line['link_url'] . "\">Link</a>]"; } ?>
     <?php print "[<a href=\"display_logbook.php?pilot=$rvar_pilot&pax=" . $line['alias'] . "\">Log</a>]";  ?>
   </th>
   <th>Alias</th>
   <th>Full Name</th>
   <th>Last Flight</th>
   <th>Elite Status</th>
  </tr>

  <tr>
   <td><?php print $line['alias']; ?></td>
   <td><?php print $line['fullname']; ?></td>
   <td><?php print $line['last_flight']; ?></td>
   <td><?php print pax_status($alias,$rvar_pilot); ?></td>
  </tr>
  <tr>
   <td colspan="4" align="center">
    <table class="embedded">
     <tr>
      <th>Year</th>
      <th>Flights</th>
      <th>Segments</th>
      <th>Landings</th>
      <th colspan="2">Hours</th>
      <th>Airports</th>
     </tr>
  <?php
     $mindate = substr($line['first_flight'],0,strpos($line['first_flight'],"-"));
     $maxdate = substr($line['last_flight'],0,strpos($line['last_flight'],"-"));

     for($i=$mindate; $i<=$maxdate; $i++) {
       $yeartotals = pax_detail($alias,$rvar_pilot,"date >= '$i-01-01' and date <= '$i-12-31'");
   ?>

      <tr>
       <td><?php print $i; ?></td>
       <td class="integer"><?php print $yeartotals['flights']; ?></td>
       <td class="integer"><?php print $yeartotals['segments']; ?></td>
       <td class="integer"><?php print $yeartotals['landings']; ?></td>
       <?php split_decimal($yeartotals['total_hours']); ?>
       <td width="100%"></td>
      </tr>

   <?php
     }

     $yeartotals = pax_detail($alias,$rvar_pilot,'');
   ?>
     <tr>
      <td><strong>Total</strong></td>
      <td class="integer"><?php print $yeartotals['flights']; ?></td>
      <td class="integer"><?php print $yeartotals['segments']; ?></td>
      <td class="integer"><?php print $yeartotals['landings']; ?></td>
      <?php split_decimal($yeartotals['total_hours']); ?>
      <td width="100%"></td>
     </tr>
    </table>
   </td>
  </tr>
  <tr>
   <th colspan="12">Detail:</th>
  </tr>
  <tr>
   <td colspan="12"><?php print $line['detail']; ?></td>
  </tr>
 </table>
 </div>

<?
 include "foot.inc";
?>
