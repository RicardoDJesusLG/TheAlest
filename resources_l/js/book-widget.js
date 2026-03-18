(()=>{var o=new Date;function V(l){var d=new Date;return d.setFullYear(l.y),d.setMonth(l.m),d.setDate(l.d),d}var _=` 


<div class="col linea">
  <span>Arrival</span>
  <div class="date">
    <a href="#">
      <div class="numero" style='width:50px;'>26</div>
      <div class="mes">Sep <img src="resources/img/arrow.png" alt="date picker">
      </div>
    </a>
  </div>
</div>
<div class="col linea">
  <span>Departure</span>
  <div class="date">
    <a href="#">
      <div class="numero" style='width:50px;'>28</div>
      <div class="mes">Sep
        <img src="resources/img/arrow.png" alt="date picker">
      </div>
    </a>
  </div>
</div>
<div class="col linea">
  <span>Adults</span>
  <div class="pax">
    <a href="#" class='hidden-xs-'><img src="resources/img/menos.png" alt="less"></a>
    <div class="numero" >2</div>
  <!--  <a href="#"  class='visible-inline-block-xs'><img src="resources/img/menos.png" alt="less"></a>-->
    <a href="#"><img src="resources/img/mas.png" alt="more"></a>
  </div>
</div>
<div class="col">
  <span>Children</span>
  <div class="pax">
    <a href="#"  class='hidden-xs-'><img src="resources/img/menos.png" alt="less"></a>
    <div class="numero" >0</div>
    <!--<a href="#"  class='visible-inline-block-xs'><img src="resources/img/menos.png" alt="less"></a>-->
  
    <a href="#"><img src="resources/img/mas.png" alt="more"></a>
  </div>
</div> 



`;(function(){let l,d,i={datein:"",dateout:"",adults:2,children:0},C;if(i.datein==""&&(i.datein=`${o.getDate()}/${o.getMonth()+1}/${o.getFullYear()}`),i.dateout==""){let p=i.datein.split("/"),f=V({d:p[0],m:Number(p[1])-1,y:p[2]});f.setDate(f.getDate()+1),i.dateout=`${f.getDate()}/${f.getMonth()+1}/${f.getFullYear()}`}window.addEventListener("DOMContentLoaded",p=>{h()});function h(){if(l=document.querySelector(".book-widget"),d=$(".calendarBox .form-hit"),d.click(z),l?l.innerHTML=_:l=document.querySelector(".book-widget2"),!l)return;let[p,f]=[[...l.querySelectorAll(".date")][0],[...l.querySelectorAll(".date")][1]],[y,c]=[[...l.querySelectorAll(".pax")][0],[...l.querySelectorAll(".pax")][1]];p.addEventListener("click",L),f.addEventListener("click",T),[...c.querySelectorAll("a")][1].addEventListener("click",M),[...c.querySelectorAll("a")][0].addEventListener("click",D),[...y.querySelectorAll("a")][1].addEventListener("click",b),[...y.querySelectorAll("a")][0].addEventListener("click",k),y.querySelector(".numero").addEventListener("click",e=>{b(e)}),c.querySelector(".numero").addEventListener("click",M),l.setAttribute("data-datein",i.datein),l.setAttribute("data-dateout",i.dateout),N(),setTimeout(()=>{m()},1e3);function m(){let e=0,t=[...l.querySelectorAll(".col")];u();function u(){if(t[e].style.opacity=1,e++,e>=t.length){$(".check-av").velocity({opacity:1},600);return}setTimeout(()=>{u()},150)}}function L(e){e&&e.preventDefault(),C="in",F({key:"datein",label:"Arrival Date",date:l.getAttribute("data-datein")||""})}function T(e){e&&e.preventDefault(),C="out",F({key:"dateout",label:"Departure Date",date:l.getAttribute("data-dateout")||""})}function M(e){e&&e.preventDefault();let t=Number(c.querySelector(".numero").innerHTML);t++,t>5&&(t=0),c.querySelector(".numero").innerHTML=t,l.setAttribute("data-children",t),i.children=t,A(i),$("input[name='children']").val(t)}function D(e){e&&e.preventDefault();let t=Number(c.querySelector(".numero").innerHTML);t<1||(t--,c.querySelector(".numero").innerHTML=t,l.setAttribute("data-children",t),i.children=t,A(i),$("input[name='children']").val(t))}function b(e){e&&e.preventDefault();let t=Number(y.querySelector(".numero").innerHTML);t++,t>6&&(t=1),y.querySelector(".numero").innerHTML=t,l.setAttribute("data-adults",t),i.adults=t,A(i),$("input[name='adults']").val(t)}function k(e){e&&e.preventDefault();let t=Number(y.querySelector(".numero").innerHTML);t<2||(t--,y.querySelector(".numero").innerHTML=t,l.setAttribute("data-adults",t),i.adults=t,A(i),$("input[name='adults']").val(t))}function N(){g(),l.setAttribute("data-datein",i.datein),l.setAttribute("data-dateout",i.dateout);let[e,t]=[i.datein.split("/")[0],B[Number(i.datein.split("/")[1])-1]],[u,S]=[i.dateout.split("/")[0],B[Number(i.dateout.split("/")[1])-1]];p.querySelector(".numero").innerHTML=e,p.querySelector(".mes").innerHTML=t+'<img src="resources/img/arrow.png" alt="date picker">',f.querySelector(".numero").innerHTML=u,f.querySelector(".mes").innerHTML=S+'<img src="resources/img/arrow.png" alt="date picker">',$(".calendarBox").velocity({opacity:0},400,function(){$(".calendarBox").hide()}),$("input[name='datein']").val(i.datein),$("input[name='dateout']").val(i.dateout),$("input[name='adults']").val(i.adults),$("input[name='children']").val(i.children)}function g(){let[e,t]=[H(i.datein),H(i.dateout)];e>=t&&(i.dateout=Y(E(e,1)))}function E(e,t){return new Date(e.getTime()+t*864e5)}function H(e){let[t,u,S]=e.split("/"),w=new Date;return w.setFullYear(S),w.setMonth(u-1),w.setDate(t),w}function Y(e){return`${e.getDate()}/${e.getMonth()+1}/${e.getFullYear()}`}function z(){$(".calendarBox").velocity({opacity:0},400,function(){$(".calendarBox").hide()})}function F(e){let t=document.getElementById("calendar");t.innerHTML="",$(".calendarBox").css({opacity:0,display:"block"}),requestAnimationFrame(()=>{window.dnaCalendar($(t),u=>{l.setAttribute("data-"+e.key,u.d+"/"+(1+u.m)+"/"+u.y),i[e.key]=u.d+"/"+(1+u.m)+"/"+u.y,N()},e.label,e.date,i),$(".calendarBox").velocity({opacity:1},400)})}}function A(p){sessionStorage.setItem("alest_book_obj",JSON.stringify(p))}})();var O,j="Enero Febrero Marzo Abril Mayo Junio Julio Agosto Septiembre Octubre Noviembre Diciembre".split(" "),P="Dom Lun Mar Mie Jue Vie Sab".split(" "),B="Ene Feb Mar Abr May Jun Jul Ago Sep Oct Nov Dic".toUpperCase().split(" ");j="January February March April May June July August September October November December".split(" ");B="Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec".toUpperCase().split(" ");var K="Monday Tuesday Wednesday Thursday Friday Saturday Sunday".split(" ");P="Mon Tue Wed Thu Fri Sat Sun".split(" ");$(function(){var l="fixed";window.dnaCalendar=function(d,i,C,h,A){var p,f,y,c=[];if(h=="")h={m:o.getMonth(),y:o.getFullYear(),d:o.getDate(),date:new Date(o)};else{let a=h.split("/");h={m:Number(a[1])-1,y:a[2],d:a[0],date:new Date}}var m,L,T,M,D,b,k,N,g;O?(L=O.clone(),E()):(L=$(U),O=$(U),E());function E(){c=[];var a={d:h.d,m:h.m,y:h.y};if(g){var s=S(a),n=S(g);w(s,o)<=w(n,o)&&(g=a)}else g=a;c.push({date:a.d+"-"+a.m+"-"+a.y,label:"",link:"",day:a.d,month:a.m,year:a.y}),d.html(L.clone()),p=d.find(".calendarLabel"),T=d.find(".dcHead p"),T.click(F),M=d.find(".dcHead img"),D=d.find(".dcDays"),M.eq(0).click(Y),M.eq(1).click(z),d.find(".dcHead .dcWeekDays span").css({width:d.width()/7+"px"}),c.length>0&&g?e(g.m,g.y):e(o.getMonth(),o.getFullYear()),$(window).resize(H),H()}function H(){var a=D.find("span"),s=d.width()/7,n=parseInt(s-16)/2;a.css({width:s+"px",height:s+"px","padding-top":n+"px","padding-bottom":n+"px"}),d.find(".dcHead .dcWeekDays span").css({width:d.width()/7+"px"})}function Y(a){a&&a.preventDefault();var s=k,n=b-1;n<0&&(n=11,s--),e(n,s)}function z(a){a&&a.preventDefault();var s=k,n=b+1;n>=12&&(n=0,s++),e(n,s)}function F(a){a&&a.preventDefault(),e(b,k)}function e(a,s){C&&p&&p.html(C),b=parseInt(a),k=parseInt(s),m=u(s,a),D.html("");for(var n=0;n<m.length;n++){if(console.log(JSON.stringify(m[n],!1,4)),n==0){var r=m[n].s;r==0&&(r=7);for(var v=0;v<r-1;v++)D.append("<span class='dcEmpty'></span>")}D.append("<span data-id='"+n+"'>"+m[n].d+"</span>")}var J=D.find("span"),W=d.width()/7;J.css({width:W+"px",height:W+"px"}),T.html(j[b]+" "+k),c?J.each(function(){if(!$(this).hasClass("dcEmpty")){for(var x=m[$(this).attr("data-id")],I=!1,q=0;q<c.length;q++)x.d==c[q].day&&x.m==c[q].month&&x.y==c[q].year&&(I=!0);x.y<=o.getFullYear()&&x.m<=o.getMonth()&&(x.m<o.getMonth()?$(this).addClass("inactive"):x.d<o.getDate()&&$(this).addClass("inactive")),I&&$(this).addClass("active"),$(this).click(t)}}):J.each(function(){if(!$(this).hasClass("dcEmpty")){var x=m[$(this).attr("data-id")];$(this).click(t)}})}function t(a){if(a&&a.preventDefault(),!$(this).hasClass("inactive")){var s=$(this).attr("data-id"),n=m[s],r=new Date;r.setFullYear(n.y),r.setMonth(n.m),r.setDate(n.d),n.date=r,i&&i(n)}}function u(a,s){var n=[],r=new Date;r.setFullYear(a),r.setDate(1),r.setMonth(s),n.push({d:r.getDate(),s:r.getDay(),m:r.getMonth(),y:r.getFullYear(),live:r>=o,date:r.getDate()+"-"+r.getMonth()+"-"+r.getFullYear()});for(var v=1;v<31;v++)r.setDate(r.getDate()+1),r.getMonth()==s&&n.push({m:r.getMonth(),y:r.getFullYear(),d:r.getDate(),s:r.getDay(),live:r>=o,date:r.getDate()+"-"+r.getMonth()+"-"+r.getFullYear()});return n}function S(a){var s=new Date;return s.setFullYear(a.y),s.setMonth(a.m),s.setDate(a.d),s}function w(a,s){var n=Math.floor((s-a)/864e5);return n=Math.abs(n),console.log("distancia",n),n}function G(a,s,n,r){n||(n={}),$.post(a,n).done(function(v){s(v)}).error(function(v){r?r(v):console.log(v)})}}});var U=`
<style type="text/css">
	.calendarBox {
		position: fixed;
 		display: none;
		 top:0px;
		 left:0px;
		border: 1px solid #000000;
		padding: 40px;
		z-index: 210;
 width:100%;
 height:100%;
 opacity:0;
		background-color: #000000ef;
		-webkit-user-select: none;  /* Chrome all / Safari all */
    -moz-user-select: none;     /* Firefox all */
    -ms-user-select: none;      /* IE 10+ */
    user-select: none; 
	}
.form-hit{
opacity:0;
position:absolute;
top:0;left:0;right:0;bottom: 0;
}
	.dnaCalendar {
		position: relative;
		margin:10% auto;
		width: 100%;
		min-width: 280px;
		max-width: 420px;
		margin-bottom: 20px;
 	}

	.dnaCalendar .dcHead {
		position: relative;
		width: 100%;
	}

	.dnaCalendar .dcHead img {
		position: absolute;
		width: 24px;
		top: 0;
		cursor: pointer;
		opacity: 0.7;
		vertical-align: middle;
		margin-top: 4px;
	}

	.dnaCalendar .dcHead img:hover {
		opacity: 1;
	}

	.dnaCalendar .dcHead img:nth-child(1) {
		left: 20px;
	}

	.dnaCalendar .dcHead img:nth-child(2) {
		right: 20px;
	}

	.dnaCalendar .dcHead p {
		text-align: center;
		font-size: 14px;
		font-weight: 400;
		color: #ffffff;
		cursor: pointer;
	}


	.dnaCalendar .dcHead .dcWeekDays {
		position: relative;
		font-size: 0;
		margin-bottom: 10px;
	}

	.dnaCalendar .dcHead .dcWeekDays span {
		font-size: 10px;
		display: inline-block;
		text-align: center;
		font-weight: 700;
		color: #ffffff;
		text-transform: uppercase;
	}

	.dnaCalendar .dcDays {
		position: relative;
		font-size: 0;
	}

	.dnaCalendar .dcDays span {
		font-size: 14px;
		display: inline-block;
		text-align: center;
		cursor: pointer;
		padding: 12px 0 10px 0;
		-webkit-transition: all 0.4s ease;
		-moz-transition: all 0.4s ease;
		-o-transition: all 0.4s ease;
		transition: all 0.4s ease;

		color: #969696;
		vertical-align: middle; 

	}

	.dnaCalendar .dcDays span.dcEmpty {
		opacity: 0;
		cursor: default;
		user-select: none;
	}

	.dnaCalendar .dcDays span:hover {
		/*background-color: #555555;*/
		background-color: #222222;
		color: #ffffff;
	}

	.dnaCalendar .dcDays span.inactive {
		/*background-color: #c7a86f;*/
		color: #555555;
				 cursor: auto;
	}
	.dnaCalendar .dcDays span.inactive:hover {
		background-color: #00000000;
		color: #555555;
				 cursor: auto;
	}


	.dnaCalendar .dcDays span.active {
		background-color: #222222;
		color: #c7a86f;
				 cursor: pointer;
				 font-weight: 600;
	}

	.dnaCalendar .dcDays span.active:hover {
	/*	background-color: #dddddd;*/
	background-color: #111111;
color:#c7a86f;
	}

	.dnaCalendar .dcDays .dcEvents {
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		min-height: 100%;
		background-color: white;
		font-size: 16px;
		color: #969696;
	}

	.dnaCalendar .dcDays .dcEvents p {
		font-size: 16px;
		color: #c7a86f;

		font-weight: 700;
	}

	.dnaCalendar .dcDays .dcEvents .dcELine {
		padding: 13px;

		color: #c7a86f;
		font-size: 11px;
		font-weight: 700;
		display: block;
		margin-bottom: 5px;

	}

	.dnaCalendar .dcDays .dcEvents .dcELine a {
		text-decoration: none;
		color: #c7a86f;
		font-size: 15px;
	}

	.dnaCalendar .dcDays .dcEvents .dcELine a:hover {
		text-decoration: none;

		color: #c7a86f;

	}
	.calendarLabel{
		color:white;
		font-size: 24px;
		text-align: center;
		margin-bottom: 16px;
		text-align: center;

	}
</style>




<div class='dcdiv'>
	<div class='calendarLabel'></div>
	<div class='dcHead'>
		<img src='resources/dnaCalendar/prev.svg'>
		<img src='resources/dnaCalendar/next.svg'>
		<p>month</p> <br>
		<div class='dcWeekDays'>
			<span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span>
		
<!--		<span>Dom</span><span>Lun</span><span>Mar</span><span>Mie</span><span>Jue</span><span>Vie</span><span>Sab</span>-->

	</div>
	</div>
	<div class='dcDays'>
	</div>
</div>
`;})();
