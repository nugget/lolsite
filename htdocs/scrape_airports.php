<?php
  $title = "Scrape Airport";
  $cvs="\$Id$";

  include "include/init.inc";

  if(!$rvar_ident) {
    $error_title = "No IDENT Specified";
    $error_text = "I can't scrape an airport unless you tell me which one!";
  }

  include "include/head.inc";
  
  $url = "http://www.airnav.com/airport/$rvar_ident";

  $handle = fopen($url,"r");
  if($handle) {
    while(!feof($handle)) {
      $buf = $buf . fread($handle,1024);
    }
  }
  if(preg_match("/  <td nowrap align=\"center\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"5\"><b>(....)<\/b>/i",$buf,$matches)) {
    $code = $matches[1];
  } 
  if(preg_match("/AirNav: (.*)<\/title>/i",$buf,$matches)) {
    $name = $matches[1];
  } 
  if(preg_match("/<br>(.*)<\/font>/i",$buf,$matches)) {
    $city = $matches[1];
  } 
  if(preg_match("/Control tower:&nbsp;<\/td><td>(.*)<\/td>/i",$buf,$matches)) {
    if(preg_match("/yes/",$buf,$matches[1])) {
      $tower = 1;
    } else {
      $tower = 0;
    }
  }
?>
   <div id="content">
   <h2>Scraped Values</h2>
   <form action="edit_airports.php">
    <table>
     <tr>
      <th>Ident</th>
      <td>
       <input type="text" size="4" name="ident" value="<?php print $rvar_ident; ?>" />
      </td>
     </tr>
     <tr>
      <th>Full Name</th>
      <td><input type="text" size="40" name="fullname" value="<?php print $name; ?>" /></td>
     </tr>
     <tr>
      <th>Location</th>
      <td><input type="text" size="40" name="city" value="<?php print $city; ?>" /></td>
     </tr>
     <tr>
      <th>Attributes</th>
      <td>
       Tower Controlled: <input name="tower" type="checkbox" value="1" <?php if($tower>0) { print "checked=\"checked\""; } ?>/>
      </td>
     </tr>
     <tr>
      <td colspan="2">
       <input type="submit" value="Accept these values" />
      </td>
     </tr>
    </table>
  </form>
  </div>

<?php

 include "include/foot.inc";

?>
