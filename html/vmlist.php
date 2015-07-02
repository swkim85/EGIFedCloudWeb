<?php

  unset($env);
  $env['prefix'] = "/www/fedcloud-web";
  include("$env[prefix]/inc/common.php");
  include("func.php");

  $pgtitle = 'VM Management';

  $occi = new OCCI_Client();
  $occi->set_debug(true,"<br>");

  $proxypath = proxy_path();
  $occi->Set_Proxy_Path($proxypath);

### {{{

function _toggle_part_start() {
  global $form;
  $sfet = $form['sfet'];
  if ($sfet == '1') { $str = 'Close'; $sty = 'display:block'; }
  else { $str = 'View'; $sty = 'display:none'; }
  print<<<EOS
<div>
  <span class='link' style="display:block; background-color:#eeeeee; text-align:center; margin:5 5 5 5px; font-size:11pt; padding:4 4 4 4px;"
 onclick="_nav_toggle(this)">$str</span>
</div>

<div id="collapse1" style="$sty">
EOS;
}

function _toggle_part_end() {
  print<<<EOS
</div>

<script>
function _nav_toggle(obj) {
  $('#collapse1').toggle(function(){
    if ($(this).css('display')=='none'){
      obj.innerHTML = "Open";
      //document.search_form.sfet.value = '0';
    } else {
      obj.innerHTML = "Close";
      //document.search_form.sfet.value = '1';
    }
  });
}
</script>
EOS;
}

function _query_vm($id) {
  $qry = "SELECT M.*, S.*, S.id siteid
 FROM vminfo M
 LEFT JOIN sites S ON M.siteid=S.id
 WHERE M.id='$id'";

  $ret = myQuery($qry);
  $row = myFetchRow($ret);
  return $row;
}

function _update_vm_status($id, $stat) {
  $qry = "UPDATE vminfo SET state='$stat' WHERE id='$id'";
  $ret = myQuery($qry);
}

### }}}

### {{{

if ($mode == 'do_change_name') {
  $id = $form['id'];
  $vmname = $form['vmname'];

  $memo = $form['memo'];
  $memo = myEscapeString($memo);

  $qry = "UPDATE vminfo SET vmname='$vmname', memo='$memo' where id='$id'"; 
  $ret = myQuery($qry);
  print myError();

  print<<<EOS
<script>
window.opener.document.location.reload();
window.close();
</script>
EOS;
  exit;
}

if ($mode == 'change_name') {
  $id = $form['id'];
  $row = _query_vm($id);
  //dd($row);

  pagehead($pgtitle, true);
  ParagraphTitle("VM Information");
  ParagraphTitle("Change Name", 1);

  $memo = $row['memo'];
  print<<<EOS
<form name='form'>
<p>
Name: <input type='text' name='vmname' value="{$row['vmname']}" size='60'>
<p>
memo: <textarea name='memo' cols='60' rows='10'>$memo</textarea>

<input type='hidden' name='id' value='$id'>
<input type='hidden' name='mode' value='do_change_name'>
<input type='submit' value='OK'>
</form>
EOS;

  pagetail();
  exit;
}

if ($mode == 'vm_delete') {
  $id = $form['id'];
  $row = _query_vm($id);

  pagehead($pgtitle);
  ParagraphTitle("VM Information");

  //dd($row);
  $occi_url = $row['ep_occi'];
  $occi->Set_OCCI_Address($occi_url);

  $vmurl = $row['vmurl'];
  $ret = $info = $occi->Delete_VM($vmurl);
  //dd($info);

  if ($ret) {
    print("delete success");
    _update_vm_status($id, 'deleted');
  } else {
    print("resource not found");
    _update_vm_status($id, 'not found');
  }

  pagetail();
  exit;
}

if ($mode == 'vm_detail') {
  $id = $form['id'];

  $row = _query_vm($id);
  //dd($row);

  pagehead($pgtitle, true);
  ParagraphTitle("VM Information");
  ParagraphTitle("Name: {$row['vmname']}",1);

  $occi_url = $row['ep_occi'];
  $occi->Set_OCCI_Address($occi_url);
  //dd($occi);

  $vmurl = $row['vmurl'];
  $info = $occi->Describe_VM_One($vmurl);
  //dd($info);
  if (!$info) { 
    print<<<EOS
VM not found
EOS;
    _update_vm_status($id, 'not found');
  } else {

  $vm = $info[0];

  $template =<<<EOS
<dt>ID</dt><dd>{id}</dd>
<dt>OS</dt><dd>{os}</dd>
<dt>Resource</dt><dd>{resource}</dd>
<dt>Title</dt><dd>{title}</dd>
<dt>Cores</dt><dd>{cores}</dd>
<dt>Memory(GB)</dt><dd>{memory}</dd>
<dt>State</dt><dd>{state}</dd>
<dt>IP</dt><dd>{ip}</dd>
EOS;

  $page = new Template();
  $page->loadstr($template);
  $page->set("id",     $vm['id']);
  $page->set("os", $vm['mixins'][1]);
  $page->set("resource", $vm['mixins'][2]);
  $page->set("title",  $vm['attributes']['occi']['core']['title']);

  // update vminfo table
  $core = $vm['attributes']['occi']['compute']['cores'];
  $memory = $vm['attributes']['occi']['compute']['memory'];
  $state = $vm['attributes']['occi']['compute']['state'];

  $v1 = $vm['links'][0]['attributes']['occi']['networkinterface']['address'];
  $v2 = $vm['links'][1]['attributes']['occi']['networkinterface']['address'];
  $v3 = $vm['attributes']['occi']['compute']['hostname'];
  $ip = get_valid_values($v1, $v2, $v3);

  $qry = "UPDATE vminfo set core='$core',memory='$memory',state='$state',ip='$ip' where id='$id'";
  $ret = myQuery($qry);

  $page->set("cores",   $core);
  $page->set("memory",  $memory);
  $page->set("state",   $state);
  $page->set("ip",      $ip);

  $page->publish();

  ParagraphTitle("SSH", 1);
  print<<<EOS
ssh -i /tmp/tmpfedcloud   cloudadm@$ip
EOS;


  ParagraphTitle("More detailed VM Information", 1);

  _toggle_part_start(); // toggle div start

  print("<pre>");
  print_r($vm);
  print("</pre>");

  _toggle_part_end(); // toggle div end

  }


  pagetail();
  exit;
}
### }}}



  pagehead($pgtitle);
  ParagraphTitle($pgtitle);

  $qry = "SELECT M.*, S.*, M.id as id
 FROM vminfo M
 LEFT JOIN sites S ON M.siteid=S.id
 ORDER BY M.createTime DESC";
  $ret = myQuery($qry);

  print<<<EOS
<table border='1' class='mmdata'>
<tr>
<th>#</th>
<th>Site</th>
<th>CC</th>
<!--
<th>Host Site</th>
<th>OS</th>
<th>VM Address</th>
-->
<th>Name</th>
<th>CMF</th>
<th>Resource</th>
<th>core</th>
<th>memory</th>
<th>state</th>
<th>ip</th>
<th>Create</th>
<th>Show</th>
<th>Delete</th>
</tr>
EOS;

  $cnt = 0;
  while ($row = myFetchRow($ret)) {

    //dd($row);
    $id = $row['id'];

    $alive = true;

    $state = $row['state'];
    if ($state == 'deleted'
       or $state == 'not found'
       //or $state == 'inactive'
       ) $alive = false;

    if ($alive) {
      $delete = "<span onclick=\"_delete('$id',this)\" class='link'>delete</span>";
    } else {
      $show = '';
      $delete = '';
    }

    $show = "<span onclick=\"_show('$id',this)\" class='link'>show</span>";

    //dd($row['createTime']);
    $ct = $row['createTime'];
    $ct = getHumanTime($ct).' ago';

    $cnt++;
    print<<<EOS
<tr>
<td>{$cnt}</td>
<td>{$row['affiliation']}</td>
<td>{$row['cc']}</td>
<!--
<td>{$row['hostsite']}</td>
<td>{$row['osid']}</td>
<td>{$row['vmurl']}</td>
-->
<td><span onclick="_chname('$id',this)" class='link'>{$row['vmname']}</span></td>
<td>{$row['cmf']}</td>
<td>{$row['resid']}</td>
<td>{$row['core']}</td>
<td>{$row['memory']}</td>
<td>{$row['state']}</td>
<td>{$row['ip']}</td>
<td>{$ct}</td>
<td>$show</td>
<td>$delete</td>
</tr>
EOS;
  }
  print<<<EOS
</table>

<script>
function _show(id, span) {
  span.style.backgroundColor = '#f0f000'
  var url = "$env[self]?mode=vm_detail&id="+id;
  wopen(url, 800,600,1,1);
}
function _delete(id, span) {
  if (!confirm("Really delete VM?")) return;
  span.style.backgroundColor = '#f0f000'
  var url = "$env[self]?mode=vm_delete&id="+id;
  wopen(url, 800,300,1,1);
}
function _chname(id, span) {
  span.style.backgroundColor = '#f0f000'
  var url = "$env[self]?mode=change_name&id="+id;
  wopen(url, 600,500,1,1);
}
</script>
EOS;

  pagetail();
  exit;

?>
