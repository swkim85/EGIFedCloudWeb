<?php

  include("path.php");
  include("$env[prefix]/inc/common.php");
  include("func.php");


### {{{
function radio_list($name, $items, $preset) {
  $html = '';
  foreach ($items as $item) {
    //dd($item);
    list($v, $t) = $item;
    if ($v == $preset) $sel = 'checked'; else $sel = '';
    $html .=<<<EOS
<label><input type='radio' name='$name' value='$v'$sel>$t</label>
EOS;
  }
  return $html;
}

function checkbox($name, $title, $checked) {
  if ($checked) $chk = ' checked'; else $chk = '';
  $html =<<<EOS
<label><input type='checkbox' name='$name' $chk>$title</label>
EOS;
  return $html;
}

function split_head_body($output, &$headers, &$body) {
  $header = array();
  $body = array();
  $is_header = true;
  foreach ($output as $line) {
    if (!$line) { $is_header = false; continue; }
    if ($is_header) $headers[] = $line;
    else $body[] = $line;
  }
  //dd($headers);
  //dd($body);
}

### }}}


  pagehead('cURL client');

  print<<<EOS
<table border='1'>
<form name='form' action='$env[self]' method='get'>
<tr>
<td>curl</td>
</tr>

<tr>
<td>URL</td>
<td>
<input type='text' name='url' size='60' value="{$form['url']}">
</td>
</tr>
EOS;

  $items = array( array('GET','GET'), array('POST','POST') );
  $name = 'op_X';
  $preset = $form['op_X']; if ($preset == '') $preset = 'GET';
  $html = radio_list($name, $items, $preset);
  print<<<EOS
<tr>
<td>-X/--request</td>
<td>
$html
</td>
</tr>
EOS;


  $title = 'Include protocol headers in the output (H/F)';
  $name = 'op_i';
  $checked = $form['op_i'];
  $html = checkbox($name, $title, $checked);
  print<<<EOS
<tr>
<td>-i</td>
<td>$html</td>
</td>
</tr>
EOS;


  $max_headers = 2;
  for ($i = 0; $i < $max_headers; $i++) {
    $key = "op_H{$i}";
    $value = $form[$key];
    print<<<EOS
<tr>
<td>-H/--header</td>
<td><input type='text' name='{$key}' size='60' value="$value"></td>
</tr>
EOS;
  }

  //$value = $form['op_d'];
  $value = htmlentities($form['op_d']);
  print<<<EOS
<tr>
<td>-d/--data</td>
<td><input type='text' name='op_d' size='60' value="$value"></td>
</tr>
EOS;

  $title = 'Make the operation more talkative';
  $name = 'op_v';
  $checked = $form['op_v'];
  $html = checkbox($name, $title, $checked);
  print<<<EOS
<tr>
<td>-v/--verbose</td>
<td>$html</td>
</td>
</tr>
EOS;


  $title = 'json parse the response body';
  $name = 'pp_json';
  $checked = $form['pp_json'];
  $html = checkbox($name, $title, $checked);
  print<<<EOS
<tr>
<td>json</td>
<td>$html</td>
</td>
</tr>
EOS;


  print<<<EOS
<tr>
<td></td>
<td>
<input type='hidden' name='mode' value='doit'>
<input type='submit' value='OK'>
</td>
</tr>

</form>
</table>
EOS;

if ($mode != 'doit') {
  $html=<<<EOS
# curl -h
Usage: curl [options...] <url>
Options: (H) means HTTP/HTTPS only, (F) means FTP only
    --anyauth       Pick "any" authentication method (H)
 -a/--append        Append to target file when uploading (F/SFTP)
    --basic         Use HTTP Basic Authentication (H)
    --cacert <file> CA certificate to verify peer against (SSL)
    --capath <directory> CA directory to verify peer against (SSL)
 -E/--cert <cert[:passwd]> Client certificate file and password (SSL)
    --cert-type <type> Certificate file type (DER/PEM/ENG) (SSL)
    --ciphers <list> SSL ciphers to use (SSL)
    --compressed    Request compressed response (using deflate or gzip)
 -K/--config <file> Specify which config file to read
    --connect-timeout <seconds> Maximum time allowed for connection
 -C/--continue-at <offset> Resumed transfer offset
 -b/--cookie <name=string/file> Cookie string or file to read cookies from (H)
 -c/--cookie-jar <file> Write cookies to this file after operation (H)
    --create-dirs   Create necessary local directory hierarchy
    --crlf          Convert LF to CRLF in upload
    --crlfile <file> Get a CRL list in PEM format from the given file
 -d/--data <data>   HTTP POST data (H)
    --data-ascii <data>  HTTP POST ASCII data (H)
    --data-binary <data> HTTP POST binary data (H)
    --data-urlencode <name=data/name@filename> HTTP POST data url encoded (H)
    --delegation STRING GSS-API delegation permission
    --digest        Use HTTP Digest Authentication (H)
    --disable-eprt  Inhibit using EPRT or LPRT (F)
    --disable-epsv  Inhibit using EPSV (F)
 -D/--dump-header <file> Write the headers to this file
    --egd-file <file> EGD socket path for random data (SSL)
    --engine <eng>  Crypto engine to use (SSL). "--engine list" for list
 -f/--fail          Fail silently (no output at all) on HTTP errors (H)
 -F/--form <name=content> Specify HTTP multipart POST data (H)
    --form-string <name=string> Specify HTTP multipart POST data (H)
    --ftp-account <data> Account data to send when requested by server (F)
    --ftp-alternative-to-user <cmd> String to replace "USER [name]" (F)
    --ftp-create-dirs Create the remote dirs if not present (F)
    --ftp-method [multicwd/nocwd/singlecwd] Control CWD usage (F)
    --ftp-pasv      Use PASV/EPSV instead of PORT (F)
 -P/--ftp-port <address> Use PORT with address instead of PASV (F)
    --ftp-skip-pasv-ip Skip the IP address for PASV (F)
    --ftp-ssl       Try SSL/TLS for ftp transfer (F)
    --ftp-ssl-ccc   Send CCC after authenticating (F)
    --ftp-ssl-ccc-mode [active/passive] Set CCC mode (F)
    --ftp-ssl-control Require SSL/TLS for ftp login, clear for transfer (F)
    --ftp-ssl-reqd  Require SSL/TLS for ftp transfer (F)
 -G/--get           Send the -d data with a HTTP GET (H)
 -g/--globoff       Disable URL sequences and ranges using {} and []
 -H/--header <line> Custom header to pass to server (H)
 -I/--head          Show document info only
 -h/--help          This help text
    --hostpubmd5 <md5> Hex encoded MD5 string of the host public key. (SSH)
 -0/--http1.0       Use HTTP 1.0 (H)
    --ignore-content-length  Ignore the HTTP Content-Length header
 -i/--include       Include protocol headers in the output (H/F)
 -k/--insecure      Allow connections to SSL sites without certs (H)
    --interface <interface> Specify network interface/address to use
 -4/--ipv4          Resolve name to IPv4 address
 -6/--ipv6          Resolve name to IPv6 address
 -j/--junk-session-cookies Ignore session cookies read from file (H)
    --keepalive-time <seconds> Interval between keepalive probes
    --key <key>     Private key file name (SSL/SSH)
    --key-type <type> Private key file type (DER/PEM/ENG) (SSL)
    --krb <level>   Enable Kerberos with specified security level (F)
    --libcurl <file> Dump libcurl equivalent code of this command line
    --limit-rate <rate> Limit transfer speed to this rate
 -l/--list-only     List only names of an FTP directory (F)
    --local-port <num>[-num] Force use of these local port numbers
 -L/--location      Follow Location: hints (H)
    --location-trusted Follow Location: and send auth to other hosts (H)
 -M/--manual        Display the full manual
    --max-filesize <bytes> Maximum file size to download (H/F)
    --max-redirs <num> Maximum number of redirects allowed (H)
 -m/--max-time <seconds> Maximum time allowed for the transfer
    --negotiate     Use HTTP Negotiate Authentication (H)
 -n/--netrc         Must read .netrc for user name and password
    --netrc-optional Use either .netrc or URL; overrides -n
 -N/--no-buffer     Disable buffering of the output stream
    --no-keepalive  Disable keepalive use on the connection
    --no-sessionid  Disable SSL session-ID reusing (SSL)
    --noproxy       Comma-separated list of hosts which do not use proxy
    --ntlm          Use HTTP NTLM authentication (H)
 -o/--output <file> Write output to <file> instead of stdout
    --pass  <pass>  Pass phrase for the private key (SSL/SSH)
    --post301       Do not switch to GET after following a 301 redirect (H)
    --post302       Do not switch to GET after following a 302 redirect (H)
 -#/--progress-bar  Display transfer progress as a progress bar
 -x/--proxy <host[:port]> Use HTTP proxy on given port
    --proxy-anyauth Pick "any" proxy authentication method (H)
    --proxy-basic   Use Basic authentication on the proxy (H)
    --proxy-digest  Use Digest authentication on the proxy (H)
    --proxy-negotiate Use Negotiate authentication on the proxy (H)
    --proxy-ntlm    Use NTLM authentication on the proxy (H)
 -U/--proxy-user <user[:password]> Set proxy user and password
    --proxy1.0 <host[:port]> Use HTTP/1.0 proxy on given port
 -p/--proxytunnel   Operate through a HTTP proxy tunnel (using CONNECT)
    --pubkey <key>  Public key file name (SSH)
 -Q/--quote <cmd>   Send command(s) to server before file transfer (F/SFTP)
    --random-file <file> File for reading random data from (SSL)
 -r/--range <range> Retrieve only the bytes within a range
    --raw           Pass HTTP "raw", without any transfer decoding (H)
 -e/--referer       Referer URL (H)
 -O/--remote-name   Write output to a file named as the remote file
    --remote-name-all Use the remote file name for all URLs
 -R/--remote-time   Set the remote file's time on the local output
 -X/--request <command> Specify request command to use
    --retry <num>   Retry request <num> times if transient problems occur
    --retry-delay <seconds> When retrying, wait this many seconds between each
    --retry-max-time <seconds> Retry only within this period
 -S/--show-error    Show error. With -s, make curl show errors when they occur
 -s/--silent        Silent mode. Don't output anything
    --socks4 <host[:port]> SOCKS4 proxy on given host + port
    --socks4a <host[:port]> SOCKS4a proxy on given host + port
    --socks5 <host[:port]> SOCKS5 proxy on given host + port
    --socks5-hostname <host[:port]> SOCKS5 proxy, pass host name to proxy
    --socks5-gssapi-service <name> SOCKS5 proxy service name for gssapi
    --socks5-gssapi-nec  Compatibility with NEC SOCKS5 server
 -Y/--speed-limit   Stop transfer if below speed-limit for 'speed-time' secs
 -y/--speed-time    Time needed to trig speed-limit abort. Defaults to 30
 -2/--sslv2         Use SSLv2 (SSL)
 -3/--sslv3         Use SSLv3 (SSL)
    --stderr <file> Where to redirect stderr. - means stdout
    --tcp-nodelay   Use the TCP_NODELAY option
 -t/--telnet-option <OPT=val> Set telnet option
 -z/--time-cond <time> Transfer based on a time condition
 -1/--tlsv1         Use TLSv1 (SSL)
    --trace <file>  Write a debug trace to the given file
    --trace-ascii <file> Like --trace but without the hex output
    --trace-time    Add time stamps to trace/verbose output
 -T/--upload-file <file> Transfer <file> to remote site
    --url <URL>     Set URL to work with
 -B/--use-ascii     Use ASCII/text transfer
 -u/--user <user[:password]> Set server user and password
 -A/--user-agent <string> User-Agent to send to server (H)
 -v/--verbose       Make the operation more talkative
 -V/--version       Show version number and quit
 -w/--write-out <format> What to output after completion
 -q                 If used as the first parameter disables .curlrc
EOS;

  $html = htmlentities($html);
  print<<<EOS
<pre>
$html
</pre>
EOS;

  pagetail();
  exit;
}


  //dd($form);

  $o = array();

  // -i
  if ($form['op_i']) $o[] = "-i";;

  // -X
  $op_X = $form['op_X'];
  $o[] = "-X $op_X";

  // -H
  $max_headers = 2;
  for ($i = 0; $i < $max_headers; $i++) {
    $key = "op_H{$i}";
    $value = $form[$key];
    $o[] = "-H \"$value\"";
  }

  // -d
  $op_d = $form['op_d'];
  $o[] = "-d '$op_d'";

  $options = join(" ", $o);

  $url = $form['url'];
  $command = "/usr/bin/curl $options $url";

  dd($command);

  $ret = exec($command, $output, $retval);
  //dd($output);

  $header = array();
  $body = array();
  if ($form['op_i']) {
    split_head_body($output, $header, $body);
  } else {
    $body = $output;
  }


  dd($header);

  if ($form['pp_json']) {
    //dd($output);
    $json = $body[0];
    $info = json_decode($json, true);
    dd($info);

  } else {

    dd($body);
  }


  pagetail();
  exit;

?>
