<?php

  include("path.php");
  include("$env[prefix]/inc/common.php");
  include("func.php");

  pagehead('Management');
  ParagraphTitle("Management");

  print<<<EOS
<ul>
<li><a href="mgrsite.php">Site Management</a>
<li><a href="mgrostpl.php">OS Template</a>
<li><a href="mgrvm.php">VM Management</a>
EOS;
  print<<<EOS
</ul>
EOS;

  $title = 'List of records contained in the NEW cloud accounting database started in last 3 hours. (Times are UTC)';
  $url = "http://goc-accounting.grid-support.ac.uk/cloudtest/vmshour2.html";
  print("<dt>$title");
  print("<dd><a href='$url' target='_blank'>$url</a>");

  $title = 'Sites publishing new cloud accounting records (SSM 2.0)';
  $url = "http://goc-accounting.grid-support.ac.uk/cloudtest/cloudsites2.html";
  print("<dt>$title");
  print("<dd><a href='$url' target='_blank'>$url</a>");

  $title = 'EGI ACCOUNTING PORTAL';
  $url = "http://accounting-devel.egi.eu/cloud.php";
  print("<dt>$title");
  print("<dd><a href='$url' target='_blank'>$url</a>");

  pagetail();


?>
