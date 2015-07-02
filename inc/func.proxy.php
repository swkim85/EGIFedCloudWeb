<?php

function get_cert_info_subject($certpath, $debug=false) {
  global $conf;
  $openssl = $conf['openssl'];
  $command = "$openssl x509 -in $certpath -noout -subject";
  if ($debug) dd($command);
  exec($command, $output, $retval);
  if ($debug) dd($output);

  $info = array();
  foreach ($output as $line) {
    if (preg_match("/subject= /", $line)) {
      list($a, $b) = preg_split("/=/", $line, 2);
      $subject = trim($b);
      $info['subject'] = $subject;
    }
  }

  return $info;
}

function get_cert_info_valid($certpath, $debug=false) {
  global $conf;
  $openssl = $conf['openssl'];
  $command = "$openssl x509 -in $certpath -noout -dates";
  if ($debug) dd($command);
  exec($command, $output, $retval);
  if ($debug) dd($output);

  $info = array();
  foreach ($output as $line) {
    if (preg_match("/notBefore/", $line)) {
      list($a, $b) = preg_split("/=/", $line, 2);
      $info['notbefore'] = trim($b);

    } else if (preg_match("/notAfter/", $line)) {
      list($a, $b) = preg_split("/=/", $line, 2);
      $info['notafter'] = trim($b);
    }
  }

  return $info;
}

function cert_and_key_path() {
  $user = get_username();
  global $conf;
  $dir = $conf['PROXY_DIR'];
  $userpath = "$dir/$user";
  $certpath = "$userpath/usercert.pem";
  $keypath  = "$userpath/userkey.pem";
  return array($certpath, $keypath);
}
function proxy_path() {
  $user = get_username();
  global $conf;
  $dir = $conf['PROXY_DIR'];
  $userpath = "$dir/$user";
  $proxypath = "$userpath/proxy.pem";
  return $proxypath;
}


function cert_info() {
  $user = get_username();
  global $conf;
  $dir = $conf['PROXY_DIR'];
  $userpath = "$dir/$user";

  $certpath = "$userpath/usercert.pem";
  $info1 = get_cert_info_subject($certpath, $debug=false);
  $info2 = get_cert_info_valid($certpath, $debug=false);
  $info = array_merge($info1, $info2);
  //dd($info);

  $subject = $info['subject'];
  //$_SESSION['subject'] = $subject;

  print<<<EOS
Certificate: $subject

EOS;
}

/*
voms-proxy-init: 
    Options
    -help, -usage                  Displays usage
    -version                       Displays version
    -debug                         Enables extra debug output
    -quiet, -q                     Quiet mode, minimal output
    -verify                        Verifies certificate to make proxy for
    -pwstdin                       Allows passphrase from stdin
    -limited                       Creates a limited proxy
    -valid <h:m>                   Proxy and AC are valid for h hours and m minutes
                                   (defaults to 12:00)
    -hours H                       Proxy is valid for H hours (default:12)
    -bits                          Number of bits in key {512|1024|2048|4096}
    -cert     <certfile>           Non-standard location of user certificate
    -key      <keyfile>            Non-standard location of user key
    -certdir  <certdir>            Non-standard location of trusted cert dir
    -out      <proxyfile>          Non-standard location of new proxy cert
    -voms <voms<:command>>         Specify voms server. :command is optional,
                                   and is used to ask for specific attributes
                                   (e.g: roles)
    -order <group<:role>>          Specify ordering of attributes.
    -target <hostname>             Targets the AC against a specific hostname.
    -vomslife <h:m>                Try to get a VOMS pseudocert valid for h hours
                                   and m minutes (default to value of -valid).
    -include <file>                Include the contents of the specified file.
    -conf <file>                   Read options from <file>.
    -confile <file>                Non-standard location of voms server addresses. Deprecated
    -userconf <file>               Non-standard location of user-defined voms server addresses. Deprecated
    -vomses <file>                 Non-standard location of configuration files.
    -policy <policyfile>           File containing policy to store in the ProxyCertInfo extension.
    -pl, -policy-language <oid>    OID string for the policy language.
    -policy-language <oid>         OID string for the policy language.
    -path-length <l>               Allow a chain of at most l proxies to be generated from this ones.
    -globus <version>              Globus version. (MajorMinor)
    -proxyver                      Version of proxy certificate.
    -noregen                       Use existing proxy certificate to connect to server and sign the new proxy.
    -separate <file>               Saves the informations returned by the server on file <file>.
    -ignorewarn                    Ignore warnings.
    -failonwarn                    Treat warnings as errors.
    -list                          Show all available attributes.
    -rfc                           Creates RFC 3820 compliant proxy (synonymous with -proxyver 4)
    -old                           Creates GT2 compliant proxy (synonymous with -proxyver 2)
    -timeout <num>                 Timeout for server connections, in seconds.
    -includeac <file>              get AC from file.
    -dont-verify-ac                Skips AC verification.
*/
function generate_proxy($keypass, $debug=false) {

  list($certpath, $keypath) = cert_and_key_path();
  $proxypath = proxy_path();

  $command =<<<EOS
echo "$keypass" | /usr/bin/voms-proxy-init -voms fedcloud.egi.eu --rfc -dont-verify-ac -cert $certpath -key $keypath -out $proxypath -pwstdin
EOS;
  if ($debug) dd($command);

  exec($command, $output, $retval);
  //dd($retval);
  if ($retval == 3) return 'wrong password';

  if ($debug) dd($output);

  dump_output($output);
}

function dump_output($output) {
  print<<<EOS
<pre>
EOS;
  foreach ($output as $line) {
    print<<<EOS
$line

EOS;
  }
  print<<<EOS
</pre>
EOS;
}



/*
$ voms-proxy-info -help
voms-proxy-info: 

Syntax: voms-proxy-info [-help][-file proxyfile][-subject][...][-exists [-hours H][-bits B][-valid H:M]]

   Options
   -help, -usage             Displays usage
   -version                  Displays version
   -debug                    Displays debugging output
   -file <proxyfile>         Non-standard location of proxy
   -dont-verify-ac           Skips AC verification
   [printoptions]            Prints information about proxy and attribute certificate
   -exists [options]         Returns 0 if valid proxy exists, 1 otherwise
   -acexists <voname>        Returns 0 if AC exists corresponding to voname, 1 otherwise
   -conf <name>              Read options from file <name>
   -included                 Print included file

   [printoptions]
      -chain                Prints information about the whol proxy chain (CA excluded)
      -subject              Distinguished name (DN) of proxy subject
      -issuer               DN of proxy issuer (certificate signer)
      -identity             DN of the identity represented by the proxy
      -type                 Type of proxy (full or limited)
      -timeleft             Time (in seconds) until proxy expires
      -strength             Key size (in bits)
      -all                  All proxy options in a human readable format
      -text                 All of the certificate
      -path                 Pathname of proxy file
      -vo                   Vo name
      -fqan                 Attribute in FQAN format      -acsubject            Distinguished name (DN) of AC subject
      -acissuer             DN of AC issuer (certificate signer)
      -actimeleft           Time (in seconds) until AC expires
      -serial               AC serial number 
      -uri                  Server URI
      -keyusage             Print content of KeyUsage extension.

   [options to -exists]      (if none are given, H = B = 0 are assumed)
      -valid H:M            time requirement for proxy to be valid
      -hours H              time requirement for proxy to be valid (deprecated, use -valid instead)
      -bits  B              strength requirement for proxy to be valid
*/
function proxy_info($debug=false) {
  $proxypath = proxy_path();
  $command =<<<EOS
/usr/bin/voms-proxy-info -file $proxypath -all
EOS;
  if ($debug) dd($command);

  exec($command, $output, $retval);
  if ($debug) dd($output);
  dump_output($output);

}

// proxy 인증서 남은 시간 리턴 (단위는 초)
function get_proxy_info_timeleft($debug=false) {
  $proxypath = proxy_path();
  $command =<<<EOS
/usr/bin/voms-proxy-info -file $proxypath -timeleft
EOS;
  if ($debug) dd($command);

  exec($command, $output, $retval);
  $time = $output[0];
  return $time;

}



?>
