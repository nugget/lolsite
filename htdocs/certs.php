<?php
 $title = "certificates";
 $cvs="\$Id$";
 $keywords="logbook online";

 include "include/init.inc";

 # run the redirect, after which we can safely assume that $rvar_pilot is a username.

 if(!isset($rvar_pilot)) {
   $error_title = "No pilot specified";
   $error_text = "You must specify a pilot in order to view this page!";
 } else {
   pilot_id_redirect($rvar_pilot);
   $rvar_username=$rvar_pilot;
   $rvar_pilot = pilot_lookup($rvar_pilot);
   if ($rvar_pilot == 0) {
     $error_title = "Pilot not found";
     $error_text = "You must specify a known pilot";
   }
 }

 include "include/head.inc";

 $certlist = pilot_certlist($rvar_pilot);
 $valids = pilot_valid_certs($rvar_pilot);

 $buttons = '';
 for($i=0; $i<count($valids); $i++) {
   $buttons .= "     <input type=\"submit\" name=\"code\" value=\"" . $valids[$i]['code'] . "\">\n";
 }

 ?>

 <div id="logbook">
  <table>

 <?php

 for($i=0; $i<count($certlist); $i++) {
   $ratings = certificate_ratings($certlist[$i]['id']);
   $ratinglist = "";
   if(count($ratings)>0) {
     for($j=0; $j<count($ratings); $j++) {
       $ratinglist .= "&nbsp;&middot; " . $ratings[$j]['name'];
       if(is_mine()) {
         $ratinglist .= " [<a href=\"edit_cert.php?pilot=$rvar_username&id=" . $ratings[$j]['id'] . "&delrating=delrating\">Delete</a>]";
       }
       $ratinglist .= "</br>\n";
     }
   }

   ?>

    <tr>
     <th><font size="+2"><?php print $certlist[$i]['name']; ?></font></th>
     <td>
      Certificate #<?php print $certlist[$i]['number']; ?>
      <br />
      Issued on <?php print $certlist[$i]['issued']; ?>
      <br />
      <?php print $ratinglist; ?>
     </td>
     <?php
       if(is_mine()) {
         print "<td>";
         print "[<a href=\"edit_cert.php?code=" . $certlist[$i]['code'] . "&id=" . $certlist[$i]['id'] . "&pilot=$rvar_username\">Add Rating</a>]";
         print "</br>";
         print "[<a href=\"edit_cert.php?id=" . $certlist[$i]['id'] . "&pilot=$rvar_username&delcert=delcert\">Delete Cert</a>]";
         print "</td>";
       }
     ?>
    </tr>

   <?php
 }

 ?>

  </table>
 </div>

 <?php

 if(is_mine()) {
   ?>
   <div id="buttonbar">
    <form action="edit_cert.php">
     New Cert:
     <?php print $buttons; ?>
     <input type="hidden" value="0" name="id">
     <input type="hidden" value="<?php print $rvar_username; ?>" name="pilot">
    </form>
   </div>
   <?php
 }


 include "include/foot.inc";
?>
