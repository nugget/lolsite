<?php

  // Authentication functions

  function is_user() {
    global $rvar_lol_username, $rvar_lol_passhash;

    if(isset($rvar_lol_username) and isset($rvar_lol_passhash)) {
      $sql = "select id from pilot where username = '$rvar_lol_username' and password = '$rvar_lol_passhash'";
      if($id = mysql_fetch_array(mysql_query($sql))) {
        $sql = "update pilot set last_login = now() where id = " . $id['id'];
        if(mysql_query($sql)) {
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
      if($id = mysql_fetch_array(mysql_query($sql))) {
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
      if($id = mysql_fetch_array(mysql_query($sql))) {
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
      if(mysql_fetch_array(mysql_query($sql))) {
        return TRUE;
      }
    }
    return FALSE;
  }

  function username_exists($username) {
    $sql = "select id from pilot where username = '$username'";
    if($id = mysql_fetch_array(mysql_query($sql))) {
      return $id['id'];
    }
    return FALSE;
  }

  function add_user($username,$password) {
    $sql = "insert into pilot (username,password) values ('$username',md5('$password'))";
    if(mysql_query($sql)) {
      $sql = "select id,admin from pilot where username = '$username' and password = md5('$password')";
      if($id = mysql_fetch_array(mysql_query($sql))) {
        if($id['id'] == 1) {
          mysql_query("update pilot set admin = 1 where id = " . $id['id']);
        }
        return $id['id'];
      }
    }
    return FALSE;
  }


  // Formatting and display functions

  function split_decimal($num) {
    $buf = sprintf("%4.1f",$num);
    print "<td class=\"integer\">";
    for($i=0; $i<strlen($buf); $i++) {
      $c = substr($buf,$i,1);
      if($c == '.') {
        print "</td><td class=\"decimal\">";
      } else {
        print $c;
      }
    }
    print "</td>";
  }

  function hobbs($hours) {

    if(!array_key_exists('hobbsfmt',$GLOBALS)) {
      $GLOBALS['hobbsfmt'] = "%04.1f";
    }

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


  // Data functions

  function pilot_hours($pilot_id) {
    $count = mysql_fetch_row(mysql_query("select sum(type_cfi+type_sic+type_dual+type_pic-(type_pic*(type_dual=type_pic))) from logbook where pilot_id = $pilot_id"));
    $count = $count[0];
    return $count;
  }

  function pilot_name($pilot_id) {
    $sql = "select username, displayname from pilot where id = $pilot_id";
    $pinfo = mysql_fetch_array(mysql_query($sql));
    if(isset($pinfo{'displayname'})) {
      return $pinfo{'displayname'};
    } else {
      return $pinfo{'username'};
    }
  }

  function pilot_search($search) {
    $sql = "select id from pilot where username like '%$search%' or displayname like '%$search%'";
    if($sqlresponse = mysql_query($sql)) {
      $list = array();
      while($line = mysql_fetch_array($sqlresponse)) {
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
    $classtable = array("null","ASEL","ASES","AMES","AMEL","Sim");
    if($class = (int) $class) {
      return $classtable[$class];
    } else {
      for($i=0; $i<count($classtable); $i++) {
        if($classtable[$i] == $class) {
          return $i;
        }
      }
    }
  }

  function class_time($class,$logid) {
    $sql = "select aircraft_class from aircraft, logbook where aircraft.ident = logbook.ident and aircraft.pilot_id = logbook.pilot_id";
    $buf = mysql_fetch_array(mysql_query($sql));
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
    if($line = mysql_fetch_array(mysql_query($sql))) {
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
    if($line = mysql_fetch_array(mysql_query($sql))) {
      return (int) $line['pilot_id'];
    }
  }

  function aircraft_equipment($ident) {
    global $rvar_pilot;
    if(isset($rvar_pilot)) {
      $sql = "select makemodel from aircraft where ident = '$ident' and pilot_id = $rvar_pilot";
      if($line = mysql_fetch_array(mysql_query($sql))) {
        return $line['makemodel'];
      }
    }
  }

  function airport_search($search) {
    $buf = '';
    $sql = "select distinct route from logbook";
    if(isset($rvar_pilot)) {
      $sql = $sql . " where pilot_id = $rvar_pilot";
    }
    $sqlresponse = mysql_query($sql);
    while($line = mysql_fetch_array($sqlresponse)) {
      $buf = $buf . " " . $line['route'];
    }
    $buf = " " . strtoupper($buf) . " ";
    $buf = preg_replace("/ LCL /"," ",$buf);
    $buf=trim($buf);
    $elements = explode(" ",$buf);
    $elements = array_values(array_unique($elements));
    usort($elements,"airport_cmp");
    return $elements;
  }

  function airport_name($ident) {
    $ident = strtoupper($ident);
    $sql = "select ident, fullname from airports where ident = '$ident' or ident = 'K$ident'";
    if($line = mysql_fetch_array(mysql_query($sql))) {
      if(isset($line['fullname'])) {
        return $line['fullname'] . " (" . $line['visits'] . ")";
      } else {
        return $line['ident'];
      }
    } else {
      return $ident;
    }
  }

  function airport_visits($ident,$pilot) {
    $sql = "select count(id) as visits from logbook where concat(' ',route,' ') like '% $ident %' or concat(' ',route,' ') like '% k$ident %'";
    if($line = mysql_fetch_array(mysql_query($sql))) {
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

?>
