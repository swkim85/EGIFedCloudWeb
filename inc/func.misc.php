<?php


# display an error message and terminate program
function iError($msg, $go_back=1, $win_close=0, $exit=1) {
  $msg = preg_replace("/\n/", "\\n", $msg);
  print("<script>\n");
  print("alert(\"$msg\");\n");
  if ($go_back) print("history.go(-1);\n");
  if ($win_close) print("window.close();");
  print("</script>\n");
  if ($exit) exit;
}

# fatal error (프로그램 오류, 일어나지 말아야 할 에러)
function fError($msg) {
  print<<<EOS
<script>
alert("시스템오류: $msg");
</script>
EOS;
  exit;
}

function cliError($msg) {
  print("cliError: $msg\n");
  exit;
}

# 팝업창에 표시한다.
function pError($msg) {
  PopupPageHead('오류');
  print<<<EOS
<table width='100%' height='100%'>
<tr><td valign='middle'>
<font color='red'>$msg</font>
</td></tr></table>
EOS;
  PopupPageTail();
  exit;
}

function Redirect($url, $http=true) {
  # HTTP 헤더가 이미 전송되었으면 자바스크립트 방식을 이용해야함
  if (headers_sent()) $http=false;
  if ($http) {
    header("Location: $url");
    exit;
  } else {
    print("<script>\n");
    print("window.location='$url';\n");
    print("</script>\n");
    exit;
  }
}

# display an error message and redirect to url
function ErrorRedir($msg, $url) {
  $msg = preg_replace("/\n/", "\\n", $msg);
  print("<script>\n");
  print("alert(\"$msg\");\n");
  print("window.location='$url';\n");
  print("</script>\n");
  exit;
}
function InformRedir($msg, $url) {
  ErrorRedir($msg, $url);
}
function InformAndCloseWindow($msg) {
  print<<<EOS
<script>
alert("$msg");
window.close();
</script>
EOS;
}
function CloseAndChangeParentWindow($url) {
  print<<<EOS
<script>
window.opener.document.location = "$url";
window.close();
</script>
EOS;
}
function CloseAndReloadParentWindow() {
  print<<<EOS
<script>
window.parent.document.location.reload();
window.close();
</script>
EOS;
}
function CloseAndReloadOpenerWindow() {
  print<<<EOS
<script>
window.opener.document.location.reload();
window.close();
</script>
EOS;
}



// 페이지 계산
//   list($start, $last, $page) = pager_calc_page($ipp, $total);
function pager_calc_page($ipp, $total) {

  global $form;

  $page = $form['page'];
  if ($page == '') $page = 1;
  $last = ceil($total/$ipp);
  if ($last == 0) $last = 1;
  if ($page > $last) $page = $last;
  $start = ($page-1) * $ipp;

  return array($start, $last, $page);
}

function Pager_s($url, $page, $total, $ipp) {
  global $conf, $env;
  $html = '';

  $btn_prev = "<img src='/img/calendar/l.gif' border=0 width=11 height=11>";
  $btn_next = "<img src='/img/calendar/r.gif' border=0 width=11 height=11>";
  $btn_prev10 = "<img src='/img/calendar/l2.gif' border=0 width=11 height=11>";
  $btn_next10 = "<img src='/img/calendar/r2.gif' border=0 width=11 height=11>";

  $last = ceil($total/$ipp);
  if ($last == 0) $last = 1;

  $start = floor(($page - 1) / 10) * 10 + 1;
  $end = $start + 9;

  $html .= "<table border='0' cellpadding='2' cellspacing='0'><tr>"; # table 1

  $attr1 = " onmouseover=\"this.className='pager_on'\""
         ." onmouseout=\"this.className='pager_off'\""
         ." class='pager_off' align='center' style='cursor:pointer;'";
  $attr2 = " onmouseover=\"this.className='pager_sel_on'\""
         ." onmouseout=\"this.className='pager_sel_off'\""
         ." class='pager_sel_off' align='center' style='cursor:pointer;'";
 
  # previous link
  if ($start > 1) {
    $prevpage = $start - 1;
    $html .= "<td$attr1 align=center onclick=\"script_Go('$url&page=$prevpage')\"><a href='$url&page=$prevpage'>$btn_prev10</a></td>\n";
  } else $html .= "<td align=center class='pager_static'>$btn_prev10</td>\n";

  if ($page > 1) {
    $prevpage = $page - 1;
    $html .= "<td$attr1 align=center onclick=\"script_Go('$url&page=$prevpage')\"><a href='$url&page=$prevpage'>$btn_prev</a></td>\n";
  } else $html .= "<td align=center class='pager_static'>$btn_prev</td>\n";


  if ($end > $last) $end = $last;
 $html .= "</td>";
  for ($i = $start; $i <= $end; $i++) {
    $s = "$i";
    if ($i != $page) {
      $html .= "<td$attr1 onclick=\"script_Go('$url&page=$i')\">$s</td>\n";
    } else {
      $html .= "<td$attr2>$s</td>\n";
    }
  }

  # next link
  if ($page < $last) {
    $nextpage = $page + 1;
    $html .= "<td$attr1 align=center onclick=\"script_Go('$url&page=$nextpage')\"><a href='$url&page=$nextpage'>$btn_next</a></td>\n";
  } else $html .= "<td align=center class='pager_static'>$btn_next</td>\n";

  if ($end < $last) {
    $nextpage = $end + 1;
    $html .= "<td$attr1 align=center onclick=\"script_Go('$url&page=$nextpage')\"><a href='$url&page=$nextpage'>$btn_next10</a></td>\n";
  } else $html .= "<td align=center class='pager_static'>$btn_next10</td>\n";

  $html .= "</tr></table>\n";

  return $html;
}


function Pager_f($formname, $page, $total, $ipp) {
  global $conf, $env;
  $html = '';

  $btn_prev = "<img src='/img/calendar/l.gif' border=0 width=11 height=11>";
  $btn_next = "<img src='/img/calendar/r.gif' border=0 width=11 height=11>";
  $btn_prev10 = "<img src='/img/calendar/l2.gif' border=0 width=11 height=11>";
  $btn_next10 = "<img src='/img/calendar/r2.gif' border=0 width=11 height=11>";

  $last = ceil($total/$ipp);
  if ($last == 0) $last = 1;

  $start = floor(($page - 1) / 10) * 10 + 1;
  $end = $start + 9;

  //print("$formname / page=$page / total=$total / ipp=$ipp / start=$start / last=$last / end=$end <br>");

  $html .= "<table border='0' cellpadding='2' cellspacing='0'><tr>"; # table 1

  $attr1 = " onmouseover=\"this.className='pager_on'\""
         ." onmouseout=\"this.className='pager_off'\""
         ." class='pager_off' align='center' style='cursor:pointer;'";
  $attr2 = " onmouseover=\"this.className='pager_sel_on'\""
         ." onmouseout=\"this.className='pager_sel_off'\""
         ." class='pager_sel_off' align='center' style='cursor:pointer;'";
 
  # previous link
  if ($start > 1) {
    $prevpage = $start - 1;
    $html .= "<td$attr1 align=center onclick=\"pager_Go('$prevpage')\">$btn_prev10</td>\n";
  } else $html .= "<td align=center class='pager_static'>$btn_prev10</td>\n";

  if ($page > 1) {
    $prevpage = $page - 1;
    $html .= "<td$attr1 align=center onclick=\"pager_Go('$prevpage')\">$btn_prev</td>\n";
  } else $html .= "<td align=center class='pager_static'>$btn_prev</td>\n";


  if ($end > $last) $end = $last;
  $html .= "</td>";
  for ($i = $start; $i <= $end; $i++) {
    $s = "$i";
    if ($i != $page) {
      $html .= "<td$attr1 onclick=\"pager_Go('$i')\">$s</td>\n";
    } else {
      $html .= "<td$attr2>$s</td>\n";
    }
  }

  # next link
  if ($page < $last) {
    $nextpage = $page + 1;
    $html .= "<td$attr1 align=center onclick=\"pager_Go('$nextpage')\">$btn_next</td>\n";
  } else $html .= "<td align=center class='pager_static'>$btn_next</td>\n";

  if ($end < $last) {
    $nextpage = $end + 1;
    $html .= "<td$attr1 align=center onclick=\"pager_Go('$nextpage')\">$btn_next10</td>\n";
  } else $html .= "<td align=center class='pager_static'>$btn_next10</td>\n";

  $html .= "</tr></table>\n";
  $html .=<<<EOS
<script>
function pager_Go(page) {
  document.$formname.page.value = page;
  document.$formname.submit();
}
</script>
EOS;
  return $html;
}


# 'yyyy-mm-dd hh:mm:ss' 형식의 datetime 문자열을
# mktime을 이용하여 타임스템프값(초단위)을 구함
function GetTimeStamp($date) {
  $y = substr($date,0,4);
  $m = substr($date,5,2);
  $d = substr($date,8,2);
  $h = substr($date,11,2);
  $n = substr($date,14,2);
  $s = substr($date,17,2);
  $t = mktime($h,$n,$s, $m,$d,$y);
  return $t;
}

# 주어진 날짜 및 시각에서 현재 시각까지 지난 시간을 초단위로 구함
# date는 'yyyy-mm-dd hh:mm:ss' 형식의 datetime 문자열
function AgeOfDate($date) {
  $t1 = GetTimeStamp($date);
  $diff = time() - $t1;
  return $diff;
}

function Percent($value, $total) {
  if ($total == 0) return "0%";
  $percent = sprintf("%3.1f%%", $value/$total*100);
  return $percent;
}


# 다운로드를 위한 HTTP 헤더
function DownloadHeader($filename) {
  header("Content-disposition: attachment; filename=\"$filename\"");
  header("Content-type: application/octetstream");
  header("Pragma: no-cache");
  header("Expires: 0");
}

# 한글 자르기
function cut_str($msg,$cut_size,$tail="") { 
  $msg_size=strlen($msg); 

  for($i=0,$r=0;$i<$cut_size;$i++){ 
    $real_cut_size++; 
    if(ord($msg[$r])>127){//한글일 경우 
      $real_cut_size++; 
      $r++;//한글일 경우 길이가 2여서 다음값으로 가기 위해서는 +1을 한다. 
     } 
     $r++;//다음자료를 가져옴 
  } 

  if($msg_size<=$real_cut_size){## 글길이 이하로 자를 경우 그냥 리턴한다. 
    return $msg; 
  }else{ 
    $str=substr($msg,0,$real_cut_size); 
    $str.=$tail; 
  } 
  return $str; 
}

# 한글 뒤에서 자르기
function back_cut($str,$len,$head="") { 
    if(strlen($str)<=$len) return $str; 

    $str2=substr($str,$len*-1); 
    $size=strlen($str2); 

    for($i=$size,$j=1;$j<=$size;$i--) 
    { 
      $chr=substr($str2,$j*-1,1); 

      if(ord($chr)>127) { 
        $j++; 
        $chr=substr($str,$j*-1,1).$chr; 
      } 

    	$result=$chr.$result; 
    	$j++; 
    } 

    return $head.$result; 
}


// 사용법  form value, session value 중에서 선택한다.
// $sday = getvalue($form['sday'], $_SESSION['drpt_sday'], '');
function getvalue($formv, $sessv, $default_value) {
  if ($formv) return $formv;
  else if ($sessv) return $sessv;
  else return $default_value;
}


// 가로 방향으로 버튼을 나열
// usage button_box($btn1, $btn2, $btn3, ....)
function button_box() {
  $len = func_num_args();
  $args = func_get_args();
  $html = "<table border='0'><tr>";
  for ($i = 0; $i < $len; $i++) {
    $btn = $args[$i];
    $html.="<td>";
    $html.=$btn;
    $html.="</td>";
  }
  $html.="</tr></table>";
  return $html;
}

// usage get_valid_values($v1, $v2, $v3, ...)
function get_valid_values() {
  $len = func_num_args();
  $args = func_get_args();
  for ($i = 0; $i < $len; $i++) {
    $v = $args[$i];
    if ($v) return $v;
  }
}



// 디버그 함수
function dd($msg) {
       if (is_string($msg)) print($msg);
  else if (is_array($msg)) { print("<pre>"); print_r($msg); print("</pre>"); }
  else print_r($msg);
}


# format byte data (reused from phpMyAdmin)
//list($fs, $fu) = FormatByteDown($bytes,3,1);
function FormatByteDown($value, $limes=6, $comma=0) {
  $dh           = pow(10, $comma);
  $li           = pow(10, $limes);
  $return_value = $value;

  $byteUnits    = array('Bytes', 'KB', 'MB', 'GB');
  $unit         = $byteUnits[0];

  if ($value >= $li*1000000) {
    $value = round($value/(1073741824/$dh))/$dh;
    $unit  = $byteUnits[3];
  } else if ($value >= $li*1000) {
    $value = round($value/(1048576/$dh))/$dh;
    $unit  = $byteUnits[2];
  } else if ($value >= $li) {
    $value = round($value/(1024/$dh))/$dh;
    $unit  = $byteUnits[1];
  }

  if ($unit != $byteUnits[0]) {
    $return_value = number_format($value, $comma, '.', ',');
  } else {
    $return_value = number_format($value, 0, '.', ',');
  }

  return array($return_value, $unit);
}        

function get_username() {
  return $_SESSION['username'];
}


?>
