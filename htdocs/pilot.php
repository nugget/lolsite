<?php
 $title = "pilot statistics";
 $cvs="\$Id$";
 $keywords="logbook online";

 include "include/init.inc";

 if(!isset($rvar_pilot)) {
   $error_title = "No pilot specified";
   $error_text = "You must specify a pilot in order to view a logbook!";
 } else {
   pilot_id_redirect($rvar_pilot);
   $rvar_pilot = pilot_lookup($rvar_pilot);
   if ($rvar_pilot == 0) {
     $error_title = "Pilot not found";
     $error_text = "You must specify a known pilot";
   }
 }

 include "include/head.inc";

 $firststats = pilot_firststats($rvar_pilot);
 $solostats = pilot_solostats($rvar_pilot);
 $checkstats= pilot_checkstats($rvar_pilot);
 $xcstats   = pilot_xcstats($rvar_pilot);
 $ifrstats  = pilot_ifrstats($rvar_pilot);
 $pplstats  = pilot_certstats($rvar_pilot,'Private');
 $comstats  = pilot_certstats($rvar_pilot,'Commercial');
 $cfistats  = pilot_certstats($rvar_pilot,'CFI');
 $atpstats  = pilot_certstats($rvar_pilot,'ATP');
 $gliderstats  = pilot_certstats($rvar_pilot,'Glider');

 $certlist = pilot_certlist($rvar_pilot);

 $currency_text = "Not implemented";

 $medical_text  = "Not implemented";

?>

   <div id="block">
     <div id="block1">
       <h3>Significant Events</h3>
       <table>
         <tr><th>Achievement</th><th>Date</th><th>Flight</th><th colspan="2">Hours</th></tr>

         <tr onMouseOver=this.style.backgroundColor="#ffffff"
             onMouseOut=this.style.backgroundColor=""
             onclick="window.location.href='<?php print "detail_logbook.php?id=$firststats[id]" ?>'" >
           <td>First Flight</td>
           <td><?php print $firststats['date']; ?></td>
           <td class="integer"><?php print $firststats['flights']; ?></td>
           <?php print split_decimal($firststats['hours']); ?>
         </tr>

         <?php if($solostats['date']) { ?>
         <tr onMouseOver=this.style.backgroundColor="#ffffff"
             onMouseOut=this.style.backgroundColor=""
             onclick="window.location.href='<?php print "detail_logbook.php?id=$solostats[id]" ?>'" >
           <td>First Solo</td>
           <td><?php print $solostats['date']; ?></td>
           <td class="integer"><?php print $solostats['flights']; ?></td>
           <?php print split_decimal($solostats['hours']); ?>
         </tr>
         <?php } ?>

         <?php if($checkstats['date']) { ?>
         <tr onMouseOver=this.style.backgroundColor="#ffffff"
             onMouseOut=this.style.backgroundColor=""
             onclick="window.location.href='<?php print "detail_logbook.php?id=$checkstats[id]" ?>'" >
           <td>First Checkride</td>
           <td><?php print $checkstats['date']; ?></td>
           <td class="integer"><?php print $checkstats['flights']; ?></td>
           <?php print split_decimal($checkstats['hours']); ?>
         </tr>
         <?php } ?>

         <?php if($xcstats['date']) { ?>
         <tr onMouseOver=this.style.backgroundColor="#ffffff"
             onMouseOut=this.style.backgroundColor=""
             onclick="window.location.href='<?php print "detail_logbook.php?id=$xcstats[id]" ?>'" >
           <td>First Cross Country</td>
           <td><?php print $xcstats['date']; ?></td>
           <td class="integer"><?php print $xcstats['flights']; ?></td>
           <?php print split_decimal($xcstats['hours']); ?>
         </tr>
         <?php } ?>

         <?php if($ifrstats['date']) { ?>
         <tr onMouseOver=this.style.backgroundColor="#ffffff"
             onMouseOut=this.style.backgroundColor=""
             onclick="window.location.href='<?php print "detail_logbook.php?id=$ifrstats[id]" ?>'" >
           <td>First IFR Flight</td>
           <td><?php print $ifrstats['date']; ?></td>
           <td class="integer"><?php print $ifrstats['flights']; ?></td>
           <?php print split_decimal($ifrstats['hours']); ?>
         </tr>
         <?php } ?>

         <?php if($pplstats['date']) { ?>
         <tr onMouseOver=this.style.backgroundColor="#ffffff"
             onMouseOut=this.style.backgroundColor=""
             onclick="window.location.href='<?php print "detail_logbook.php?id=$pplstats[id]" ?>'" >
            <td>Private Pilots License</td>
            <td><?php print $pplstats['date']; ?></td>
            <td class="integer"><?php print $pplstats['flights']; ?></td>
            <?php print split_decimal($pplstats['hours']); ?>
          </tr>
          <?php } ?>

          <?php if($gliderstats['date']) { ?>
          <tr onMouseOver=this.style.backgroundColor="#ffffff"
              onMouseOut=this.style.backgroundColor=""
              onclick="window.location.href='<?php print "detail_logbook.php?id=$gliderstats[id]" ?>'" >
            <td>Glider Pilots License</td>
            <td><?php print $gliderstats['date']; ?></td>
            <td class="integer"><?php print $gliderstats['flights']; ?></td>
            <?php print split_decimal($gliderstats['hours']); ?>
          </tr>
          <?php } ?>

          <?php if($comstats['date']) { ?>
          <tr onMouseOver=this.style.backgroundColor="#ffffff"
              onMouseOut=this.style.backgroundColor=""
              onclick="window.location.href='<?php print "detail_logbook.php?id=$comstats[id]" ?>'" >
            <td>Commercial Pilots License</td>
            <td><?php print $comstats['date']; ?></td>
            <td class="integer"><?php print $comstats['flights']; ?></td>
            <?php print split_decimal($comstats['hours']); ?>
          </tr>
          <?php } ?>

          <?php if($cfistats['date']) { ?>
          <tr onMouseOver=this.style.backgroundColor="#ffffff"
              onMouseOut=this.style.backgroundColor=""
              onclick="window.location.href='<?php print "detail_logbook.php?id=$cfistats[id]" ?>'" >
            <td>CFI License</td>
            <td><?php print $cfistats['date']; ?></td>
            <td class="integer"><?php print $cfistats['flights']; ?></td>
            <?php print split_decimal($cfistats['hours']); ?>
          </tr>
          <?php } ?>

          <?php if($atpstats['date']) { ?>
          <tr onMouseOver=this.style.backgroundColor="#ffffff"
              onMouseOut=this.style.backgroundColor=""
              onclick="window.location.href='<?php print "detail_logbook.php?id=$atpstats[id]" ?>'" >
            <td>ATP License</td>
            <td><?php print $atpstats['date']; ?></td>
            <td class="integer"><?php print $atpstats['flights']; ?></td>
            <?php print split_decimal($atpstats['hours']); ?>
          </tr>
          <?php } ?>

        </table>
      </div>

      <div id="block2">
       <h3>At A Glance...</h3>
       <table>
         <tr><th>Certifications</th></tr>

         <tr>
           <td><?php for($i=0; $i<count($certlist); $i++) { if($certlist[$i]['code'] <> 'Medical') { print $certlist[$i]['code'] . " "; } } ?></td>
         </tr>

         <tr>
          <th>Passenger Currency</th>
         </tr>
         <tr>
          <td><?php print $currency_text; ?></td>
         </tr>

         <tr>
          <th>Medical Currency</th>
         </tr>
         <tr>
          <td><?php print $medical_text; ?></td>
         </tr>

       </table>
      </div>

      <div id="block3">
       <h3>Counts and Totals</h3>
       <table>
         <tr><th>Activity</th><th colspan="2">&lt;90</th><th colspan="2">Year</th><th colspan="2">Total</th></tr>

         <tr>
           <td>Flights</td>
           <td class="integer" colspan="2"><?php print pilot_flights($rvar_pilot,90,'D'); ?></td>
           <td class="integer" colspan="2"><?php print pilot_flights($rvar_pilot,365,'D'); ?></td>
           <td class="integer" colspan="2"><?php print pilot_flights($rvar_pilot,0,'D'); ?></td>
         </tr>
         <tr>
           <td>Total Hours</td>
           <?php split_decimal(pilot_hours($rvar_pilot,90)); ?>
           <?php split_decimal(pilot_hours($rvar_pilot,365)); ?>
           <?php split_decimal(pilot_hours($rvar_pilot,0)); ?>
         </tr>

         <tr>
           <td>PIC Hours</td>
           <?php split_decimal(pilot_pic($rvar_pilot,90)); ?>
           <?php split_decimal(pilot_pic($rvar_pilot,365)); ?>
           <?php split_decimal(pilot_pic($rvar_pilot,0)); ?>
         </tr>

         <tr>
           <td>X/C Hours</td>
           <?php split_decimal(pilot_xc($rvar_pilot,90)); ?>
           <?php split_decimal(pilot_xc($rvar_pilot,365)); ?>
           <?php split_decimal(pilot_xc($rvar_pilot,0)); ?>
         </tr>

         <tr>
           <td>IFR Hours</td>
           <?php split_decimal(pilot_ifr($rvar_pilot,90)); ?>
           <?php split_decimal(pilot_ifr($rvar_pilot,365)); ?>
           <?php split_decimal(pilot_ifr($rvar_pilot,0)); ?>
         </tr>

         <tr>
           <td>Night Hours</td>
           <?php split_decimal(pilot_night($rvar_pilot,90)); ?>
           <?php split_decimal(pilot_night($rvar_pilot,365)); ?>
           <?php split_decimal(pilot_night($rvar_pilot,0)); ?>
         </tr>

         <tr>
           <td>Landings (Day)</td>
           <td class="integer" colspan="2"><?php print pilot_landings($rvar_pilot,90,'D'); ?></td>
           <td class="integer" colspan="2"><?php print pilot_landings($rvar_pilot,365,'D'); ?></td>
           <td class="integer" colspan="2"><?php print pilot_landings($rvar_pilot,0,'D'); ?></td>
         </tr>
         <tr>
           <td>Landings (Night)</td>
           <td class="integer" colspan="2"><?php print pilot_landings($rvar_pilot,90,'N'); ?></td>
           <td class="integer" colspan="2"><?php print pilot_landings($rvar_pilot,365,'N'); ?></td>
           <td class="integer" colspan="2"><?php print pilot_landings($rvar_pilot,0,'N'); ?></td>
         </tr>

       </table>
      </div>

    </div>
 
<?php
 include "include/foot.inc";
?>
