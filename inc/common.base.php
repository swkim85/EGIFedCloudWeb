<?php

  session_start();

  error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

  $mysqli = new mysqli();
  if (!$mysqli) die('mysqli_init failed');

  if (!$mysqli->options(MYSQLI_INIT_COMMAND, 'SET AUTOCOMMIT = 1')) {
    die('Setting MYSQLI_INIT_COMMAND failed');
  }

  $mysqli->options(MYSQLI_INIT_COMMAND, "SET NAMES 'utf8'");

  if (!$mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5)) {
    die('Setting MYSQLI_OPT_CONNECT_TIMEOUT failed');
  }

  if (!$mysqli->real_connect($conf['dbhost'], $conf['dbuser'], $conf['dbpasswd'], $conf['dbname'], $conf['dbport'], $conf['dbpath'])) {
    die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
  }

  //print_r($mysqli->host_info);

  # GET/POST --> form
  $form = $_REQUEST;
  $mode = $form['mode'];

?>
