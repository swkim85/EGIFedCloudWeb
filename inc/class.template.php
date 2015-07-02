<?php

// class.template.php
// http://www.codeproject.com/Articles/312510/Building-a-Simple-PHP-Templating-Class


# // example)
# include('class.template.php');
# $page = new Template("template.html");
# $page->loadstr($template);
# $page->set("title", "Á¦¸ñ");
# $page->publish();

// template.html
/*
<html>
<meta charset="UTF-8">
    <head>
        <title>{title}</title>
    </head>
    <body>
        <div>Please Fill out the form below</div>
        <div id="form"> {form} </div>
    </body>
</html>
<form>
    Enter Bottle Color: <input type="text" name="color" />
    Enter Bottle Size: <input type="text" name="size" />
</form>
*/

class Template {

  private $template;
  function __construct($template = null) {
    if (isset($template)) {
      $this->load($template);
    }
  }

  public function load($file) {
    if (isset($file) && file_exists($file)) {
       $this->template = file_get_contents($file);
    }
  }

  public function loadstr($str) {
    $this->template = $str;
  }


  public function set($var, $content) {
    $this->template = str_replace("{" . "$var" . "}", $content, $this->template);
  }

  public function publish() {
    $this->removeEmpty();
    print $this->template;
  }

  private function removeEmpty() {
    $this->template = preg_replace('^{.*}^', "", $this->template);
  }

  public function parse() {
    $this->removeEmpty();
    return $this->template;
  }

}

?>
