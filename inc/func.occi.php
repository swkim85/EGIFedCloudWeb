<?php

class SiteCache {
  var $tbl = 'sitecache';
  var $debug = false;
  var $delim = '';
  var $vtime = 0;

  function SiteCache() {
  }


  function set_debug($debug, $delim="") {
    if ($debug) $this->debug = true;
    $this->delim = $delim;
  }
  function _debug($msg) {
    $delim = $this->delim;
    if ($this->debug) print("SiteCache debug: $msg$delim");
  }

  // cache valid time in seconds
  function set_valid_time($vtime) {
    $this->vtime = $vtime;
  }

  function Update_OS_Template($siteid, $lists) {

    $this->_debug("Update_OS_Template....");

    $ctype = 'OS';
    $qry = "DELETE FROM {$this->tbl} WHERE siteid='$siteid' AND ctype='$ctype'";
    $this->_debug($qry);
    $ret = myQuery($qry);

    foreach ($lists as $uri) {
      # eg. http://occi.cloud.cesga.es/occi/infrastructure/os_tpl#uuid_debian6_88
      #     ----------------------------------------------------- ---------------
      list($prefix, $uuid) = preg_split("/#/", $uri);

      //$flag = false;
      //if (preg_match("/os_tpl/", $prefix)) $flag = true;
      //if (preg_match("/template.os/", $prefix)) $flag = true;
      //if (!$flag) continue;

      $s = array();
      $s[] = "siteid='$siteid'";
      $s[] = "uri='$uri'";
      $s[] = "hash='$hash'";
      $s[] = "ctype='$ctype'";
      $s[] = "idate=now()";
      $s[] = "udate=now()";
      $sql_set = " SET ".join(",",$s);

      $qry = "INSERT INTO {$this->tbl} $sql_set";
      $ret = myQuery($qry);
      $err = myError(); if ($err) print $qry.$err;
    }

  }

  function Update_Resource_Template($siteid, $lists) {

    $this->_debug("Update_Resource_Template....");

    $ctype = 'RES';
    $qry = "DELETE FROM {$this->tbl} WHERE siteid='$siteid' AND ctype='$ctype'";
    $this->_debug($qry);
    $ret = myQuery($qry);

    foreach ($lists as $uri) {
      list($prefix, $uuid) = preg_split("/#/", $uri);

      //$flag = false;
      //if (preg_match("/os_tpl/", $prefix)) $flag = true;
      //if (preg_match("/template.os/", $prefix)) $flag = true;
      //if (!$flag) continue;

      $s = array();
      $s[] = "siteid='$siteid'";
      $s[] = "uri='$uri'";
      $s[] = "hash='$hash'";
      $s[] = "ctype='$ctype'";
      $s[] = "idate=now()";
      $s[] = "udate=now()";
      $sql_set = " SET ".join(",",$s);

      $qry = "INSERT INTO {$this->tbl} $sql_set";
      $ret = myQuery($qry);
      $err = myError(); if ($err) print $qry.$err;
    }

  }


  function Dump($siteid) {
    $qry = "SELECT * FROM {$this->tbl} WHERE siteid='$siteid'";
    $ret = myQuery($qry);
    while ($row = myFetchRow($ret)) {
      dd($row);
    }
  }

  function List_OS_Template($siteid) {
    $ctype = 'OS';

    $w = array();
    $w[] = "siteid='$siteid'";
    $w[] = "ctype='$ctype'";

    $vtime = $this->vtime;
    if ($vtime > 0) $w[] = "idate > date_sub(now(), interval $vtime SECOND)";

    $sql_where = " WHERE ".join(" AND ", $w);

    $qry = "SELECT * FROM {$this->tbl} $sql_where";
    $this->_debug($qry);

    $ret = myQuery($qry);

    $lists = array();
    while ($row = myFetchRow($ret)) {
      //dd($row);
      $uri = $row['uri'];
      $lists[] = $uri;
    }

    return $lists;
  }

  function List_Resource_Template($siteid) {
    $ctype = 'RES';

    $w = array();
    $w[] = "siteid='$siteid'";
    $w[] = "ctype='$ctype'";

    $vtime = $this->vtime;
    if ($vtime > 0) $w[] = "idate > date_sub(now(), interval $vtime SECOND)";

    $sql_where = " WHERE ".join(" AND ", $w);

    $qry = "SELECT * FROM {$this->tbl} $sql_where";
    $this->_debug($qry);

    $ret = myQuery($qry);

    $lists = array();
    while ($row = myFetchRow($ret)) {
      //dd($row);
      $uri = $row['uri'];
      $lists[] = $uri;
    }

    return $lists;
  }




}

class OCCI_Client {

  var $proxypath = '';
  var $occi_url = '';
  var $debug = false;
  var $delim = '';

  function OCCI_Client() {
  }

  function set_debug($debug, $delim="") {
    if ($debug) $this->debug = true;
    $this->delim = $delim;
  }
  function _debug($msg) {
    $delim = $this->delim;
    if ($this->debug) print("OCCI debug: $msg$delim");
  }

  function Set_Proxy_Path($proxy_path) {
    $this->proxypath = $proxy_path;
  }

  function Set_OCCI_Address($occi_address) {
    $this->occi_url = $occi_address;
  }

  function _command_base() {
    $occi_path = '/usr/local/bin/occi';
    $command = "$occi_path --endpoint {$this->occi_url} --auth x509 --voms --user-cred {$this->proxypath}";
    return $command;
  }

  function _error($msg) {
    die("OCCI Error: $msg");
  }

  function List_OS_Template() {

    $cmd = $this->_command_base();
    $command = "$cmd --action list --resource os_tpl";
    $this->_debug($command);

    $ret = exec($command, $output, $retval);

    if ($retval) $this->_error($command);

    $list = $output;
    return $list;
  }

  function List_Resource_Template() {

    $cmd = $this->_command_base();
    $command = "$cmd --action list --resource resource_tpl";
    $this->_debug($command);

    $ret = exec($command, $output, $retval);

    if ($retval) $this->_error($command);

    $list = $output;
    return $list;

  }

  function Describe_OS_Template($osid) {

    $cmd = $this->_command_base();
    $command = "$cmd --action describe -r os_tpl#$osid"
          ." --output-format json";
    $this->_debug($command);

    $ret = exec($command, $output, $retval);

    if ($retval) $this->_error($command);

    $json = $output[0];
    $info = json_decode($json, true)[0];

    return $info;
  }


  function Describe_Resource_Template($resid) {

    $cmd = $this->_command_base();
    $command = "$cmd --action describe -r resource_tpl#$resid"
          ." --output-format json";
    $this->_debug($command);

    $ret = exec($command, $output, $retval);

    if ($retval) $this->_error($command);

    $json = $output[0];
    $info = json_decode($json, true)[0];

    return $info;

  }

  function Create_VM_Instance($name, $osid, $resid) {

    $cmd = $this->_command_base();
    $command = "$cmd --action create --resource compute"
        ." --attribute occi.core.title=\"$name\" "
        ." --mixin os_tpl#$osid"
        ." --mixin resource_tpl#$resid"
        ." --context user_data=\"file:///tmp/tmpfedcloud.login\" ";

    $ret = exec($command, $output, $retval);
    if ($retval) $this->_error($command);

    $vmurl = $output[0];

    return $vmurl;

  }

  function Describe_VM_One($vmurl) {

    $cmd = $this->_command_base();
    $command = "$cmd --action describe -r $vmurl"
        ." --output-format json";

    $ret = exec($command, $output, $retval);

    if ($retval) return false; // resource not found 

    $json = $output[0];
    $info = json_decode($json, true);

    return $info;
  }

  function Delete_VM($vmurl) {
    $cmd = $this->_command_base();
    $command = "$cmd --action delete -r $vmurl"
        ." --output-format json";

    $ret = exec($command, $output, $retval);

    if ($retval) return false; // resource not found 

    return true; // success
  }

}



?>
