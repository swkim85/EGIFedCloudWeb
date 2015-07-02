<?php

  include("$env[prefix]/config/config.php");
  include("$env[prefix]/inc/func.php");

  include("$env[prefix]/inc/common.base.php");


  // check login
  $username = $_SESSION['username'];
  if (!$username) {
    Redirect("/login.php");
    exit;
  }

  $env['self'] = $_SERVER['SCRIPT_NAME'];


?>
