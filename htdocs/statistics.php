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

 $sql = "select id, date from logbook where type_pic > 0 order by date limit 1";
 $firstsolo = lol_fetch_row(lol_query($sql));
 if($firstsolo[0]) {
   $sql = "select count(id) as flights, sum(type_dual) as hours from logbook where date < \"$firstsolo[1]\" and id <> $firstsolo[0]";
   $solostats = lol_fetch_row(lol_query($sql));
 }

 $pplcerts = lol_fetch_row(lol_query("select count(name) as name, min(issued) as issued from certifications where issued > 0 and name = 'Private';"));
 if($pplcerts[0]) {
   $sql = "select count(id) as flights, sum(type_cfi+type_sic+type_dual+type_pic-(type_pic*(type_dual=type_pic))) as hours from logbook where date < \"$pplcerts[1]\"";
   $pplstats = lol_fetch_row(lol_query($sql));
 }

 $comcerts = lol_fetch_row(lol_query("select count(name) as name, min(issued) as issued from certifications where issued > 0 and name = 'Commercial';"));
 if($comcerts[0]) {
   $sql = "select count(id) as flights, sum(type_cfi+type_sic+type_dual+type_pic-(type_pic*(type_dual=type_pic))) as hours from logbook where date < \"$comcerts[1]\"";
   $comstats = lol_fetch_row(lol_query($sql));
 }

 $comcerts = lol_fetch_row(lol_query("select count(name) as name, min(issued) as issued from certifications where issued > 0 and name = 'Commercial';"));
 if($comcerts[0]) {
   $sql = "select count(id) as flights, sum(type_cfi+type_sic+type_dual+type_pic-(type_pic*(type_dual=type_pic))) as hours from logbook where date < \"$comcerts[1]\"";
   $comstats = lol_fetch_row(lol_query($sql));
 }

 $cficerts = lol_fetch_row(lol_query("select count(name) as name, min(issued) as issued from certifications where issued > 0 and name = 'CFI';"));
 if($cficerts[0]) {
   $sql = "select count(id) as flights, sum(type_cfi+type_sic+type_dual+type_pic-(type_pic*(type_dual=type_pic))) as hours from logbook where date < \"$cficerts[1]\"";
   $cfistats = lol_fetch_row(lol_query($sql));
 }

 $atpcerts = lol_fetch_row(lol_query("select count(name) as name, min(issued) as issued from certifications where issued > 0 and name = 'ATP';"));
 if($atpcerts[0]) {
   $sql = "select count(id) as flights, sum(type_cfi+type_sic+type_dual+type_pic-(type_pic*(type_dual=type_pic))) as hours from logbook where date < \"$atpcerts[1]\"";
   $atpstats = lol_fetch_row(lol_query($sql));
 }

?>

   <div id="content">
     <h2>Significant Events</h2>

      <ul>

     <?php if($firstsolo[0]) { ?>
       <li>
         First Solo on <?php print $firstsolo[1]; ?>, flight number <?php print $solostats[0]; ?> at <?php print $solostats[1]; ?> hours.
       </li>
     <?php } ?>

     <?php if($pplcerts[0]) { ?>
       <li>
         Private License earned on <?php print $pplcerts[1]; ?>, flight number <?php print $pplstats[0]; ?> at <?php print $pplstats[1]; ?> hours.
       </li>
     <?php } ?>

     <?php if($comcerts[0]) { ?>
       <li>
         Commercial License earned on <?php print $comcerts[1]; ?>, flight number <?php print $comstats[0]; ?> at <?php print $comstats[1]; ?> hours.
       </li>
     <?php } ?>

     <?php if($cficerts[0]) { ?>
       <li>
         CFI earned on <?php print $cficerts[1]; ?>, flight number <?php print $cfistats[0]; ?> at <?php print $cfistats[1]; ?> hours.
       </li>
     <?php } ?>

     <?php if($atpcerts[0]) { ?>
       <li>
         ATP earned on <?php print $ctperts[1]; ?>, flight number <?php print $stptats[0]; ?> at <?php print $atpstats[1]; ?> hours.
       </li>
     <?php } ?>

      </ul>

     <h2>Miscellany</h2>
     <p>

     </p>

   </div>
 
<?php
 include "include/foot.inc";
?>
