<?php
  $title = "main page";
  $cvs="\$Id$";
  $keywords="logbook online";

  include "init.inc";
  include "head.inc";

?>

  <h2>Testing</h2>

  <p> is_user() = <?php print is_user(); ?></p>
  <p> is_admin() = <?php print is_admin(); ?></p>
  <p> is_mine() = <?php print is_mine(); ?></p>

<?php

  include "foot.inc";

?>
