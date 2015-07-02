<?php

  include("path.php");
  include("$env[prefix]/inc/common.php");
  include("func.php");



### {{{
function _read_file($file, $type) {
  $tname = $file['tmp_name'];
  $cont = file_get_contents($tname);

  $user = get_username();

  global $conf;
  $dir = $conf['PROXY_DIR'];
  $userpath = "$dir/$user";
  @mkdir($userpath);

       if ($type == 'cert') $path = "$userpath/usercert.pem";
  else if ($type == 'key') $path = "$userpath/userkey.pem";

  rename($tname, $path);

  return $cont;
}

### }}}

### {{{
if ($mode == 'doupload') {
  _read_file($_FILES['filecert'],'cert');
  _read_file($_FILES['filekey'],'key');

  $title = "Generate Proxy Certificate";
  pagehead($title);
  ParagraphTitle($title);

  ParagraphTitle("Certificate Subject", 1);
  cert_info();

  $debug = true;
  $debug = false;

  ParagraphTitle("Generate Proxy Certifiate", 1);
  $keypass = $form['keypass'];
  $error = generate_proxy($keypass, $debug);

  if ($error) {
    print("<b>$error</b>");
  } else {
    print("<b>proxy generated</b>");
  }

  ParagraphTitle("Proxy Infomation", 1);
  proxy_info($debug);

  pagetail();
  exit;
}
### }}}




  $title = "Proxy Information";
  pagehead($title);
  ParagraphTitle($title);

  $debug = true;
  $debug = false;
  ParagraphTitle("Proxy Infomation", 1);
  proxy_info($debug);


  $title = "Generate Proxy Certificate";
  ParagraphTitle($title, 1);

  print<<<EOS
<form name='form' method='post' enctype='multipart/form-data'>

Certificate:
<input type='file' name='filecert' size='30' style='height:30;'>
<br>
Key:
<input type='file' name='filekey' size='30' style='height:30;'>
<br>
Password:
<input type='password' name='keypass' size='20''>
<br>

<input type='hidden' name='mode' value='doupload'>
<input type='button' value='OK' onclick='go_1()'>
</form>

<script>
function go_1() {
  var form = document.form;

  if (form.filecert.value == '') { alert('Choose your certificate file'); return; }
  if (form.filekey.value == '') { alert('Choose your key file'); return; }
  if (form.keypass.value == '') { alert('Input your key file password'); return; }

  form.submit();
}
</script>
EOS;
  pagetail();
  exit;

?>



