<?php

  unset($env);
  $env['prefix'] = "/www/fedcloud-web";
  include("$env[prefix]/inc/common.php");
  include("func.php");

  pagehead('Home');

  print<<<EOS
EOS;

  pagetail();


?>
