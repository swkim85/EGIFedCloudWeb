<?php

  unset($env);
  $env['prefix'] = "/www/fedcloud-web";
  include("$env[prefix]/inc/common.login.php");
  include("func.php");


  session_unset();
  session_destroy();
  
  print<<<EOS
<script type="text/javascript">
function doLogout() {
  window.location.replace("/index.php?url=$url");
}
doLogout();
</script>
EOS;
  exit;

?>
