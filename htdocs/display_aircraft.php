<?php
 $title = "Aircraft Database";

 include "include/init.inc";

 if(!isset($rvar_pilot)) {
   $error_title = "No pilot specified";
   $error_text = "You must specify a pilot in order to view a logbook!";
 }

 include "include/head.inc";

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
    $line = aircraft_detail($aircraftlist[$i],$rvar_pilot);

    if( $class != "odd" ) {
      $class = "odd";
    } else {
      $class = "even";
    }

    $detaillink="detail_aircraft.php?ident=" . $line['ident'] . "&pilot=" . $rvar_pilot;

    $features = "&nbsp;";
    if(isset($line['image_url'])) {
      if($line['image_url']) {
        $features = "Image";
      }
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
   <td><?php print $line['ident']; ?></td>
   <td><?php print $line['makemodel']; ?>&nbsp;</td>
   <td><?php print class_lookup($line['aircraft_class']); ?></td>
   <td><?php print $line['home_field']; ?>&nbsp;</td>
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
 include "include/foot.inc";
?>
