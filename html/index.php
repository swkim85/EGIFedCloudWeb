<?php

  unset($env);
  $env['prefix'] = "/www/fedcloud-web";
  include("$env[prefix]/inc/common.login.php");

  $qry = "show databases";
  $ret = $mysqli->query($qry);
  $row = $ret->fetch_assoc();
  print_r($row);

  //print_r($_SESSION);
  $user = $_SESSION['user'];
  if (!$user) {
    Redirect("login.php");
    exit;
  }

?>
