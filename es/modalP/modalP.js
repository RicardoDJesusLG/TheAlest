(function(){

var full,but,image;
var T=3000;
var CT=26500;
$(document).ready(initPop);

function initPop(){
setTimeout(showModal,T);
}

function showModal(e){
	if(e)e.preventDefault();

full=$("<div class='full' style='z-index:99999;opacity:0;position:fixed;top:0;left:0;right:0;bottom:0;background-color:rgba(0,0,0,0.0);'><div style='position:absolute;width:100%;height:100%;top:0;left:0;' class='popClose'></div></div>");
$("body").append(full);
full.animate({"opacity":1},600);

var src=window.POPUPIMG;
if(src)
loadImage(window.POPUPIMG,function(im){
		if(!full)return;
full.append(im);
if(CT){
	setTimeout(close,CT);
}

full.find(".popClose").click(close);
alignImg(im,full);

$(window).bind("resize",align);
im.animate({"opacity":1},600);
try{
if(window.POPUPLINK){
im.css("cursor","pointer");
im.click(function(){
	window.open(window.POPUPLINK);
});
}
}catch(e){}

image=im;
addBut();

});

}
function close(e){
	if(e)e.preventDefault();
	$(window).unbind("resize",align);
full.animate({opacity:0},600,function(){
full.html("");
	full.remove();
full=null;
});

}
function addBut(){
but=$("<div class='popBut'></div>");
but.css({
	"position":"absolute",
	"width":"25px","height":"25px",
	"background-image":"url(modalP/but.png)"
});
full.append(but);
but.click(close);
alignBut();
}

function align(){
alignImg(full.find("img").eq(0),full);
alignBut();	
}

function alignBut(){
	if(!full)return;
but.css({
"left":	((image.position().left+image.width())-25)+"px",
"top":	((image.position().top)-25)+"px",
"cursor":"pointer"});

}

function alignImg(im,cont){
		if(!full)return;
var iww=im.width();
var ihh=im.height();
ir=ihh/iww;
if(iww>$(window).width()*0.95){
	iww=$(window).width()*0.95;
	ihh=iww*ir;
}

  im.css({"top":((cont.height()/2)-(ihh/2))+"px",
  "left":((cont.width()/2)-(iww/2))+"px","width":iww+"px","height":ihh+"px"});

}

function loadImage(src,fn){

var ratio;
var loadedf;
var w,h,done;
var im;
im=$("<img style='position:absolute;top:-1000px;opacity:0;'/>");
$("body").append(im);

load(fn);

function load(fn){
	loadedf=fn;
im.load(iloaded);
im.attr('src', src);
}

function iloaded(){
done=true;
ratio=im.get(0).naturalHeight/im.get(0).naturalWidth;
im.attr("data-ratio",ratio);

loadedf(im);
}

 

}


})();