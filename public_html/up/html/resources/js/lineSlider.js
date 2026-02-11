$(function(){
$(".specials").each(SpecialLine);

function SpecialLine(){
let div=$(this);
div.css({'position':"relative"});
let cont=div.find(".grid").eq(0);
if(cont.length<1)return;
let slides=cont.find("figure").clone();
let n=slides.length;

if(n==0){
  div.hide();
  return;
}
cont.removeClass('grid');
cont.removeClass('data-aos');
cont.removeClass('data-aos-duration');
div.find("nav a").each(function(i){
if(i==0)$(this).addClass("prevSlide");
if(i==1)$(this).addClass("nextSlide");


});

cont.addClass('swiper');
cont.html("<div class='swiper-wrapper'></div>");
let line=cont.find('.swiper-wrapper');
slides.addClass('swiper-slide');
line.append(slides);

let ww=$(window).width();
let c=ww<768?1:ww<992?2:ww<1400?3:4;
if(n<=c){

 
  div.find("nav a").hide();

if(n==1 && c>2){c=2;} else {c=n;}
} else {
  div.find("nav a").show();
}




var swiper = new Swiper(".specials .swiper", {
  on:{
    'resize':resize
  },
  navigation: {
    nextEl: ".specials .nextSlide",
    prevEl: ".specials .prevSlide",
  },
      slidesPerView: c,
      spaceBetween: 18,
      freeMode: true,
      
    });

 
function resize(){
  let ww=$(window).width();
let c=ww<768?1:ww<992?2:ww<1400?3:4;

if(n<=c){

 
  div.find("nav a").hide();

if(n==1 && c>2){c=2;} else {c=n;}
} else {
  div.find("nav a").show();
}


this.params.slidesPerView=c;
this.update();

console.log(this);
}
}


});