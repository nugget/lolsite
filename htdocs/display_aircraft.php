<?php
 $title = "Aircraft Database";

 include "init.inc";

 if(!isset($rvar_pilot)) {
   $error_title = "No pilot specified";
   $error_text = "You must specify a pilot in order to view a logbook!";
 }

 include "head.inc";

 if(is_mine()) {
   ?>
   <div id="buttonbar">
    <form action="edit_aircraft.php"><input type="hidden" value="<?php print 0; ?>" name="id"><input type="submit" value="Add New Entry"></form>
   </div>
   <?php
 }

 $aircraftlist = aircraft_search("");
 usort($aircraftlist,"aircraft_cmp");

?>

 <div id="logbook">
  <table>
   <tr>
    <th rowspan="2">Ident</th>
    <th rowspan="2">Equipment</th>
    <th rowspan="2">Class</th>
    <th rowspan="2">Home</th>
    <th colspan="3">Attributes</th>
    <th rowspan="2">Flights</th>
    <th rowspan="2" colspan="2">Flight Time</th>
    <th colspan="2" rowspan="2" width="100%">Comments</th>
   </tr>

   <tr>
    <th>CX</th>
    <th>HP</th> 
    <th>TW</th>
   </tr>


<?php

  $class = '';
  for($i=0; $i<count($aircraftlist); $i++) { 
    $id = aircraft_id($aircraftlist[$i],$rvar_pilot);
    $line = aircraft_detail($id);

    if( $class != "odd" ) {
      $class = "odd";
    } else {
      $class = "even";
    }

    $detaillink="detail_aircraft.php?id=" . $id;

    if($line['image_url']) {
      $features = "Image";
    } else {
      $features = "&nbsp;";
    }
?>

  <tr class="<?php print $class; ?>" onMouseOver=this.style.backgroundColor="#ffffff"
                                     onMouseOut=this.style.backgroundColor=""
                                     onclick="window.location.href='<?php print $detaillink; ?>'" >
   <td><?php print $line['ident']; ?></td>
   <td><?php print $line['makemodel']; ?></td>
   <td><?php print $line['aircraft_class']; ?></td>
   <td><?php print $line['home_field']; ?></td>
   <td><?php print checkmark($line['complex']); ?></td>
   <td><?php print checkmark($line['high_perf']); ?></td>
   <td><?php print checkmark($line['tailwheel']); ?></td>
   <td class="integer"><?php print $line['flights']; ?></td>
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
