<?php

  include("path.php");
  include("$env[prefix]/inc/common.php");
  include("func.php");


  pagehead($pgtitle);
  ParagraphTitle($pgtitle);


  print<<<EOS
  <script>
  $(function() {
    $( "#speed" ).selectmenu();
 
    $( "#files" ).selectmenu();
 
    $( "#number" )
      .selectmenu()
      .selectmenu( "menuWidget" )
        .addClass( "overflow" );
  });
  </script>

  <style>
    fieldset {
      border: 0;
    }
    label {
      display: block;
      margin: 30px 0 0 0;
    }
    select {
      width: 200px;
    }
    .overflow {
      height: 200px;
    }
  </style>
</head>
<body>
 
<div class="demo">
 
<form action="#">
 
  <fieldset>
    <label for="speed">Select a speed</label>
    <select name="speed" id="speed">
      <option>Slower</option>
      <option>Slow</option>
      <option selected="selected">Medium</option>
      <option>Fast</option>
      <option>Faster</option>
    </select>
 
    <label for="files">Select a file</label>
    <select name="files" id="files">
      <optgroup label="Scripts">
        <option value="jquery">jQuery.js</option>
        <option value="jqueryui">ui.jQuery.js</option>
      </optgroup>
      <optgroup label="Other files">
        <option value="somefile">Some unknown file</option>
        <option value="someotherfile">Some other file with a very long option text</option>
      </optgroup>
    </select>
 
    <label for="number">Select a number</label>
    <select name="number" id="number">
      <option>1</option>
      <option selected="selected">2</option>
      <option>3</option>
      <option>4</option>
      <option>5</option>
      <option>6</option>
      <option>7</option>
      <option>8</option>
      <option>9</option>
      <option>10</option>
      <option>11</option>
      <option>12</option>
      <option>13</option>
      <option>14</option>
      <option>15</option>
      <option>16</option>
      <option>17</option>
      <option>18</option>
      <option>19</option>
    </select>
  </fieldset>
 
</form>
 
</div>
EOS;

  pagetail();
  exit;

?>
