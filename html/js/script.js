// Encoding: UTF-8

// 한글
function wopen(url, width, height, scrollbars, resizable) {
  option = "width="+width
          +",height="+height
          +",scrollbars="+scrollbars
          +",resizable="+resizable;
          //+",status="+status; 
  open(url, '', option);
}
function script_wopen(url, width, height, scrollbars, resizable) {
  option = "width="+width
          +",height="+height
          +",scrollbars="+scrollbars
          +",resizable="+resizable;
          //+",status="+status; 
  open(url, '', option);
}

// wopen with name
function wopen2(url, name, width, height, scrollbars, resizable) {
  option = "width="+width
          +",height="+height
          +",scrollbars="+scrollbars
          +",resizable="+resizable;
          //+",status="+status; 
  return window.open(url, name, option);
}


function script_Go(url) {
  document.location = url;
}

function script_Question(url, msg) {
  if (confirm(msg)) document.location = url;
  else return;
}



