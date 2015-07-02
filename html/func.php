<?php

function pagehead($title='title', $popup=false) {
  print<<<EOS
<html>
<head>
<meta charset="UTF-8">

<title>$title</title>

<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
<link rel="icon" href="/favicon.ico" type="image/x-icon">
EOS;

  $script =<<<EOS
<script type="text/javascript" src="/js/script.js" charset="utf-8"></script>
<!--
<script src='/js/script.md5.js' type='text/javascript'></script>
-->
EOS;
  $style =<<<EOS
<link rel='stylesheet' type='text/css' href='/css/style.css'/>
EOS;


  // jquery
  $script .=<<<EOS
<script type="text/javascript" src="/js/jquery-1.8.3.min.js"></script>
EOS;

  // jquery horizontalNav
  $script .=<<<EOS
<script type="text/javascript" src="/js/jquery.horizontalNav.min.js"></script>
EOS;


  // jquery-ui
  // http://jqueryui.com/selectmenu/
  $script .=<<<EOS
<script type="text/javascript" src="/utl/jquery-ui/jquery-ui.min.js"></script>

EOS;
  $style .=<<<EOS
<link rel="stylesheet" href="/utl/jquery-ui/jquery-ui.min.css">
<link rel="stylesheet" href="/utl/jquery-ui/jquery-ui.theme.min.css">
EOS;

if (0) {
  // jquery mobile
  // http://jquerymobile.com/
  $style .=<<<EOS
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />
EOS;
  $script .=<<<EOS
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
EOS;
}


  print $script;
  print $style;
 
  print<<<EOS
</head>
<body>
EOS;
  if (!$popup) pagemenu();
}

function pagetail() {
  print<<<EOS
</body>
</html>
EOS;
}

function pagemenu() {
  $username = $_SESSION['username'];

  $t = get_proxy_info_timeleft();
  $time = getHumanTime($t);

  print<<<EOS
<div>
<a href='/home.php'>FedCloud Web</a>

Welcome {$username}
<a href='/logout.php'>[logout]</a>
proxy time left : $time
</div>
EOS;

  http://sebnitu.github.io/HorizontalNav/# 
  print<<<EOS
<div>
<nav class="horizontal-nav full-width horizontalNav-notprocessed">
  <ul>
    <li><a href="pxinfo.php">Proxy Info</a></li>
    <li><a href="create.php">Create VM</a></li>
    <li><a href="vmlist.php">Manage VM</a></li>
    <li><a href="curl.php">curl client</a></li>
    <li><a href="mgr.php">Management</a></li>
  </ul>
</nav>
</div>

<script>
$(document).ready(function() {
  $('.full-width').horizontalNav({});
});
</script>
EOS;

}

function ParagraphTitle($title, $level=0, $tostring=false) {
  global $conf, $env;
  $html = '';
  if ($level == 1) {
    $html=<<<EOS
<ul style="margin:9 0 0 0px; font-weight:bold;">
<li style="list-style-type:circle; font-size:13px; margin:0 0 0 -15px;">$title</li>
</ul>
EOS;
  } else { // level 0
    $html=<<<EOS
<ul style="margin:9 0 0 0px; font-weight:bold;">
<li style="list-style-type:square; font-size:15px; margin:0 0 0 -15px;">$title</li>
</ul>
EOS;
  }
  if ($tostring) { return $html; }
  else { print $html; return null; }
}


?>
