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


?>
