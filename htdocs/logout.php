<?php
  $cvs="\$Id$";
 
  include "include/init.inc";

  setcookie("lol_username");
  setcookie("lol_passhash");

  header('Location: ' . $GLOBALS['baseurl'] . '/');
?>
