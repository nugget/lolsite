<?php
  
  // $Id$

  // guard against local php setting deviations
  error_reporting(0);
  ini_set('safe_mode',1);
  ini_set('register_globals',0);
  
  // test that we can get the base bootstrap configuration information
  if(!include "config.inc") {
    print "<html><head><title>Error</title><body><h1>unable to load config.inc</h1></body></html>";
    exit;
  }

  // at this point we have enough to connect to the database
  if(!$dbh = @mysql_connect("$dbhost:$dbport",$dbuser,$dbpasswd)) {
    print "<html><head><title>Error</title><body><h1>unable to connect to the database server</h1></body></html>";
    exit;
  }
  if(!@mysql_select_db($dbname)) {
    print "<html><head><title>Error</title><body><h1>unable to connect to the lol database</h1></body></html>";
    exit;
  }

  // we're connected, let's populate our config variables
  $sql = "select * from settings";
  if(!$sqlresponse=mysql_query($sql)) {
    print "<html><head><title>Error</title><body><h1>unable to retrieve site settings</h1></body></html>";
    exit;
  }
  while ($buf = mysql_fetch_array($sqlresponse)) {
    $GLOBALS[$buf['setting']] = $buf['value'];
  }

  // process special configuration settings;
  if(array_key_exists('error_reporting',$GLOBALS)) {
    error_reporting((int) $GLOBALS['error_reporting']);
  }
?>
