<?php
 $title = "pilot statistics";
 $cvs="\$Id$";
 $keywords="logbook online";

 include "include/init.inc";

 if(!isset($rvar_pilot)) {
   $error_title = "No pilot specified";
   $error_text = "You must specify a pilot in order to view a logbook!";
 }

 include "include/head.inc";

 $solostats = pilot_solostats($rvar_pilot);
 $xcstats   = pilot_xcstats($rvar_pilot);
 $pplstats  = pilot_certstats($rvar_pilot,'Private');
 $comstats  = pilot_certstats($rvar_pilot,'Commercial');
 $cfistats  = pilot_certstats($rvar_pilot,'CFI');
 $atpstats  = pilot_certstats($rvar_pilot,'ATP');
 $gliderstats  = pilot_certstats($rvar_pilot,'Glider');

?>

   <div id="block">
     <div id="block1">
       <h3>Significant Events</h3>
       <table>
         <tr><th>Achievement</th><th>Date</th><th>Flight</th><th colspan="2">Hours</th></tr>

         <?php if($solostats['date']) { ?>
         <tr>
           <td>First Solo</td>
           <td><?php print $solostats['date']; ?></td>
           <td class="integer"><?php print $solostats['flights']; ?></td>
           <?php print split_decimal($solostats['hours']); ?>
         </tr>
         <?php } ?>

         <?php if($xcstats['date']) { ?>
         <tr>
           <td>First Cross Country</td>
           <td><?php print $xcstats['date']; ?></td>
           <td class="integer"><?php print $xcstats['flights']; ?></td>
           <?php print split_decimal($xcstats['hours']); ?>
         </tr>
         <?php } ?>

         <?php if($pplstats['date']) { ?>
          <tr>
            <td>Private Pilots License</td>
            <td><?php print $pplstats['date']; ?></td>
            <td class="integer"><?php print $pplstats['flights']; ?></td>
            <?php print split_decimal($pplstats['hours']); ?>
          </tr>
          <?php } ?>

          <?php if($gliderstats['date']) { ?>
          <tr>
            <td>Glider Pilots License</td>
            <td><?php print $gliderstats['date']; ?></td>
            <td class="integer"><?php print $gliderstats['flights']; ?></td>
            <?php print split_decimal($gliderstats['hours']); ?>
          </tr>
          <?php } ?>

          <?php if($comstats['date']) { ?>
          <tr>
            <td>Commercial Pilots License</td>
            <td><?php print $comstats['date']; ?></td>
            <td class="integer"><?php print $comstats['flights']; ?></td>
            <?php print split_decimal($comstats['hours']); ?>
          </tr>
          <?php } ?>

          <?php if($cfistats['date']) { ?>
          <tr>
            <td>CFI License</td>
            <td><?php print $cfistats['date']; ?></td>
            <td class="integer"><?php print $cfistats['flights']; ?></td>
            <?php print split_decimal($cfistats['hours']); ?>
          </tr>
          <?php } ?>

          <?php if($atpstats['date']) { ?>
          <tr>
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
         <tr><th colspan="2">Moo</th></tr>

         <tr>
           <th style="text-align: right;">Certifications</th>
           <td><?php print pilot_certlist($rvar_pilot); ?></td>
         </tr>

       </table>


      </div>

    </div>
 
<?php
 include "include/foot.inc";
?>
