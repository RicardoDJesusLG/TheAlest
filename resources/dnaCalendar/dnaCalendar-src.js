$(function(){

var MONTHS="Enero Febrero Marzo Abril Mayo Junio Julio Agosto Septiembre Octubre Noviembre Diciembre".split(" ");
var DAYS="Do Lu Ma Mi Ju Vi Sa".split(" ");
var MONTHSshort="Ene Feb Mar Abr May Jun Jul Ago Sep Oct Nov Dic".toUpperCase().split(" ");


	
$(".dnaCalendar").each(function(){dnaCalendar($(this))});


function dnaCalendar(div){
var W,margin,DATA=null;
var today=new Date();	
var monthdays;
var temp,monthTitle,arrows,daysDiv,amonth,ayear,aday;

loadData("resources/dnaCalendar/template.html",function(data){

temp=$(data);
init();

},{},function(err){
	console.log(err);

});


function init(){
var dat=div.find("event");
if(dat.length>0){
DATA=[];
dat.each(function(){
	var ev=$(this);
DATA.push({date:ev.attr("day")+"-"+ev.attr("month")+"-"+ev.attr("year"),
	label:ev.attr("label"),
	link:ev.attr("link"),
    day:ev.attr("day"),
    month:ev.attr("month"),
    year:ev.attr("year")
 });
});
//DATA=dat.clone();



}



div.html(temp.clone());
monthTitle=div.find(".dcHead p");
monthTitle.click(showActual);


arrows=div.find(".dcHead img");
daysDiv=div.find(".dcDays");

arrows.eq(0).click(prevMonth);
arrows.eq(1).click(nextMonth);

div.find(".dcHead .dcWeekDays span").css({width:(div.width()/7)+"px"});


showMonth(today.getMonth(),today.getFullYear());
$(window).resize(align);
}

function align(){
var spans=daysDiv.find("span");
var tw=div.width()/7;
spans.css({width:tw+"px"});
div.find(".dcHead .dcWeekDays span").css({width:(div.width()/7)+"px"});


}

function prevMonth(e){
if(e)e.preventDefault();
if(ayear<=today.getFullYear() && amonth<=today.getMonth() )return;
 
var y=ayear;
var m=amonth-1;

if(m<0){m=11;y--;}
showMonth(m,y);

}

function nextMonth(e){
if(e)e.preventDefault();
  
var y=ayear;
var m=amonth+1;

if(m>=12){m=0;y++;}
showMonth(m,y);

}

function showActual(e){
if(e)e.preventDefault();
  showMonth(amonth,ayear);

}


function showMonth(month,year){
	amonth=month;
	ayear=year;
monthdays=getDays(year,month);

daysDiv.html('');
for(var i=0;i<monthdays.length;i++){

if(i==0){
	var ds=monthdays[i].s;
	for(var ii=0;ii<ds;ii++){
daysDiv.append("<span class='dcEmpty'></span>");

	}
}


daysDiv.append("<span data-id='"+i+"'>"+monthdays[i].d+"</span>");

}

var spans=daysDiv.find("span");
var tw=div.width()/7;

spans.css({width:tw+"px"});
monthTitle.html(MONTHS[amonth]+" "+ayear);

if(DATA){
spans.each(function(){
	if($(this).hasClass("dcEmpty"))return;
var sdate=monthdays[$(this).attr("data-id")];
var active=false;
for(var i=0;i<DATA.length;i++){
if(sdate.d==DATA[i].day && sdate.m==DATA[i].month && sdate.y==DATA[i].year ){active=true;}

}
if(active){
	$(this).addClass('active');
$(this).click(showEvent);
}

});
}

}

function showEvent(e){
if(e)e.preventDefault();
var id=$(this).attr("data-id");
var sdate=monthdays[id];
var acts=[];


var evs=$("<div class='dcEvents'><br><br><p>"+sdate.d +" de "+MONTHS[sdate.m]+" de "+sdate.y+"</p></div>");

for(var i=0;i<DATA.length;i++){

if(sdate.d==DATA[i].day && sdate.m==DATA[i].month && sdate.y==DATA[i].year ){

evs.append("<div class='dcELine'>"+DATA[i].day +" "+MONTHSshort[DATA[i].month]+", "+DATA[i].year+"<br><a href='"+DATA[i].link+"'>"+DATA[i].label+"</a></div>")

}



}

//evs.append("<div class='dcELine'><a href='#'>&lt Volver al calendario</a></div>")
if(evs.find(".dcELine").length==1){
var lnk=evs.find(".dcELine").eq(0).find("a").attr("href");

//window.location.href=lnk;
//return;
}



daysDiv.append(evs);

}

function getDays(year, month) {
      var dArr = [];
 
      var d = new Date();

d.setFullYear(year);
d.setMonth(month);
d.setDate(1);

      dArr.push({
        d: d.getDate(),
        s: d.getDay(),
        m: d.getMonth(),
        y: d.getFullYear(),
        live: d >=today,
        date:d.getDate()+"-"+d.getMonth()+"-"+d.getFullYear()
      });

      for (var i = 1; i < 31; i++) {
        d.setDate(d.getDate() + 1);
        if (d.getMonth() == month) dArr.push({
          m: d.getMonth(),
          y: d.getFullYear(),
          d: d.getDate(),
          s: d.getDay(),
          live: (d >= today),
                  date:d.getDate()+"-"+d.getMonth()+"-"+d.getFullYear()
        })
      }
      return dArr
    }



}//dnaCalendar



//loadData("scr.php",function(data){},{com:"string"},function(err){});
function loadData(file,f,obj,err){
  if(!obj)obj={};
  $.post( file, obj)
  .done(function( data ) {
 
    f(data);//.children("div"));

  }).error(function(data){
if(err)err(data);else console.log(data)
    });
}







});