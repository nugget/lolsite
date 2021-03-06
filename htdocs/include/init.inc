<?php
  
  // $Id$

  // guard against local php setting deviations
  error_reporting(E_ALL);
  ini_set('safe_mode',1);
  ini_set('register_globals',0);
  
  // test that we can get the base bootstrap configuration information
  if(!include "constants.inc") {
    print "<html><head><title>Error</title><body><h1>unable to load constants.inc</h1></body></html>";
    exit;
  }
  if(!include "config.inc") {
    print "<html><head><title>Error</title><body><h1>unable to load config.inc</h1></body></html>";
    exit;
  }
 
  // secondly we need version and target schema information for sanity checking
  if(!include "version.inc") {
    print "<html><head><title>Error</title><body><h1>unable to load version.inc</h1></body></html>";
    exit;
  }

  // at this point we have enough to connect to the database
  if($dbtype == 'mysql') {
    if(!$dbh = @mysql_connect("$dbhost:$dbport",$dbuser,$dbpasswd)) {
      print "<html><head><title>Error</title><body><h1>unable to connect to the database server</h1></body></html>";
      exit;
    }
    if(!@mysql_select_db($dbname)) {
      print "<html><head><title>Error</title><body><h1>unable to connect to the lol database</h1></body></html>";
      exit;
    }
  } else {
    if(!$dbh = pg_connect("dbname=$dbname user=$dbuser password=$dbpasswd")) {
      print "<html><head><title>Error</title><body><h1>unable to connect to the database server</h1></body></html>";
      exit;
    }
  }

  // we're connected, let's populate our config variables
  $sql = "select * from settings";
  if($dbtype == 'mysql') {
    if(!$sqlresponse=mysql_query($sql)) {
      print "<html><head><title>Error</title><body><h1>unable to retrieve site settings</h1></body></html>";
      exit;
    }
    while ($buf = mysql_fetch_array($sqlresponse)) {
      $GLOBALS[$buf['setting']] = $buf['value'];
    }
  } else {
    if(!$sqlresponse=pg_query($sql)) {
      print "<html><head><title>Error</title><body><h1>unable to retrieve site settings</h1></body></html>";
      exit;
    }
    while ($buf = pg_fetch_array($sqlresponse)) {
      $GLOBALS[$buf['setting']] = $buf['value'];
    }
  }

  // if our target schema differs from our current schema, abort for migration
  if(array_key_exists('target_schema',$GLOBALS)) {
    if(array_key_exists('schema',$GLOBALS)) {
      if($GLOBALS['schema'] <> $GLOBALS['target_schema']) {
        print "<html><head><title>Error</title><body><h1>This code is for schema v";
        print $GLOBALS['target_schema'];
        print " but the database is v";
        print $GLOBALS['schema'];
        print ".  Please convert.</h1></body></html>";
        exit;
      }
    } else {
      print "<html><head><title>Error</title><body><h1>No target_schema value found in the settings table.</h1></body></html>";
      exit;
    }
  } else {
    print "<html><head><title>Error</title><body><h1>No target_schema value found in version.inc.</h1></body></html>";
    exit;
  }

  // process special configuration settings;
  if(array_key_exists('error_reporting',$GLOBALS)) {
    error_reporting((int) $GLOBALS['error_reporting']);
  }

  // REMOVE THIS
  error_reporting(E_ALL);

  // OK, Everything looks healthy, let's pull in our provided stuff from the user
  import_request_variables("cpg","rvar_");

  include("functions.inc");

  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
  header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0"); // HTTP/1.1
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache"); // HTTP/1.0

?>
