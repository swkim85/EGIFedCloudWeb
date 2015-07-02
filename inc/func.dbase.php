<?php

function myError() {
  global $mysqli;
  return $mysqli->error;
}
function myWarnings() {
  global $mysqli;
  return $mysqli->get_warnings();
}


function myQuery($query) {
  global $mysqli;
  $result = $mysqli->query($query);
  return $result;
}

function myAffectedRows() {
  global $mysqli;
  return $mysqli->affected_rows;
}

function myFetchRow($result) {
  $row = $result->fetch_assoc();
  return $row;
}

function myQueryAndFetchRow($query) {
  global $mysqli;

  $result = $mysqli->query($query);
  if (!$result) return null;
  $row = $result->fetch_assoc();
  return $row;
}

function myEscapeString($str) {
  global $mysqli;
  return $mysqli->real_escape_string($str);
}


?>
