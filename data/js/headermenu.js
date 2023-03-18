var dialog = document.querySelector(".dialog");
var overlay = document.querySelector('#overlay');
pageHeight = document.documentElement.scrollHeight;
document.querySelector(
"#openDialog"
).onclick = function () {
dialog.hidden = false;
pageHeight = document.documentElement.scrollHeight;
overlay.style.height = String(pageHeight);
overlay.hidden = false;
var timer = setTimeout((function(val){return function(){overlay.style.opacity = val};})('0.1'), 100);
var timer2 = setTimeout((function(val){return function(){dialog.style.opacity = val};})('1'), 0);
}

document.querySelector(
'#overlay'
).onclick = function () {
dialog.hidden = true;
overlay.style.opacity = '0.0';
overlay.hidden = true;
}