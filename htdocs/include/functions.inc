<?php

  // Authentication functions

  function lol_query($querystring) {
    if($querystring) {
      if($GLOBALS['dbtype'] == 'mysql') {
        return mysql_query($querystring);
      } else {
        return pg_query($querystring);
      }
    }
  }

  function lol_fetch_array($qbuf) {
    if($qbuf) {
      if($GLOBALS['dbtype'] == 'mysql') {
        return mysql_fetch_array($qbuf);
      } else {
        return pg_fetch_array($qbuf);
      }
    }
  }

  function lol_fetch_row($qbuf) {
    if($qbuf) {
      if($GLOBALS['dbtype'] == 'mysql') {
        return mysql_fetch_row($qbuf);
      } else {
        return pg_fetch_row($qbuf);
      }
    }
  }

  function is_user() {
    global $rvar_lol_username, $rvar_lol_passhash;

    if(isset($rvar_lol_username) and isset($rvar_lol_passhash)) {
      $sql = "select id from pilot where username = '$rvar_lol_username' and password = '$rvar_lol_passhash'";
      if($id = lol_fetch_array(lol_query($sql))) {
        $sql = "update pilot set last_login = now() where id = " . $id['id'];
        if(lol_query($sql)) {
          return $id['id'];
        }
      }
    }
    return FALSE;
  }

  function is_admin() {
    global $rvar_lol_username, $rvar_lol_passhash;

    if(isset($rvar_lol_username) and isset($rvar_lol_passhash)) {
      $sql = "select id,admin from pilot where username = '$rvar_lol_username' and password = '$rvar_lol_passhash'";
      if($id = lol_fetch_array(lol_query($sql))) {
        if($id['admin']) {
          return $id['id'];
        }
      }
    }
    return FALSE;
  }

  function is_mine() {
    global $rvar_pilot, $rvar_lol_username, $rvar_lol_passhash;
    if(!isset($rvar_pilot)) {
      return FALSE;
    }
    if(isset($rvar_lol_username) and isset($rvar_lol_passhash)) {
      $sql = "select id from pilot where username = '$rvar_lol_username' and password = '$rvar_lol_passhash'";
      if($id = lol_fetch_array(lol_query($sql))) {
        if($id['id'] == $rvar_pilot) {
          return $id['id'];
        }
      }
    }
    return FALSE;
  }

  function is_valid() {
    global $rvar_pilot;
    if(isset($rvar_pilot)) {
      $sql = "select id from pilot where id = $rvar_pilot";
      if(lol_fetch_array(lol_query($sql))) {
        return TRUE;
      }
    }
    return FALSE;
  }

  function username_exists($username) {
    $sql = "select id from pilot where username = '$username'";
    if($id = lol_fetch_array(lol_query($sql))) {
      return $id['id'];
    }
    return FALSE;
  }

  function add_user($username,$password) {
    $sql = "insert into pilot (username,password) values ('$username','".md5($password)."')";
    if(lol_query($sql)) {
      $sql = "select id,admin from pilot where username = '$username' and password = '".md5($password)."'";
      if($id = lol_fetch_array(lol_query($sql))) {
        if($id['id'] == 1) {
          lol_query("update pilot set admin = true where id = " . $id['id']);
        }
        return $id['id'];
      }
    }
    return FALSE;
  }


  // Formatting and display functions

  function split_decimal($num) {
    $i = (int)$num;
    print "<td class=\"integer\">$i</td>";
    $f = sprintf("%02d", (int)(($num - $i) * 100 + 0.5));
    while (strlen($f) > 1 && $f[strlen($f)-1] == '0') { # trim trailing zeros
        $f = substr($f, 0, strlen($f)-1);
    }
    print "<td class=\"decimal\">$f</td>";
  }

  function hobbs($hours) {
    $buf = "<p class=\"gaugetitle\">HOBBS METER</p><p class=\"gauge\">";

    $hoursstr = sprintf($GLOBALS['hobbsfmt'],$hours);
    $hobbsified = "";
    $style = "digit";
    for($i=0; $i<strlen($hoursstr); $i++) {
      $c = substr($hoursstr,$i,1);
      if($c == '.') {
        $style = "digitdec";
      } else {
        $hobbsified = "$hobbsified<a class=\"$style\">$c</a>";
      }
    }
    $buf = $buf . $hobbsified . "</p>\n";
    return $buf;
  }

  function yesno($bool) {
    if($bool == 0) {
      return "N";
    } else {
      return "Y";
    }
  }

  function checkmark($bool) {
    if($bool == 0) {
      return "&nbsp;";
    } else {
      return "X";
    }
  }



  // Math functions

  function count_elements($buf) {
    $buf = trim($buf);
    if($buf) {
      $elements = explode(" ",$buf);
      return count($elements);
    } else {
      return 0;
    }
  }

  function larger($a,$b) {
    if($a > $b) {
      return $a;
    } else {
      return $b;
    }
  }       



  // Data functions

  function logbook_launches($whereclause) {
    $launches = lol_fetch_row(lol_query("SELECT count(launch_type) FROM logbook WHERE $whereclause"));
    $launches = $launches[0];
    return $launches;
  }

  function pilot_hours($pilot_id) {
    $count = lol_fetch_row(lol_query("select sum(type_cfi+type_sic+type_dual+type_pic-(case when type_dual=type_pic then type_pic else 0 end)) from logbook where pilot_id = $pilot_id"));
    $count = $count[0];
    return $count;
  }

  function pilot_name($pilot_id) {
    $sql = "select username, displayname from pilot where id = $pilot_id";
    $pinfo = lol_fetch_array(lol_query($sql));
    if(isset($pinfo{'displayname'})) {
      return $pinfo{'displayname'};
    } else {
      return $pinfo{'username'};
    }
  }

  function pilot_search($search) {
    $sql = "select id from pilot where username like '%$search%' or displayname like '%$search%'";
    if($sqlresponse = lol_query($sql)) {
      $list = array();
      while($line = lol_fetch_array($sqlresponse)) {
        $list[] = $line['id'];
      }
      return $list;
    }
  }

  function pilot_cmp($a,$b) {
    $vala = pilot_hours($a);
    $valb = pilot_hours($b);

    if($vala == $valb) return 0;
    return ($vala > $valb) ? -1 : 1;
  }

  function class_lookup($class) {
    if(!$class) {
      return "????";
    }
    if($class = (int) $class) {
      return $GLOBALS['classcode'][$class];
    } else {
      for($i=0; $i<count($GLOBALS['classcode']); $i++) {
        if($$GLOBALS['classcode'][$i] == $class) {
          return $i;
        }
      }
    }
  }

  function class_longname($class) {
    if($class = (int) $class) {
      return $GLOBALS['classname'][$class];
    }
  }

  function class_time($class,$logid) {
    $sql = "select aircraft_class from aircraft, logbook where logbook.id = $logid and aircraft.ident = logbook.ident and aircraft.pilot_id = logbook.pilot_id";
    $buf = lol_fetch_array(lol_query($sql));
    $aircraft_class = (int) $buf['aircraft_class'];
    $hours = logbook_hours($logid);
    if($class == class_lookup($aircraft_class)) {
      return $hours;
    } else {
      return 0;
    }
  }

  function logbook_hours($logid) {
    $sql = "select type_cfi, type_dual, type_pic, type_sic from logbook where id = $logid";
    if($line = lol_fetch_array(lol_query($sql))) {
      $duration = (int) $line['type_cfi']+$line['type_dual']+$line['type_pic']+$line['type_sic'];
      if($line['type_dual'] == $line['type_pic']) {
        $duration = (int) $duration - $line['type_dual'];
      }
    } else {
      $duration = 0;
    }
    return $duration;
  }  

  function logbook_pilot($logid) {
    $sql = "select pilot_id from logbook where id = $logid";
    if($line = lol_fetch_array(lol_query($sql))) {
      return (int) $line['pilot_id'];
    }
  }

  function logbook_detail($logid) {
    $sql = "select * from logbook where id = $logid";
    $line = lol_fetch_array(lol_query($sql));
    return $line;
  }

  function logbook_recent() {
    $sql = "select id from logbook order by date desc, id desc limit 10";
    if($sqlresponse = lol_query($sql)) {
      $list = array();
      while($line = lol_fetch_array($sqlresponse)) {
        $list[] = $line['id'];
      }
      return $list;
    }
  } 
     
  function aircraft_equipment($ident) {
    global $rvar_pilot;
    if(isset($rvar_pilot)) {
      $sql = "select makemodel from aircraft where ident = '$ident' and pilot_id = $rvar_pilot";
      if($line = lol_fetch_array(lol_query($sql))) {
        return $line['makemodel'];
      }
    }
  }

  function airport_exists($ident) {
    $buf = lol_fetch_array(lol_query("select count(ident) as num from airports where ident = '$ident'"));
    $buf = $buf['num'];
    if($buf > 0) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  function airport_search($search) {
    global $rvar_pilot;

    $buf = '';
    $sql = "select distinct route from logbook";
    if(isset($rvar_pilot)) {
      $sql = $sql . " where pilot_id = $rvar_pilot";
    }
    $sqlresponse = lol_query($sql);
    while($line = lol_fetch_array($sqlresponse)) {
      $buf = $buf . " " . $line['route'];
    }
    $buf = " " . strtoupper($buf) . " ";
    $buf = preg_replace("/ LCL /"," ",$buf);
    $buf=trim($buf);
    if(strlen($buf)>0) {
      $elements = explode(" ",$buf);
      $elements = array_values(array_unique($elements));
      usort($elements,"airport_cmp");
      return $elements;
    } else {
      return;
    }
  }

  function airport_detail($ident) {
    global $rvar_pilot;
    if(!isset($rvar_pilot)) {
      $pilot = 0;
    } else {
      $pilot = $rvar_pilot;
    }

    $filler['pilot_id'] = $pilot;
    $filler['ident'] = $ident;
    $filler['image_url'] = '';
    $filler['link_url'] = '';
    $filler['fullname'] = '';
    $filler['city'] = '';
    $filler['timezone'] = '';
    $filler['tower'] = 0;
    $filler['detail'] = '';

    $sql = "select * from airports where ident = '$ident' and (pilot_id = 0 or pilot_id = $pilot)";
    $detail = lol_fetch_array(lol_query($sql));

    $sql = "select count(id) as visits, min(date) as first_visit, max(date) as last_visit from logbook where ' '||route||' ' like '% $ident %'";
    if($pilot > 0) {
      $sql = $sql . " and pilot_id = $pilot";
    }
    $totals = lol_fetch_array(lol_query($sql));

    $sql = "select count(*) as record_exists from airports where ident = '$ident'";
    $recs = lol_fetch_array(lol_query($sql));
   
    return array_merge($filler,$detail,$totals, $recs);
  }

  function airport_name($ident) {
    $ident = strtoupper($ident);
    $sql = "select ident, fullname from airports where ident = '$ident' or ident = 'K$ident'";
    if($line = lol_fetch_array(lol_query($sql))) {
      if(isset($line['fullname'])) {
        return $line['fullname'] . " (" . $line['ident'] . ")";
      } else {
        return $line['ident'];
      }
    } else {
      return $ident;
    }
  }

  function airport_visits($ident,$pilot) {
    $sql = "select count(id) as visits from logbook where ' '||route||' ' like '% $ident %' or ' '||route||' ' like '% k$ident %'";
    if($line = lol_fetch_array(lol_query($sql))) {
      return $line['visits'];
    }
    return 0;
  }

  function airport_cmp($a,$b) {
    global $rvar_pilot;

    $pilot = 0;
    if(isset($rvar_pilot)) {
      $pilot = $rvar_pilot;
    }

    $vala = airport_visits($a,$pilot);
    $valb = airport_visits($b,$pilot);

    if($vala == $valb) return 0;
    return ($vala > $valb) ? -1 : 1;
  }

  function aircraft_search($search) {
    global $rvar_pilot;
    $list = array();

    $sql = "select distinct ident from logbook where ident like '%$search%'";
    if(isset($rvar_pilot)) {
      $sql = $sql . " and pilot_id = " . (int) $rvar_pilot;
    }
    if($sqlresponse = lol_query($sql)) {
      while($line = lol_fetch_array($sqlresponse)) {
        $list[] = $line['ident'];
      }
    }

    $sql = "select distinct ident from aircraft where ident like '%$search%'";
    if(isset($rvar_pilot)) {
      $sql = $sql . " and pilot_id = " . (int) $rvar_pilot;
    }
    if($sqlresponse = lol_query($sql)) {
      while($line = lol_fetch_array($sqlresponse)) {
        $list[] = $line['ident'];
      }
    }

    $list = array_values(array_unique($list));
    usort($list,"aircraft_cmp");

    return $list;
  }

  function aircraft_cmp($a,$b) {
    global $rvar_pilot;

    $pilot = 0;
    if(isset($rvar_pilot)) {
      $pilot = $rvar_pilot;
    }

    $vala = aircraft_flights($a,$pilot);
    $valb = aircraft_flights($b,$pilot);

    if($vala == $valb) return 0;
    return ($vala > $valb) ? -1 : 1;
  }

  function aircraft_flights($ident,$pilot) {
    $sql = "select count(id) as flights from logbook where ident = '$ident'";
    if($pilot > 0) {
      $sql = $sql . " and pilot_id = $pilot";
    }
    if($line = lol_fetch_array(lol_query($sql))) {
      return $line['flights'];
    }
    return 0;
  }

  function aircraft_id($ident,$pilot) {
    $sql = "select id from aircraft where ident = '$ident' and pilot_id = $pilot";
    if($line = lol_fetch_array(lol_query($sql))) {
      return $line['id'];
    }
  }
  function aircraft_key($id) {
    $sql = "select ident,pilot_id from aircraft where id = $id";
    return lol_fetch_array(lol_query($sql));
  }

  function aircraft_detail($ident,$pilot) {
    if(!isset($pilot)) {
      $pilot=0;
    }
    $sql = "select * from aircraft where ident = '$ident' and pilot_id = $pilot";
    $sqlresponse = lol_query($sql);
    if($buf = lol_fetch_array($sqlresponse)) {
      $detail = $buf;
      $detail['classcode'] = class_lookup($detail['aircraft_class']);
      $detail['classname'] = class_longname($detail['aircraft_class']);
    } else {
      $detail['id'] = 0;
      $detail['ident'] = $ident;
      $detail['makemodel'] = '';
      $detail['aircraft_class'] = '';
      $detail['home_field'] = '';
      $detail['complex'] = '';
      $detail['high_perf'] = '';
      $detail['tailwheel'] = '';
      $detail['detail'] = '';
      $detail['image_url'] = '';
      $detail['link_url'] = '';
      $detail['classcode'] = '????';
      $detail['classname'] = 'Unknown';
    }
    $sql = "SELECT count(id) as flights, max(date) as last_flight, sum(type_cfi+type_sic+type_dual+type_pic-(case when type_dual=type_pic then type_pic else 0 end)) as total_hours from logbook WHERE ident = '$detail[ident]' and pilot_id = $pilot";
    $totals = lol_fetch_array(lol_query($sql));
    return array_merge($detail,$totals);
  }

  function pax_search($search) {
    global $rvar_pilot;

    $buf = '';
    $sql = "select distinct passengers from logbook";
    if(isset($rvar_pilot)) {
      $sql = $sql . " where pilot_id = $rvar_pilot";
    }
    $sqlresponse = lol_query($sql);
    while($line = lol_fetch_array($sqlresponse)) {
      $buf = $buf . " " . $line['passengers'];
    }
    $buf = " " . strtolower($buf) . " ";
    $buf = preg_replace("/ LCL /"," ",$buf);
    $buf=trim($buf);
    $elements = explode(" ",$buf);
    $elements = array_values(array_unique($elements));
    usort($elements,"pax_cmp");
    return $elements;
  }

  function pax_cmp($a,$b) {
    global $rvar_pilot;

    $pilot = 0;
    if(isset($rvar_pilot)) {
      $pilot = $rvar_pilot;
    }

    $vala = pax_flights($a,$pilot);
    $valb = pax_flights($b,$pilot);

    if($vala == $valb) return 0;
    return ($vala > $valb) ? -1 : 1;
  }

  function pax_flights($alias,$pilot) {
    $sql = "select count(id) as flights from logbook where ' '||passengers||' ' like '% $alias %'";
    if($pilot > 0) {
      $sql = $sql . " and pilot_id = $pilot";
    }
    if($line = lol_fetch_array(lol_query($sql))) {
      return $line['flights'];
    }
    return 0;
  }

  function pax_detail($alias,$pilot,$whereclause) {
    $filler['id'] = 0;
    $filler['detail'] = '';
    $filler['image_url'] = '';
    $filler['link_url'] = '';
    $filler['fullname'] = '';
    $filler['alias'] = $alias;
    $filler['pilot_id'] = $pilot;

    $sql = "select * from passengers where alias = '$alias' and pilot_id = $pilot";
    if($sqlresponse=lol_query($sql)) {
      $line = lol_fetch_array($sqlresponse);
    }

    $wherebuf = "where ' '||passengers||' ' like '% $alias %' and pilot_id = $pilot";
    if($whereclause) {
      $wherebuf = $wherebuf . " and " . $whereclause;
    }

    $sql = "SELECT count(id) as flights, sum(landings_day+landings_night) as landings, min(date) as first_flight, max(date) as last_flight, sum(type_cfi+type_sic+type_dual+type_pic-(case when type_dual=type_pic then type_pic else 0 end)) as total_hours from logbook $wherebuf";
    if($sqlresponse=lol_query($sql)) {
      $totals = lol_fetch_array($sqlresponse);
    }

    $segments = 0;
    $sql = "select route from logbook $wherebuf";
    $sqlresponse = lol_query($sql);
    while($segs = lol_fetch_array($sqlresponse)) {
      $hops = preg_split("/ +/",$segs['route'],-1,PREG_SPLIT_NO_EMPTY);
      $segments = $segments + count($hops)-1;
    }
    $line['segments'] = $segments;

    return array_merge($filler,$line,$totals);
  }

  function pax_status($alias,$pilot) {
    $thisyear = date("Y");
    $lastyear = date("Y") - 1;
    $status = "&nbsp;";
        
    $paxalias = "' '||passengers||' ' like '% $alias %' and pilot_id=$pilot";
        
    $sql = "select count(id) as flights, sum(landings_day+landings_night) as segments, sum(type_cfi+type_sic+type_dual+type_pic-(case when type_dual=type_pic then type_pic else 0 end)) as total_hours from logbook WHERE $paxalias and date >= '$thisyear-01-01'";
    $yeartotals = lol_fetch_array(lol_query($sql));
    $sql = "select count(id) as flights, sum(landings_day+landings_night) as segments, sum(type_cfi+type_sic+type_dual+type_pic-(case when type_dual=type_pic then type_pic else 0 end)) as total_hours from logbook WHERE $paxalias and date >= '$lastyear-01-01' and date <= '$lastyear-12-31'";
    $lasttotals = lol_fetch_array(lol_query($sql));
        
    $flights = larger($yeartotals['flights'],$lasttotals['flights']);
    $segments = larger($yeartotals['segments'],$lasttotals['segments']);
    $total_hours = larger($yeartotals['total_hours'],$lasttotals['total_hours']);
                
    for($i=0; $i<count($GLOBALS['statusgoals']); $i++) {
      list($tiername,$tiersegments,$tierhours) = preg_split("/:/",$GLOBALS['statusgoals'][$i]);
      if($segments > $tiersegments) {
        $status = $tiername;
      }
      if($total_hours > $tierhours) {
         $status = $tiername;
      }
    }
    return $status;
  }

  function pax_key($id) {
    $filler['alias'] = '';
    $filler['id'] = 0;
    $sql = "select alias,pilot_id from passengers where id = $id";
    $line = lol_fetch_array(lol_query($sql));
    return array_merge($filler,$line);
  }

?>
