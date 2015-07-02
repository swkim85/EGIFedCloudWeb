<?php


# 현재날짜와 시간을 구함
function CurrentTime($string_type = false) {
  $d = date("Y-m-d H:i:s");
  if ($string_type) return $d;
  $date['year']  = (int)substr($d, 0, 4);
  $date['month'] = (int)substr($d, 5, 2);
  $date['day']   = (int)substr($d, 8, 2);
  $date['hour']  = (int)substr($d, 11, 2);
  $date['min']   = (int)substr($d, 14, 2);
  $date['sec']   = (int)substr($d, 17, 2);
  return $date;
}

# get the current time of day in usec
function TimeStampUsec() {
  $now =  gettimeofday();
  $usec = $now['sec'] * 1000000 + $now['usec'];
  return $usec;
}

# 주어진 년도의 날짜수를 구함. (365 또는 366)
# 예) $days = DaysPerYear($year);
function DaysPerYear($year) {
  if ($year % 400 == 0) $days = 366;
  else if ($year % 100 == 0) $days = 365;
  else if ($year % 4 == 0) $days = 366;
  else $days = 365;
  return $days;
}

# 주어진 년도와 달의 날짜수를 구함. (28, 29, 30, 31)
# 예) $days = DaysPerMonth($year, $month);
function DaysPerMonth($year, $month) {
  if ($month == 1) $days = 31;
  else if ($month == 2) {
    if ($year % 400 == 0) $days = 29;
    else if ($year % 100 == 0) $days = 28;
    else if ($year % 4 == 0) $days = 29;
    else $days = 28;
  }
  else if ($month == 3) $days = 31;
  else if ($month == 4) $days = 30;
  else if ($month == 5) $days = 31;
  else if ($month == 6) $days = 30;
  else if ($month == 7) $days = 31;
  else if ($month == 8) $days = 31;
  else if ($month == 9) $days = 30;
  else if ($month == 10) $days = 31;
  else if ($month == 11) $days = 30;
  else if ($month == 12) $days = 31;
  return $days;
}

# 요일계산
# 0=일요일, 1=월, 2=화, 3=수, 4=목, 5=금, 6=토
function DayOfWeek($year, $month, $day) {
  if ($month < 3) { $year--; $month += 12; }
  $dow = ($year + (int)($year / 4) - (int)($year / 100)
        + (int)($year / 400) + (int)((13 * $month + 8) / 5) + $day) % 7;
  return $dow;
}
function DayOfWeekString($year, $month, $day, $opt=0) {
  global $conf;
  if ($opt == 0) $dow_str = $conf['dayofweek'];
  else if ($opt == 1) $dow_str = $conf['dayofweek2'];
  $dow = DayOfWeek($year, $month, $day, 1);
  //print("===$dow===");
  return $conf['dayofweek'][$dow];
}
function DayOfWeekStringByArray($date, $opt=0) {
  $d = DateStringToArray($date);
  //print_r($d);
  return DayOfWeekString($d['year'], $d['month'], $d['day'], $opt);
}
function day_of_week($date) {
  $y = (int)substr($date, 0, 4);
  $m = (int)substr($date, 5, 2);
  $d = (int)substr($date, 8, 2);
  $w = DayOfWeek($y, $m, $d);
  global $conf;
  return $conf['dayofweek'][$w];
}


# 신천지 날짜
function ScjDateFormat($date) {
  if (is_string($date)) {
    $date = DateStringToArray($date);
  }
  $year = $date['year']-1983;
  if ($year < 0) return "";
  $ret = sprintf("신천기 %d년 %02d월 %02d일", $year, $date['month'], $date['day']);
  return $ret;
}

# $datestr는 "yyyy-mm-dd hh:mm:ss" 형식의 문자
# $format은 날자 포맷
#   %Y   년도 (1970...)
#   %y   신천기 (년도-1983)
#   %m   월 (01..12)
#   %d   해당월에 날짜 (01..31)
#   %H   시 (00..23)
#   %h   시 (1..12)
#   %p   오전/오후
#   %M   분 (00..59)
#   %S   초 (00..60)
#   %w   요일 (일,월,화,수,목,금,토)
#   %W   요일 (주일,월요일,화요일,수요일,목요일,금요일,토요일)
function DateTimeFormat($format, $datestr) {
  $dows = array('일','월','화','수','목','금','토');
  $dows2 = array('주일','월요일','화요일','수요일','목요일','금요일','토요일');
  $ampm = array('오전','오후');
  $yr  = substr($datestr,0,4);        # year (1970...)
  $sy = sprintf("%d", floor(substr($datestr,0,4)) - 1983); # 신천기년도
  //if ($sy < 0) return "";
  $mo  = substr($datestr,5,2);       # month (01..12)
  $dm  = substr($datestr,8,2);       # day of month (01..31)
  $hr = substr($datestr,11,2);       # hour (00..23)
  if ($hr < 12) $ap = $ampm[0];      # AM / PM
  else $ap = $ampm[1];
  //print("$format $datestr $hr<br>");

  $_hr = floor($hr);
  $_hs = ($_hr>12) ? $_hr-12 : $_hr;    
  $hs  = sprintf("%d", $_hs);        # hour (1..12)

  $mi = substr($datestr,14,2);       # minute (00..59)
  $se  = substr($datestr,17,2);      # second (00..60)
  $w = DayOfWeek($yr, $mo, $dm);
  $dw = $dows[$w];
  $dw2 = $dows2[$w];
  //print("$yr, $sy, $mo, $dm, $hr, $hs, $mi, $se, $dw");

  $ret = $format;
  $ret = ereg_replace("%Y", $yr, $ret);
  $ret = ereg_replace("%y", $sy, $ret);
  $ret = ereg_replace("%m", $mo, $ret);
  $ret = ereg_replace("%d", $dm, $ret);
  $ret = ereg_replace("%H", $hr, $ret);
  $ret = ereg_replace("%h", $hs, $ret);
  $ret = ereg_replace("%p", $ap, $ret);
  $ret = ereg_replace("%M", $mi, $ret);
  $ret = ereg_replace("%S", $se, $ret);
  $ret = ereg_replace("%w", $dw, $ret);
  $ret = ereg_replace("%W", $dw2, $ret);
  return $ret;
}
# $timestr는 "HH:MM:SS" 형식의 문자열
function TimeFormat($format, $timestr) {
  $datestr = "1900-00-00 $timestr";
  return DateTimeFormat($format, $datestr);
}
# $datestr는 "YYYY-MM-DD" 형식의 문자열
function DateFormat($format, $datestr) {
  $str = "$datestr 00:00:00";
  return DateTimeFormat($format, $str);
}



/*
# datetime is a string with format "yyyy-mm-dd hh:mm:ss"
# format is a string including formating sequences.
#   %a   abbreviated weekly name (Sun..Sat)
#   %b   abbreviated month name (Jan..Dec)
#   %d   day of month (01..31)
#   %e   day of month, blank padded ( 1..31)
#   %H   hour (00..23)
#   %I   hour (01..12)
#   %k   hour ( 0..23)
#   %l   hour ( 1..12)
#   %m   month (01..12)
#   %M   minute (00..59)
#   %p   AM or PM
#   %S   second (00..60)
#   %T   time, 24-hour (hh:mm:ss)
#   %w   day of week (0..6); 0 represents Sunday
#   %Y   year (1970...)
#   %y   last two digits of year (00..99)
function FormatDateAndTime($datetime, $format) {
  global $lang;

  if ($datetime == "0000-00-00 00:00:00") return "NULL";
  $ampm = explode(":", $lang['ampm']);
  $dows = explode(":", $lang['dayofweek']);
  $months = explode(":", $lang['months']);

  $d = substr($datetime,8,2);        # day of month (01..31)
  $e = ereg_replace("^0", " ", $d);  # day of month (1..31)
  $H = substr($datetime,11,2);       # hour (00..23)
  $k = ereg_replace("^0", "", $H);   # hour (0..23)
  $l = ($k > 12) ? $k - 12 : $k;     # hour (1..12)
  $I = sprintf("%02d", $l);          # hour (01..12)
  $m = substr($datetime,5,2);        # month (01..12)
  $M = substr($datetime,14,2);       # minute (00..59)
  $p = ($k < 12) ? $ampm[0] : $ampm[1]; # AM or PM
  $S = substr($datetime,17,2);       # second (00..60)
  $T = substr($datetime,11,8);       # time, 24-hour (hh:mm:ss)
  $y = substr($datetime,2,2);        # last two digits of year (00..99)
  $Y = substr($datetime,0,4);        # year (1970...)
  $month = (int)$m;
  $w = DayOfWeek($y, $month, $e);    # day of week (0..6)
  $a = $dows[$w];                    # abbreviated weekly name (Sun..Sat)
  $b = $months[$month-1];            # abbreviated month name (Jan..Dec)

  $ret = $format;
  $ret = ereg_replace("%a", $a, $ret);
  $ret = ereg_replace("%b", $b, $ret);
  $ret = ereg_replace("%d", $d, $ret);
  $ret = ereg_replace("%e", $e, $ret);
  $ret = ereg_replace("%H", $H, $ret);
  $ret = ereg_replace("%I", $I, $ret);
  $ret = ereg_replace("%k", $k, $ret);
  $ret = ereg_replace("%l", $l, $ret);
  $ret = ereg_replace("%m", $m, $ret);
  $ret = ereg_replace("%M", $M, $ret);
  $ret = ereg_replace("%p", $p, $ret);
  $ret = ereg_replace("%S", $S, $ret);
  $ret = ereg_replace("%T", $T, $ret);
  $ret = ereg_replace("%w", $w, $ret);
  $ret = ereg_replace("%Y", $Y, $ret);
  $ret = ereg_replace("%y", $y, $ret);

  return $ret;
}
*/


# Convert a date-and-time string to an array
# str is a string with format "yyyy-mm-dd hh:mm:ss"
function DateStringToArray($str) {
  $ret = array();
  $ret['year']  = (int)substr($str, 0, 4);
  $ret['month'] = (int)substr($str, 5, 2);
  $ret['day']   = (int)substr($str, 8, 2);
  $ret['hour']  = (int)substr($str, 11, 2);
  $ret['min']   = (int)substr($str, 14, 2);
  $ret['sec']   = (int)substr($str, 17, 2);
  return $ret;
}

# 2000-01-01 이후로 지난 날짜를 구함
# 예) $days = DaysFrom2000($year, $month, $day);
function DaysFrom2000Str($str) {
  $year  = (int)substr($str, 0, 4);
  $month = (int)substr($str, 5, 2);
  $day   = (int)substr($str, 8, 2);
  return DaysFrom2000($year, $month, $day);
}
function DaysFrom2000($year, $month, $day) {
  $days = 0;
  for ($i = 2000; $i < $year; $i++) $days += DaysPerYear($i);
  for ($i = 1; $i < $month; $i++) $days += DaysPerMonth($year, $i);
  for ($i = 1; $i <= $day; $i++) $days++;
  return $days;
}
# 2000-01-01 00:00:00 이후로 지난 시간을 초단위로 구함
# 예) $days = SecondssFrom2000($year, $month, $day, $hour, $min, $sec);
function SecondsFrom2000($year, $month, $day, $hour, $min, $sec) {
  $days = DaysFrom2000($year, $month, $day);
  $secs = ($days * 86400) + ($hour * 3600) + ($min * 60) + $sec;
  return $secs;
}
# ex) $days = SecondssFrom2000($date)
#     where $date is a array with keys(year, month, day, hour, min, sec)
function SecondsFrom2000Array($date) {
  if (is_string($date)) $date = DateStringToArray($date);
  return SecondsFrom2000($date['year'], $date['month'], $date['day'],
    $date['hour'], $date['min'], $date['sec']);
}

# 2000-01-01 이후로 $days번째 날의 날짜를 구한다.
# 예) list($year, $moneh, $day) = DaysFromInv($days);
function DaysFrom2000Inv($days) {
  $year = 2000;
  while ($days > 0) {
    $temp = DaysPerYear($year);
    if ($days - $temp > 0) { $days -= $temp; $year++; }
    else break;
  }
  $month = 1;
  while ($days > 0) {
    $temp = DaysPerMonth($year, $month);
    if ($days - $temp > 0) { $days -= $temp; $month++; }
    else break;
  }
  $day = $days;

  $ret = array();
  $ret['year'] = $year;
  $ret['month'] = $month;
  $ret['day'] = $day;
  return $ret;
}


# $d1, $d2 are a date string in format "yyyy-mm-dd hh:mm:ss"
# return the time difference between d1 and d2 in seconds.
# ex) $secs = DiffDateSecs($date1, $date1);
function DiffDateSecs($d1, $d2) {
  if (is_string($d1)) $a1 = DateStringToArray($d1);
  else $a1 = $d1;
  if (is_string($d2)) $a2 = DateStringToArray($d2);
  else $a2 = $d2;
  $s1 = SecondsFrom2000Array($a1);
  $s2 = SecondsFrom2000Array($a2);
  $secs = $s1 - $s2;
  return $secs;
}
function DiffDate($d1, $d2) {
  if (is_string($d1)) $a1 = DateStringToArray($d1);
  else $a1 = $d1;
  if (is_string($d2)) $a2 = DateStringToArray($d2);
  else $a2 = $d2;
  $day1 = DaysFrom2000($a1['year'], $a1['month'], $a1['day']);
  $day2 = DaysFrom2000($a2['year'], $a2['month'], $a2['day']);
  return $day1 - $day2;
}

function AddDate($year, $month, $day, $days) {
  $day = DaysFrom2000($year, $month, $day);
  $day += $days;
  $ret = DaysFrom2000Inv($day);
  return $ret;
}


function PreviousMonth($year, $month) {
  if ($month > 1) return array(year=>$year, month=>$month-1);
  else return array(year=>$year-1, month=>12);
}
function NextMonth($year, $month) {
  if ($month < 12) return array(year=>$year, month=>$month+1);
  else return array(year=>$year+1, month=>1);
}


# dt를 로컬 시간으로 변환된 문자열을 리턴함.
# dt와 now 는 키값으로 (year, month, day, hour, min, sec)를 가지고 있는 array임
function SmartDateTime($dt, $now=null) {
  if ($now == null) $now = CurrentTime();
  # dt와 now가 문자열 'yyyy-mm-dd hh:mm:ss' 이면 array로 변환하여 계산
  if (is_string($dt)) $dt = DateStringToArray($dt);
  if (is_string($now)) $now = DateStringToArray($now);

  if ($dt['year'] == $now['year'] &&
      $dt['month'] == $now['month'] &&
      $dt['day'] == $now['day']) { # 같은 날짜이면 시간만 출력함

   # 같은 날짜이면 날짜와 시간을 모두 출력함
    $str = sprintf("%04d-%02d-%02d", $dt['year'], $dt['month'], $dt['day']);
    $str .= sprintf(" %02d:%02d:%02d", $dt['hour'], $dt['min'], $dt['sec']);

  } else { # 서로 다른 날짜이면 날짜만 출력함
    $str = sprintf("%04d-%02d-%02d", $dt['year'], $dt['month'], $dt['day']);
  }
  return $str;
}

function getHumanTime($s) {
  $unit = array('D'=>'일','H'=>'시간','M'=>'분','S'=>'초');
  $unit = array('D'=>' days','H'=>' hours','M'=>' mins','S'=>' secs');

  $len = strlen($s);
  if ($len == 19) {
    $yy = (int)substr($s, 0, 4);
    $mm = (int)substr($s, 5, 2);
    $dd = (int)substr($s, 8, 2);
    $hh = (int)substr($s, 11, 2);
    $ii = (int)substr($s, 14, 2);
    $ss = (int)substr($s, 17, 2);
    $s = time() - mktime($hh,$ii,$ss,$mm,$dd,$yy);
  }

  $m = $s / 60;
  $h = $s / 3600;
  $d = $s / 86400;
  if ($m > 1) {
    if ($h > 1) {
      if ($d > 1) {
        return (int)$d.$unit['D'];
      } else {
        return (int)$h.$unit['H'];
      }
    } else {
      return (int)$m.$unit['M'];
    }
  } else {
    return (int)$s.$unit['S'];
  }
}

/*
# get the time stamp of date
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
*/

?>
