<?php
 $title = "Passengers Database";

 $year = date("Y");

 include "init.inc";

 if(!isset($rvar_pilot)) {
   $error_title = "No pilot specified";
   $error_text = "You must specify a pilot in order to view a logbook!";
 }

 include "head.inc";

 if(is_mine()) {
   ?>
   <div id="buttonbar">
    <form action="edit_pax.php"><input type="hidden" value="<?php print 0; ?>" name="id"><input type="submit" value="Add New Entry"></form>
   </div>
   <?php
 }

 $paxlist = pax_search("");
 usort($paxlist,"pax_cmp");

?>

 <div id="logbook">
  <table>
   <tr>
    <th rowspan="2">Passenger</th>
    <th colspan="4">This Year</th>
    <th rowspan="2">Status</th>
    <th colspan="4">Total</th>
    <th rowspan="2" colspan="2" width="100%">Comments</th>
   </tr>
   <tr>
    <th>Flights</th>
    <th>Segs</th>
    <th colspan="2">Hours</th>
    <th>Flights</th>
    <th>Segs</th>
    <th colspan="2">Hours</th>
   </tr>
<?php

 $class = '';
 foreach(pax_search("") as $alias) {

  if( $class != "odd" ) {
    $class = "odd";
  } else {
    $class = "even";
  }

  $status = pax_status($alias,$rvar_pilot,"");

  $line = pax_detail($alias,$rvar_pilot,"");
  $id = $line['id'];
  if($id > 0) {
    $detaillink="detail_pax.php?id=$id";
  } else {
    $detaillink="";
  }

  $features = "&nbsp;";
  if($line['image_url']) {
    $features = "Image";
  }
  $name = $alias;
  if($line['fullname']) {
    $name = $line['fullname'] . " (" . $alias . ")";
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

   <td class="integer"><?php print $year['flights']; ?></td>
   <td class="integer"><?php print $year['segments']; ?></td>
   <?php split_decimal($year['total_hours']); ?>

   <td><?php print $status; ?></td>

   <td class="integer"><?php print $line['flights']; ?></td>
   <td class="integer"><?php print $line['segments']; ?></td>
   <?php split_decimal($line['total_hours']); ?>

   <td><?php print $line['detail']; ?>&nbsp;</td>
   <td class="hidden"><?php print $features; ?></td>
  </tr>
<?php
 };

?>

  </table>
 </div>

<?
 include "foot.inc";
?>
