<?php
  $title = "user login";
  $cvs="\$Id$";
  $keywords="logbook online";

  include "include/init.inc";

  if(strlen($rvar_lol_username)>0 and strlen($rvar_lol_password)>0) {
    if(isset($rvar_register)) {
      if(array_key_exists('phpbb_database',$GLOBALS)) {
        if(list($phpbb_host,$phpbb_port,$phpbb_user,$phpbb_password) = split(":",$GLOBALS['phpbb_database'])) {
          if($phph = @mysql_connect("$phpbb_host:$phpbb_port",$phpbb_user,$phpbb_passwd)) {
            // Here goes the phpbb stuff.
          }
        }
      } else {
        if(!username_exists($rvar_lol_username)) {
          if($id = add_user($rvar_lol_username,$rvar_lol_password)) {
            $rvar_login = "Login";
          } else {
            $notice_title = "Unable to create new user";
            $notice_text = "Unable to create a new user account.  Sorry.";
          }
        }
      }
    }
    if(isset($rvar_login)) {
      $rvar_lol_passhash=md5($rvar_lol_password);
      if(is_user()) {
        setcookie("lol_username",$rvar_lol_username,time()+86400);
        setcookie("lol_passhash",$rvar_lol_passhash,time()+86400);
        header("Location: " . $GLOBALS['baseurl'] . "/");
      }

      $notice_title = "Login Incorrect";
      $notice_text  = "The login credentials you supplied were not valid.
                       Please enter a valid username and password.";
    }
  }

  include "include/head.inc";

?>

  <div class="content">

    <h2>Login/Register</h2>

    <form action="login.php" method="get">
      <table class="tightbox">
        <tr>
          <th>Login:</th>
          <td><input type="text" name="lol_username" size="20" /></td>
        </tr>
        <tr>
          <th>Password:</th>
          <td><input type="password" name="lol_password" size="20" /></td>
        </tr>
        <tr>
         <th colspan="2">
           <input name="login" type="submit" value="Login" />
           <input name="register" type="submit" value="Register" />
         </th>
        </tr>
        <tr>
         <td colspan="2">
           Existing users should enter their username and password and select "Login".
           <br />
           New users should enter their
           <strong><?php if(array_key_exists('phpbb_database',$GLOBALS)) { print "phpBB"; } else { print "desired"; } ?></strong>
           username and password and select "Register".
         </td>
        </tr>
      </table>
    </form>

  </div>

<?php

  include "include/foot.inc";

?>
