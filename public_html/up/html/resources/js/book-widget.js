(()=>{var s=new Date;function V(o){var l=new Date;return l.setFullYear(o.y),l.setMonth(o.m),l.setDate(o.d),l}var _=` 


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
    <a href="#"><img src="resources/img/menos.png" alt="less"></a>
    <div class="numero" style='width:50px;'>2</div>
    <a href="#"><img src="resources/img/mas.png" alt="more"></a>
  </div>
</div>
<div class="col">
  <span>Children</span>
  <div class="pax">
    <a href="#"><img src="resources/img/menos.png" alt="less"></a>
    <div class="numero" style='width:50px;'>0</div>
    <a href="#"><img src="resources/img/mas.png" alt="more"></a>
  </div>
</div> 



`;(function(){let o,l,r={datein:"",dateout:"",adults:2,children:0},C;if(r.datein==""&&(r.datein=`${s.getDate()}/${s.getMonth()+1}/${s.getFullYear()}`),r.dateout==""){let p=r.datein.split("/"),f=V({d:p[0],m:Number(p[1])-1,y:p[2]});f.setDate(f.getDate()+1),r.dateout=`${f.getDate()}/${f.getMonth()+1}/${f.getFullYear()}`}window.addEventListener("DOMContentLoaded",p=>{v()});function v(){if(o=document.querySelector(".book-widget"),l=$(".calendarBox .form-hit"),l.click(z),o?o.innerHTML=_:o=document.querySelector(".book-widget2"),!o)return;let[p,f]=[[...o.querySelectorAll(".date")][0],[...o.querySelectorAll(".date")][1]],[g,c]=[[...o.querySelectorAll(".pax")][0],[...o.querySelectorAll(".pax")][1]];p.addEventListener("click",L),f.addEventListener("click",T),[...c.querySelectorAll("a")][1].addEventListener("click",k),[...c.querySelectorAll("a")][0].addEventListener("click",D),[...g.querySelectorAll("a")][1].addEventListener("click",b),[...g.querySelectorAll("a")][0].addEventListener("click",w),g.querySelector(".numero").addEventListener("click",e=>{b(e)}),c.querySelector(".numero").addEventListener("click",k),o.setAttribute("data-datein",r.datein),o.setAttribute("data-dateout",r.dateout),Y(),setTimeout(()=>{h()},1e3);function h(){let e=0,t=[...o.querySelectorAll(".col")];u();function u(){if(t[e].style.opacity=1,e++,e>=t.length){$(".check-av").velocity({opacity:1},600);return}setTimeout(()=>{u()},150)}}function L(e){e&&e.preventDefault(),C="in",F({key:"datein",label:"Arrival Date",date:o.getAttribute("data-datein")||""})}function T(e){e&&e.preventDefault(),C="out",F({key:"dateout",label:"Departure Date",date:o.getAttribute("data-dateout")||""})}function k(e){e&&e.preventDefault();let t=Number(c.querySelector(".numero").innerHTML);t++,t>5&&(t=0),c.querySelector(".numero").innerHTML=t,o.setAttribute("data-children",t),r.children=t,A(r),$("input[name='children']").val(t)}function D(e){e&&e.preventDefault();let t=Number(c.querySelector(".numero").innerHTML);t<1||(t--,c.querySelector(".numero").innerHTML=t,o.setAttribute("data-children",t),r.children=t,A(r),$("input[name='children']").val(t))}function b(e){e&&e.preventDefault();let t=Number(g.querySelector(".numero").innerHTML);t++,t>6&&(t=1),g.querySelector(".numero").innerHTML=t,o.setAttribute("data-adults",t),r.adults=t,A(r),$("input[name='adults']").val(t)}function w(e){e&&e.preventDefault();let t=Number(g.querySelector(".numero").innerHTML);t<2||(t--,g.querySelector(".numero").innerHTML=t,o.setAttribute("data-adults",t),r.adults=t,A(r),$("input[name='adults']").val(t))}function Y(){y(),o.setAttribute("data-datein",r.datein),o.setAttribute("data-dateout",r.dateout);let[e,t]=[r.datein.split("/")[0],O[Number(r.datein.split("/")[1])-1]],[u,S]=[r.dateout.split("/")[0],O[Number(r.dateout.split("/")[1])-1]];p.querySelector(".numero").innerHTML=e,p.querySelector(".mes").innerHTML=t+'<img src="resources/img/arrow.png" alt="date picker">',f.querySelector(".numero").innerHTML=u,f.querySelector(".mes").innerHTML=S+'<img src="resources/img/arrow.png" alt="date picker">',$(".calendarBox").velocity({opacity:0},400,function(){$(".calendarBox").hide()}),$("input[name='datein']").val(r.datein),$("input[name='dateout']").val(r.dateout),$("input[name='adults']").val(r.adults),$("input[name='children']").val(r.children)}function y(){let[e,t]=[H(r.datein),H(r.dateout)];e>=t&&(r.dateout=N(E(e,1)))}function E(e,t){return new Date(e.getTime()+t*864e5)}function H(e){let[t,u,S]=e.split("/"),M=new Date;return M.setFullYear(S),M.setMonth(u-1),M.setDate(t),M}function N(e){return`${e.getDate()}/${e.getMonth()+1}/${e.getFullYear()}`}function z(){$(".calendarBox").velocity({opacity:0},400,function(){$(".calendarBox").hide()})}function F(e){let t=document.getElementById("calendar");t.innerHTML="",$(".calendarBox").css({opacity:0,display:"block"}),requestAnimationFrame(()=>{window.dnaCalendar($(t),u=>{o.setAttribute("data-"+e.key,u.d+"/"+(1+u.m)+"/"+u.y),r[e.key]=u.d+"/"+(1+u.m)+"/"+u.y,Y()},e.label,e.date,r),$(".calendarBox").velocity({opacity:1},400)})}}function A(p){sessionStorage.setItem("alest_book_obj",JSON.stringify(p))}})();var B,j="Enero Febrero Marzo Abril Mayo Junio Julio Agosto Septiembre Octubre Noviembre Diciembre".split(" "),P="Dom Lun Mar Mie Jue Vie Sab".split(" "),O="Ene Feb Mar Abr May Jun Jul Ago Sep Oct Nov Dic".toUpperCase().split(" ");j="January February March April May June July August September October November December".split(" ");O="Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec".toUpperCase().split(" ");var K="Monday Tuesday Wednesday Thursday Friday Saturday Sunday".split(" ");P="Mon Tue Wed Thu Fri Sat Sun".split(" ");$(function(){var o="fixed";window.dnaCalendar=function(l,r,C,v,A){var p,f,g,c=[];if(v=="")v={m:s.getMonth(),y:s.getFullYear(),d:s.getDate(),date:new Date(s)};else{let a=v.split("/");v={m:Number(a[1])-1,y:a[2],d:a[0],date:new Date}}var h,L,T,k,D,b,w,Y,y;B?(L=B.clone(),E()):(L=$(U),B=$(U),E());function E(){c=[];var a={d:v.d,m:v.m,y:v.y};if(y){var d=S(a),n=S(y);M(d,s)<=M(n,s)&&(y=a)}else y=a;c.push({date:a.d+"-"+a.m+"-"+a.y,label:"",link:"",day:a.d,month:a.m,year:a.y}),l.html(L.clone()),p=l.find(".calendarLabel"),T=l.find(".dcHead p"),T.click(F),k=l.find(".dcHead img"),D=l.find(".dcDays"),k.eq(0).click(N),k.eq(1).click(z),l.find(".dcHead .dcWeekDays span").css({width:l.width()/7+"px"}),c.length>0&&y?e(y.m,y.y):e(s.getMonth(),s.getFullYear()),$(window).resize(H),H()}function H(){var a=D.find("span"),d=l.width()/7,n=parseInt(d-16)/2;a.css({width:d+"px",height:d+"px","padding-top":n+"px","padding-bottom":n+"px"}),l.find(".dcHead .dcWeekDays span").css({width:l.width()/7+"px"})}function N(a){a&&a.preventDefault();var d=w,n=b-1;n<0&&(n=11,d--),e(n,d)}function z(a){a&&a.preventDefault();var d=w,n=b+1;n>=12&&(n=0,d++),e(n,d)}function F(a){a&&a.preventDefault(),e(b,w)}function e(a,d){C&&p&&p.html(C),b=parseInt(a),w=parseInt(d),h=u(d,a),D.html("");for(var n=0;n<h.length;n++){if(n==0)for(var i=h[n].s,m=0;m<i;m++)D.append("<span class='dcEmpty'></span>");D.append("<span data-id='"+n+"'>"+h[n].d+"</span>")}var J=D.find("span"),W=l.width()/7;J.css({width:W+"px",height:W+"px"}),T.html(j[b]+" "+w),c?J.each(function(){if(!$(this).hasClass("dcEmpty")){for(var x=h[$(this).attr("data-id")],I=!1,q=0;q<c.length;q++)x.d==c[q].day&&x.m==c[q].month&&x.y==c[q].year&&(I=!0);x.y<=s.getFullYear()&&x.m<=s.getMonth()&&(x.m<s.getMonth()?$(this).addClass("inactive"):x.d<s.getDate()&&$(this).addClass("inactive")),I&&$(this).addClass("active"),$(this).click(t)}}):J.each(function(){if(!$(this).hasClass("dcEmpty")){var x=h[$(this).attr("data-id")];$(this).click(t)}})}function t(a){if(a&&a.preventDefault(),!$(this).hasClass("inactive")){var d=$(this).attr("data-id"),n=h[d],i=new Date;i.setFullYear(n.y),i.setMonth(n.m),i.setDate(n.d),n.date=i,r&&r(n)}}function u(a,d){var n=[],i=new Date;i.setFullYear(a),i.setDate(1),i.setMonth(d),n.push({d:i.getDate(),s:i.getDay(),m:i.getMonth(),y:i.getFullYear(),live:i>=s,date:i.getDate()+"-"+i.getMonth()+"-"+i.getFullYear()});for(var m=1;m<31;m++)i.setDate(i.getDate()+1),i.getMonth()==d&&n.push({m:i.getMonth(),y:i.getFullYear(),d:i.getDate(),s:i.getDay(),live:i>=s,date:i.getDate()+"-"+i.getMonth()+"-"+i.getFullYear()});return n}function S(a){var d=new Date;return d.setFullYear(a.y),d.setMonth(a.m),d.setDate(a.d),d}function M(a,d){var n=Math.floor((d-a)/864e5);return n=Math.abs(n),console.log("distancia",n),n}function G(a,d,n,i){n||(n={}),$.post(a,n).done(function(m){d(m)}).error(function(m){i?i(m):console.log(m)})}}});var U=`
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
