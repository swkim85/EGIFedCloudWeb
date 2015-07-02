<?php

  include("path.php");
  include("$env[prefix]/inc/common.login.php");
  include("func.php");

if ($mode == 'dologin') {
  //dd($form);
  $username = $form['username'];
  $_SESSION['username'] = $username;


  // ÀÎÁõ¾øÀ½

  Redirect("/home.php");
  exit;
}


  print<<<EOS
<html>
<head>
<title>page</title>
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
<link rel="icon" href="/favicon.ico" type="image/x-icon">
<style type='text/css'> 
body {  font-size: 12px; font-family: ±¼¸²,µ¸¿ò,verdana;
  font-style: normal; line-height: 12pt;
  text-decoration: none; color: #333333;
}
table,td,th { font-size: 12px; font-family: µ¸¿ò,verdana; white-space: nowrap; }
</style>

</head>
<body>

<form name='loginform' method='post'>
username : <input type='text' size='20' name='username' value='sangwan'>
<input type='hidden' name='mode' value='dologin'>
<input type='button' value='login' onclick='sf_1()'>
</form>

<script>
function sf_1() {
  document.loginform.submit();
}
</script>

</body>
</html>
EOS;
  exit;

?>
