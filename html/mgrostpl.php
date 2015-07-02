<?php

  include("path.php");
  include("$env[prefix]/inc/common.php");
  include("func.php");

  $pgtitle = 'Resource Management';

### {{{
if ($mode == 'dodelete') {
  $id = $form['id'];
  $qry = "DELETE FROM resources WHERE id='$id'";
  $ret = myQuery($qry);
  CloseAndReloadOpenerWindow();
  exit;
}

if ($mode == 'dosave') {
  $id = $form['id'];

  //dd($form);

  $s = array();
  $k = 'affiliation'; $v = $form[$k]; $s[] = "$k='$v'";
  $k = 'cc';          $v = $form[$k]; $s[] = "$k='$v'";
  $k = 'contact';     $v = $form[$k]; $s[] = "$k='$v'";
  $k = 'deputies';    $v = $form[$k]; $s[] = "$k='$v'";
  $k = 'hostsite';    $v = $form[$k]; $s[] = "$k='$v'";
  $k = 'cmf';         $v = $form[$k]; $s[] = "$k='$v'";
  $k = 'ep_occi';     $v = $form[$k]; $s[] = "$k='$v'";
  $k = 'ep_cdmi';     $v = $form[$k]; $s[] = "$k='$v'";
  $k = 'res_size';    $v = $form[$k]; $s[] = "$k='$v'";
  $k = 'vm_max_size'; $v = $form[$k]; $s[] = "$k='$v'";

  $sql_set = " SET ".join(",", $s);
  //dd($s);

  $qry = "UPDATE resources $sql_set WHERE id='$id'";
  $ret = myQuery($qry);
  CloseAndReloadOpenerWindow();
  exit;
}

if ($mode == 'edit') {
  $id = $form['id'];

  $qry = "SELECT * FROM resources WHERE id='$id'";
  $ret = myQuery($qry);
  $row = myFetchRow($ret);

  pagehead($pgtitle);
  ParagraphTitle($pgtitle);

  $width = '100%';

  //dd($row);
  print<<<EOS
<table border='1' class='mmdata' width='500'>
<form name='form' method='post'>
EOS;

  print<<<EOS
<tr>
<th>Affiliation</th>
<td><input type='text' name='affiliation' value='{$row['affiliation']}' style='width:$width'></td>
</tr>
EOS;

  print<<<EOS
<tr>
<th>CC</th>
<td><input type='text' name='cc' value='{$row['cc']}' style='width:$width'></td>
</tr>
EOS;

  print<<<EOS
<tr>
<th>Contact</th>
<td><input type='text' name='contact' value='{$row['contact']}' style='width:$width'></td>
</tr>
EOS;
  print<<<EOS
<tr>
<th>Deputies</th>
<td><input type='text' name='deputies' value='{$row['deputies']}' style='width:$width'></td>
</tr>
EOS;
  print<<<EOS
<tr>
<th>Host Site</th>
<td><input type='text' name='hostsite' value='{$row['hostsite']}' style='width:$width'></td>
</tr>
EOS;
  print<<<EOS
<tr>
<th>CMF</th>
<td><input type='text' name='cmf' value='{$row['cmf']}' style='width:$width'></td>
</tr>
EOS;
  print<<<EOS
<tr>
<th>OCCI</th>
<td><input type='text' name='ep_occi' value='{$row['ep_occi']}' style='width:$width'></td>
</tr>
EOS;
  print<<<EOS
<tr>
<th>CDMI</th>
<td><input type='text' name='ep_cdmi' value='{$row['ep_cdmi']}' style='width:$width'></td>
</tr>
EOS;

  print<<<EOS
<tr>
<th>res_size</th>
<td><input type='text' name='res_size' value='{$row['res_size']}' style='width:$width'></td>
</tr>
EOS;

  print<<<EOS
<tr>
<th>vm_max_size</th>
<td><input type='text' name='vm_max_size' value='{$row['vm_max_size']}' style='width:$width'></td>
</tr>
EOS;

  print<<<EOS
<tr>
<td colspan='2'>
<input type='hidden' name='id' value='$id'>
<input type='hidden' name='mode' value='dosave'>
<input type='button' value='Save' onclick='sf_2()'>
<input type='button' value='Delete' onclick='sf_d()'>
</td>
</tr>
EOS;

  print<<<EOS
</form>
</table>

<iframe name='hiddenframe' width='100' height='100' style="display:none"></iframe>

<script>
function sf_2() {
  document.form.submit();
}
function sf_d() {
  if (!confirm('Delete?')) return;
  var form = document.form;
  form.mode.value = 'dodelete';
  //form.target = 'hiddenframe'
  form.submit();
}
</script>
EOS;

  pagetail();
  exit;
}
### }}}

  pagehead($pgtitle);
  ParagraphTitle($pgtitle);
  ParagraphTitle("OS Template", 1);

  $qry = "SELECT O.*
, R.affiliation, R.cmf, R.cc
 FROM ostpl O
 LEFT JOIN resources R ON O.resid=R.id";

  $ret = myQuery($qry);

  print<<<EOS
<table border='1' class='mmdata'>

<th>#</th>
<th>Affiliation</th>
<th>CC</th>
<th>CMF</th>
<th>OSID</th>
<th>Date</th>
</tr>
EOS;

  $cnt = 0;
  while ($row = myFetchRow($ret)) {
    $cnt++;

    //dd($row);

    $resid = $row['resid'];

    $uri = $row['uri'];
    $ostpl = urlencode($uri); 
    $link =<<<EOS
<a href='lires.php?mode=detail&resid=$resid&ostpl=$ostpl'>detail</a>
EOS;

    print<<<EOS
<tr>
<td>{$cnt}</td>
<td>{$row['affiliation']}</td>
<td>{$row['cc']}</td>
<td>{$row['cmf']}</td>
<td class='l'>{$row['osid']}</td>
<td>{$row['idate']}</td>
<td>{$link}</td>
</tr>
EOS;
  }
  print<<<EOS
</table>

<script>
function _edit(id) {
  var url = "$env[self]?mode=edit&id="+id;
  wopen(url, 550,500,1,1);
}
</script>
EOS;

  pagetail();
  exit;

?>
