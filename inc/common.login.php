<?php

  include("$env[prefix]/config/config.php");
  include("$env[prefix]/inc/func.php");

  // 로그인 했는지 검사하지 않는다.

  include("$env[prefix]/inc/common.base.php");

  $env['self'] = $_SERVER['SCRIPT_NAME'];


?>
