<?php

  unset($env);
  $env['prefix'] = "/www/fedcloud-web";
  include("$env[prefix]/inc/common.php");
  include("func.php");

  $debug = true;
  $debug = false;

  $pgtitle = "Create a VM";

  $occi = new OCCI_Client();
  //$occi->set_debug(true,"<br>");

  $proxypath = proxy_path();
  $occi->Set_Proxy_Path($proxypath);


### {{{
function fetch_site($id) {
  $qry = "SELECT * FROM sites WHERE id='$id'";
  $ret = myQuery($qry);
  $row = myFetchRow($ret);
  return $row;
}

function _insert_vminfo($siteid, $osid, $resid, $name, $vmurl, $title) {
  $qry = "INSERT INTO vminfo"
     ." SET siteid='$siteid', osid='$osid', resid='$resid', vmname='$name', vmurl='$vmurl', title='$title', createTime=NOW()";
  //dd($qry);
  $ret = myQuery($qry);
  print myError();
}

function site_title($row) {
  $af = $row['affiliation'];
  $cc = $row['cc'];
  $hs = $row['hostsite'];
  $cm = $row['cmf'];
  $t = "[$cc] ($cm) $af - $hs";
  return $t;
}

function option_resources($preset='') {
  $qry = "SELECT * FROM sites WHERE ep_occi != '' ORDER BY cmf";
  $ret = myQuery($qry);
  $opts = "<option>::select a site::</option>";
  while ($row = myFetchRow($ret)) {
    //dd($row);
    $v = $row['id'];
    $t = site_title($row);
    if ($preset == $v) $sel = " selected"; else $sel = '';
    $opts .= "<option value='$v'$sel>$t</option>";
  }
  return $opts;
}


### }}}


### {{{
if ($mode == 'os_detail') {

  pagehead($pgtitle, true);
  ParagraphTitle($pgtitle);

  $siteid = $form['siteid'];
  $row = fetch_site($siteid);
  $occi_url = $row['ep_occi'];
  $occi->Set_OCCI_Address($occi_url);

  $ostpl = $form['ostpl'];
  list($prefix, $osid) = preg_split("/#/", $ostpl);

  $info = $occi->Describe_OS_Template($osid);
  //dd($info);

  $template =<<<EOS
<dt>title</dt>
<dd>{title}</dd>
<dt>term</dt>
<dd>{term}</dd>
<dt>location</dt>
<dd>{location}</dd>
<dt>scheme</dt>
<dd>{scheme}</dd>
<dt>depends</dt>
<dd>{depends}</dd>
<dt>related</dt>
<dd>{related}</dd>
EOS;

  $page = new Template();
  $page->loadstr($template);
  $page->set("title", $info['title']);
  $page->set("term", $info['term']);
  $page->set("location", $info['location']);
  $page->set("scheme", $info['scheme']);
  $page->set("related", $info['related'][0]);
  $page->set("depends", $info['depends'][0]);
  print $page->parse();

  pagetail();
  exit;
}

if ($mode == 'resource_detail') {

  $title = "Resource Template";
  pagehead($title, true);
  ParagraphTitle($title);

  $siteid = $form['siteid'];
  $row = fetch_site($siteid);
  $occi_url = $row['ep_occi'];
  $occi->Set_OCCI_Address($occi_url);

  $restpl = $form['restpl'];
  list($prefix, $resid) = preg_split("/#/", $restpl);

  $info = $occi->Describe_Resource_Template($resid);
  //dd($info);

  $template =<<<EOS
<dt>title</dt>
<dd>{title}</dd>
<dt>term</dt>
<dd>{term}</dd>
<dt>location</dt>
<dd>{location}</dd>
<dt>scheme</dt>
<dd>{scheme}</dd>
<dt>depends</dt>
<dd>{depends}</dd>
<dt>related</dt>
<dd>{related}</dd>
EOS;

  $page = new Template();
  $page->loadstr($template);
  $page->set("title", $info['title']);
  $page->set("term", $info['term']);
  $page->set("location", $info['location']);
  $page->set("scheme", $info['scheme']);
  $page->set("related", $info['related'][0]);
  $page->set("depends", $info['depends'][0]);
  print $page->parse();

  dd($info);

  pagetail();
  exit;
}


if ($mode == 'launch') {
  pagehead($pgtitle, true);
  ParagraphTitle($pgtitle);


  $siteid = $form['siteid'];
  $row = fetch_site($siteid);
  //dd($form);
  //dd($row);

  $occi_url = $row['ep_occi'];
  $occi->Set_OCCI_Address($occi_url);


  $vmname = $form['vmname'];
  $ostpl = urldecode($form['ostpl']);
  $restpl = urldecode($form['restpl']);

  list($prefix, $osid) = preg_split("/#/", $ostpl);
  list($prefix, $resid) = preg_split("/#/", $restpl);

  $proxypath = proxy_path();

  $vmurl = $occi->Create_VM_Instance($vmname, $osid, $resid);

  _insert_vminfo($siteid, $osid, $resid, $vmname, $vmurl, $title);

  print<<<EOS
$vmurl
EOS;

  pagetail();
  exit;
}
### }}}


  pagehead($pgtitle);
  ParagraphTitle($pgtitle);
 
  $preset = $form['siteid'];
  $opts = option_resources($preset);

  $s1 = <<<EOS
<label for='siteid'>Select a site</label>
EOS;
  $s2 = <<<EOS
<select name='siteid' id='siteid'>$opts</select>
EOS;
  $s3 = <<<EOS
<input type='button' value='OK' onclick="sf_1()" id='select_site_ok'>
<input type='hidden' name='mode' value='doquery'>
EOS;
  print("<form name='form' method='get'>");
  print button_box($s1,$s2,$s3);
  print("</form>");

  print<<<EOS
<script>
function sf_1() {
  document.form.submit();
}

$(function() {
  $( "#siteid" ).selectmenu();

  $("#select_site_ok")
    .button()
    .click(function(event) {
      event.preventDefault();
    });
});
</script>
EOS;


if ($mode != 'doquery') {
  pagetail();
  exit;
}



### {{{

  //dd($form);
  $siteid = $form['siteid'];
  $row = fetch_site($siteid);
  //dd($row);

  ParagraphTitle("Site Detail");

  $title = site_title($row);
  ParagraphTitle("Site: $title", 1);
  print<<<EOS
<ul>
<li>Affiliation: {$row['affiliation']}
<li>CC: {$row['cc']}
<li>Contact: {$row['contact']}
<li>Deputies: {$row['deputies']}
<li>Host Site: {$row['hostsite']}
<li>CMF: {$row['cmf']}
<li>OCCI: {$row['ep_occi']}
<li>CDMI: {$row['ep_cdmi']}
<li>Size: {$row['res_size']}
<li>Max VM: {$row['vm_max_size']}
</ul>
EOS;

  // cache operation
  $sitec = new SiteCache();
  //$sitec->set_debug(true, "<br>");
  $vtime = 3600; // 1 hour
  $sitec->set_valid_time($vtime);

  print<<<EOS
<form name='form2' action='$env[self]'>
EOS;

  $occi_url = $row['ep_occi'];
  $occi->Set_OCCI_Address($occi_url);
  //dd($occi);

  // check cache first
  $output = $sitec->List_OS_Template($siteid);

  if ($output) {
    $from_cache = true;

  } else {
    $from_cache = false;

    // occi query (it takes some time)
    $output = $occi->List_OS_Template();
  }

  if (!$from_cache) {
    // update the cache
    $sitec->Update_OS_Template($siteid, $output);
    //$sitec->Dump($siteid);
  }


  ParagraphTitle("OS Templates", 1);

  $opts = "<option value=''>Select an OS template</option>";
  foreach ($output as $uri) {
    list($prefix, $hash) = preg_split("/#/", $uri);
    $val = urlencode($uri);
    $opts .=<<<EOS
<option value='$val' hash='$hash'>$uri</option>
EOS;
  }

  $s1 = "<select name='ostpl' id='ostpl' onchange='_fill_title()'>$opts</select>";
  $s2 = "<input type='button' value='Show' onclick='_osview()' id='os_detail_btn'>";
  print button_box($s1,$s2);


  // check cache first
  $output = $sitec->List_Resource_Template($siteid);

  if ($output) {
  } else {
    // occi query (it takes some time)
    $output = $occi->List_Resource_Template();

    // update the cache
    $sitec->Update_Resource_Template($siteid, $output);
    //$sitec->Dump($siteid);
  }


  ParagraphTitle("Resource Templates", 1);

  $opts = "<option value=''>Select a resource template</option>";
  foreach ($output as $uri) {

    list($prefix, $hash) = preg_split("/#/", $uri);
    $val = urlencode($uri);

    $opts .=<<<EOS
<option value='$val' hash='$hash'>$uri</option>
EOS;
  }

  $s1 = "<select name='restpl' id='restpl' onchange='_fill_title()'>$opts</select>";
  $s2 = "<input type='button' value='Show' onclick='_resview()' id='res_detail_btn'>";
  print button_box($s1,$s2);

  $username = get_username();
  $now = date("YmdHis");
  $vmname = "{$username}_{$now}";
  print<<<EOS
<p>
VM Name: <input type='text' name='vmname' value='$vmname' size='60'>

<p>
Title: <input type='text' name='title' value='' size='60'>

<p>
<input type='hidden' name='siteid' value='$siteid'>
<input type='hidden' name='mode' value='launch'>
<input type='button' value='Launch VM' onclick='_create()' id='create_btn' style='width:200; height:50;'>
</form>

<script>
function _osview() {
  var form = document.form2;
  var sel = form.ostpl;
  var idx = sel.selectedIndex;
  var ostpl = sel.options[idx].value;
  if (ostpl == '') { alert('Select an OS template'); return; }
  var url = "$env[self]?mode=os_detail&siteid=$siteid&ostpl="+ostpl;
  wopen(url, 500,500,1,1);
}

function _resview() {
  var form = document.form2;
  var sel = form.restpl;
  var idx = sel.selectedIndex;
  var restpl = sel.options[idx].value;
  if (restpl == '') { alert('Select a resource template'); return; }
  var url = "$env[self]?mode=resource_detail&siteid=$siteid&restpl="+restpl;
  wopen(url, 500,500,1,1);
}

function _get_site_title() {
  var form = document.form;
  var sel = form.siteid;
  var idx = sel.selectedIndex;
  var title = sel.options[idx].text;
  return title;
}

function _get_os_hash() {
  var form = document.form2;
  var sel = form.ostpl;
  var idx = sel.selectedIndex;
  var hash = sel.options[idx].getAttribute("hash");
  return hash;
}

function _get_res_hash() {
  var form = document.form2;
  var sel = form.restpl;
  var idx = sel.selectedIndex;
  var hash = sel.options[idx].getAttribute("hash");
  return hash;
}

function _fill_title() {
  var form = document.form2;
  var title = _get_site_title();
  var os = _get_os_hash();
  var res = _get_res_hash();
  form.title.value = title + "_" + os + "_" + res;
}

function _wopen(url, name, width, height, scrollbars, resizable) {
  option = "width="+width
          +",height="+height
          +",scrollbars="+scrollbars
          +",resizable="+resizable;
  return window.open(url, name, option);
}

$(function() {
  $("#ostpl").selectmenu()
    .on("selectmenuchange", function(event, ui) { _fill_title(); });

  $("#restpl").selectmenu()
  .on("selectmenuchange", function(event, ui) { _fill_title(); });

  $("#os_detail_btn").button()
    .click(function(event) { event.preventDefault(); });

  $("#res_detail_btn").button()
    .click(function(event) { event.preventDefault(); });

  $("#create_btn").button();

});

function _create() {
  var form = document.form2;
  if (form.ostpl.selectedIndex == 0) {
    alert("Select a OS template!!"); return;
  }
  if (form.restpl.selectedIndex == 0) {
    alert("Select a resource template!!"); return;
  }
  if (form.name.value == '') {
    alert('Please input a name!!'); form.name.focus(); return;
  }

  _wopen("about:", 'create', 500,500,1,1);
  form.target = 'create'

  form.mode.value = 'launch';
  form.submit();
}
</script>
EOS;

### }}}

  pagetail();
  exit;

?>
