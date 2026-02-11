var today = new Date();
//const template=`<div class="book-widget">


function DateObjToDate(o) {
    var d = new Date();

    d.setFullYear(o.y);
    d.setMonth(o.m);
    d.setDate(o.d);

    //console.log("from", o.d + "/" + o.m + "/" + o.y, "to:", d);

    return d;
}

const templateBook = ` 


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



` ;


(function () {
    let apdiv,formhit;
    let bookObj = {
        datein: '',
        dateout: '',
        adults: 2,
        children: 0
    };
let    actualDate;


    if (bookObj.datein == '') {
        bookObj.datein = `${today.getDate()}/${today.getMonth() + 1}/${today.getFullYear()}`;
    }
    if (bookObj.dateout == '') {
        let ar = bookObj.datein.split("/");
        let nd = DateObjToDate({ d: ar[0], m: Number(ar[1]) - 1, y: ar[2] });
        nd.setDate(nd.getDate() + 1);
        bookObj.dateout = `${nd.getDate()}/${nd.getMonth() + 1}/${nd.getFullYear()}`;
    }



    window.addEventListener('DOMContentLoaded', (event) => {
        init();
    });



    function init() {
        apdiv = document.querySelector('.book-widget');

        formhit=$(".calendarBox .form-hit");
        formhit.click(cerrar);

        if (apdiv) {       
             apdiv.innerHTML = templateBook; 
        } else {
            apdiv = document.querySelector('.book-widget2');
            }

            if (apdiv){} else return;


        let [datein, dateout] = [[...apdiv.querySelectorAll('.date')][0], [...apdiv.querySelectorAll('.date')][1]];

        let [adults, children] = [[...apdiv.querySelectorAll('.pax')][0], [...apdiv.querySelectorAll('.pax')][1]];

        //console.log(datein, dateout, children, adults);



        datein.addEventListener("click", openCalendarIn);
        dateout.addEventListener("click", openCalendarOut);



        [...children.querySelectorAll('a')][1].addEventListener("click", morechildren);
        [...children.querySelectorAll('a')][0].addEventListener("click", lesschildren);
        [...adults.querySelectorAll('a')][1].addEventListener("click", moreadults);
        [...adults.querySelectorAll('a')][0].addEventListener("click", lessadults);
        adults.querySelector('.numero').addEventListener("click", (e)=>{moreadults(e)});
        children.querySelector('.numero').addEventListener("click", morechildren);


        apdiv.setAttribute('data-datein', bookObj.datein);
        apdiv.setAttribute('data-dateout', bookObj.dateout);


        updateDates();

        setTimeout(() => {
            intro();
        }, 1000)




        function intro() {
            let c = 0;
            let cols = [...apdiv.querySelectorAll('.col')];
            showNext();
            function showNext() {
                cols[c].style.opacity = 1;
                c++;
                if (c >= cols.length) {
                    $(".check-av").velocity({ opacity: 1 }, 600);



                    return;
                }
                setTimeout(() => {
                    showNext();
                }, 150)
            }


        }


        function openCalendarIn(e) {
            if (e) e.preventDefault();
actualDate='in';
            Calendar({ key: 'datein', label: 'Arrival Date', date: apdiv.getAttribute('data-datein') || '' });

        }

        function openCalendarOut(e) {
            if (e) e.preventDefault();
            actualDate='out';
            Calendar({ key: 'dateout', label: 'Departure Date', date: apdiv.getAttribute('data-dateout') || '' });

        }



 

        function morechildren(e) {
            if (e) e.preventDefault();
            let a = Number(children.querySelector('.numero').innerHTML);
            a++;
            if (a > 5) a=0;
           
            children.querySelector('.numero').innerHTML = a;
            apdiv.setAttribute("data-children", a);
            bookObj.children = a;
            saveBook(bookObj);
 
            $("input[name='children']").val(a);

        }
        function lesschildren(e) {
            if (e) e.preventDefault();
            let a = Number(children.querySelector('.numero').innerHTML);
            if (a < 1) return;
            a--;
            children.querySelector('.numero').innerHTML = a;
            apdiv.setAttribute("data-children", a);
            bookObj.children = a;
            saveBook(bookObj);
            $("input[name='children']").val(a);
        }
        function moreadults(e) {
            if (e) e.preventDefault();
            let a = Number(adults.querySelector('.numero').innerHTML);
             a++;
            if (a > 6) a=1;


            adults.querySelector('.numero').innerHTML = a;
            apdiv.setAttribute("data-adults", a);
            bookObj.adults = a;
            saveBook(bookObj);
            $("input[name='adults']").val(a);
        }
        function lessadults(e) {
            if (e) e.preventDefault();
            let a = Number(adults.querySelector('.numero').innerHTML);
            if (a < 2) return;
            a--;
            adults.querySelector('.numero').innerHTML = a;
            apdiv.setAttribute("data-adults", a);
            bookObj.adults = a;
            saveBook(bookObj);
            $("input[name='adults']").val(a);
        }



        function updateDates() {

 checkdates();


            apdiv.setAttribute('data-datein', bookObj.datein);
            apdiv.setAttribute('data-dateout', bookObj.dateout);



            let [did, dim] = [bookObj.datein.split("/")[0], MONTHSshort[Number(bookObj.datein.split("/")[1]) - 1]];
            let [dod, dom] = [bookObj.dateout.split("/")[0], MONTHSshort[Number(bookObj.dateout.split("/")[1]) - 1]];

            datein.querySelector('.numero').innerHTML = did;
            datein.querySelector('.mes').innerHTML = dim + '<img src="resources/img/arrow.png" alt="date picker">';

            dateout.querySelector('.numero').innerHTML = dod;
            dateout.querySelector('.mes').innerHTML = dom + '<img src="resources/img/arrow.png" alt="date picker">';

            $(".calendarBox").velocity({ opacity: 0 }, 400, function () {
                $(".calendarBox").hide();
            });



            $("input[name='datein']").val(bookObj.datein);
            $("input[name='dateout']").val(bookObj.dateout);
            $("input[name='adults']").val(bookObj.adults);
            $("input[name='children']").val(bookObj.children);
            


        }


function checkdates(){
let [din,dout]=[strToDate(bookObj.datein),strToDate(bookObj.dateout)];
if(din>=dout){
    
    bookObj.dateout=dateToStr(daysAgo(din,1));
 }

}

function daysAgo(d,nd){

return    new Date((d).getTime() + (nd * 86400000))
}

function strToDate(str){
    let [dia,m,y]=str.split("/");
    let d=new Date();
    d.setFullYear(y);
    d.setMonth(m-1);
    d.setDate(dia);
return d;    
}
function dateToStr(d){

return `${d.getDate()}/${d.getMonth()+1}/${d.getFullYear()}`;

}


function cerrar(){

    $(".calendarBox").velocity({ opacity: 0 }, 400, function () {
        $(".calendarBox").hide();
    });

}


        //{ key: 'dateout', label: 'Select check-out date:', date: apdiv.getAttribute('data-dateout') || '' }
        function Calendar(target) {

            let div = document.getElementById('calendar');
            div.innerHTML = '';
            $(".calendarBox").css({ 'opacity': 0, "display": 'block' });


            requestAnimationFrame(() => {
                window.dnaCalendar($(div), (ob) => {
                    //console.log(ob);
                    //console.log(ob.d + "/" + (1 + ob.m) + "/" + ob.y);
                    apdiv.setAttribute("data-" + target.key, ob.d + "/" + (1 + ob.m) + "/" + ob.y);
                    bookObj[target.key] = ob.d + "/" + (1 + ob.m) + "/" + ob.y;
                    updateDates();
                }
                    , target.label, target.date, bookObj
                );
                $(".calendarBox").velocity({ 'opacity': 1 }, 400);
            });


        }




    }

    function saveBook(obj) {
        sessionStorage.setItem('alest_book_obj', JSON.stringify(obj));

    }



})();


var template;
var MONTHS = "Enero Febrero Marzo Abril Mayo Junio Julio Agosto Septiembre Octubre Noviembre Diciembre".split(" ");
var DAYS = "Dom Lun Mar Mie Jue Vie Sab".split(" ");
var MONTHSshort = "Ene Feb Mar Abr May Jun Jul Ago Sep Oct Nov Dic".toUpperCase().split(" ");



MONTHS = "January February March April May June July August September October November December".split(" ");
MONTHSshort = "Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec".toUpperCase().split(" ");
var DAYSf = "Monday Tuesday Wednesday Thursday Friday Saturday Sunday".split(" ");
DAYS = "Mon Tue Wed Thu Fri Sat Sun".split(" ");



$(function () {
    var mode = "fixed";



    //}

 




    //$(".dnaCalendar").each(function(){	dnaCalendar($(this))});


    window.dnaCalendar = function (div, fn, label, adate, bookobj) {

       // console.log("cal", label, adate);


        var labelDiv;
        var W, margin;
        var DATA = [];

        if (adate == '') {
            adate = { m: today.getMonth(), y: today.getFullYear(), d: today.getDate(), date: new Date(today) }


        } else {
            let at = adate.split("/");
            adate = { m: Number(at[1]) - 1, y: at[2], d: at[0], date: new Date() }


        }

        var monthdays;
        var temp, monthTitle, arrows, daysDiv, amonth, ayear, aday;
        var ClosestItem;

        if (!template) {

 
                temp = $(CALTEMP);
                template = $(CALTEMP);
                init();

  

        } else {
            temp = template.clone();
           // temp.find("style").remove();
            init();

        }

        function init() {

            DATA = [];


            /*
             
            */


            var nob = { d: adate.d, m: adate.m, y: adate.y };

            if (ClosestItem) {
                var d1 = DateObjToDate(nob);
                var d2 = DateObjToDate(ClosestItem);
                if (daysBetween(d1, today) <= daysBetween(d2, today)) {

                    ClosestItem = nob;
                }

            } else {
                ClosestItem = nob;

            }



            DATA.push({
                date: nob.d + "-" + nob.m + "-" + nob.y,
                label: "",
                link: "",
                day: nob.d,
                month: nob.m,
                year: nob.y
            });


 





            div.html(temp.clone());

            labelDiv = div.find(".calendarLabel");
            monthTitle = div.find(".dcHead p");
            monthTitle.click(showActual);


            arrows = div.find(".dcHead img");
            daysDiv = div.find(".dcDays");

            arrows.eq(0).click(prevMonth);
            arrows.eq(1).click(nextMonth);

            div.find(".dcHead .dcWeekDays span").css({ width: (div.width() / 7) + "px" });


            if (DATA.length > 0 && ClosestItem) {
                showMonth(ClosestItem.m, ClosestItem.y);
            } else showMonth(today.getMonth(), today.getFullYear());


            $(window).resize(align);
            align();
        }

        function align() {
            var spans = daysDiv.find("span");
            var tw = div.width() / 7;

            var mm = parseInt(tw - 16) / 2;
            spans.css({ width: tw + "px", height: tw + "px", "padding-top": mm + "px", "padding-bottom": mm + "px" });

            div.find(".dcHead .dcWeekDays span").css({ width: (div.width() / 7) + "px" });


        }

        function prevMonth(e) {
            if (e) e.preventDefault();
            //if(ayear<=today.getFullYear() && amonth<=today.getMonth() )return;

            var y = ayear;
            var m = amonth - 1;

            if (m < 0) { m = 11; y--; }
            showMonth(m, y);

        }

        function nextMonth(e) {
            if (e) e.preventDefault();

            var y = ayear;
            var m = amonth + 1;

            if (m >= 12) { m = 0; y++; }
            showMonth(m, y);

        }

        function showActual(e) {
            if (e) e.preventDefault();
            showMonth(amonth, ayear);

        }


        function showMonth(month, year) {
            if (label && labelDiv) labelDiv.html(label);

            amonth = parseInt(month);
            ayear = parseInt(year);
            monthdays = getDays(year, month);

            daysDiv.html('');
            for (var i = 0; i < monthdays.length; i++) {
console.log(JSON.stringify(monthdays[i],false,4));
                if (i == 0) {
                    var ds = monthdays[i].s;
                    if(ds==0)ds=7;
                    for (var ii = 0; ii < ds-1; ii++) {
                        daysDiv.append("<span class='dcEmpty'></span>");

                    }
                }


                daysDiv.append("<span data-id='" + i + "'>" + monthdays[i].d + "</span>");

            }

            var spans = daysDiv.find("span");
            var tw = div.width() / 7;


            spans.css({ width: tw + "px", height: tw + "px" });

            monthTitle.html(MONTHS[amonth] + " " + ayear);

            if (DATA) {
                spans.each(function () {
                    if ($(this).hasClass("dcEmpty")) return;
                    var sdate = monthdays[$(this).attr("data-id")];
                    var active = false;
                    for (var i = 0; i < DATA.length; i++) {
                        if (sdate.d == DATA[i].day && sdate.m == DATA[i].month && sdate.y == DATA[i].year) { active = true; }

                    }
                    if (sdate.y<=today.getFullYear()&& sdate.m <= today.getMonth() ) {

                        if (sdate.m < today.getMonth()) {

                            $(this).addClass('inactive');
                        } else {
                            if (sdate.d < today.getDate()) {
                                $(this).addClass('inactive');
                            }
                        }
                    }

                    if (active) {
                        $(this).addClass('active');
                        //$(this).click(showEvent);
                    }
                    $(this).click(dateClick);



                });
            } else {

                spans.each(function () {
                    if ($(this).hasClass("dcEmpty")) return;
                    var sdate = monthdays[$(this).attr("data-id")];
                    $(this).click(dateClick);


                });

            }

        }


        function dateClick(e) {
            if (e) e.preventDefault();
            if ($(this).hasClass('inactive')) return;
            var id = $(this).attr("data-id");
            var sdate = monthdays[id];
           // console.log("dateClick sdate", sdate);
            //if(!sdate.live)return;

            var nd = new Date();


            nd.setFullYear(sdate.y);
            nd.setMonth(sdate.m);
            nd.setDate(sdate.d);
            sdate.date = nd;
           // div.html('');
            if (fn) fn(sdate);
        }


        function getDays(year, month) {
            var dArr = [];

            var d = new Date();

            d.setFullYear(year);
            d.setDate(1);
            d.setMonth(month);


            dArr.push({
                d: d.getDate(),
                s: d.getDay(),
                m: d.getMonth(),
                y: d.getFullYear(),
                live: d >= today,
                date: d.getDate() + "-" + d.getMonth() + "-" + d.getFullYear()
            });

            for (var i = 1; i < 31; i++) {
                d.setDate(d.getDate() + 1);
                if (d.getMonth() == month) dArr.push({
                    m: d.getMonth(),
                    y: d.getFullYear(),
                    d: d.getDate(),
                    s: d.getDay(),
                    live: (d >= today),
                    date: d.getDate() + "-" + d.getMonth() + "-" + d.getFullYear()
                })
            }
            return dArr
        }
        function DateObjToDate(o) {
            var d = new Date();
        
            d.setFullYear(o.y);
            d.setMonth(o.m);
            d.setDate(o.d);
        
           // console.log("from", o.d + "/" + o.m + "/" + o.y, "to:", d);
        
            return d;
        }
        
    function daysBetween(dd1, dd2) {
        var d = Math.floor((dd2 - dd1) / 86400000);
        d = Math.abs(d);
        console.log("distancia", d);
        return d;
    }



    //loadData("scr.php",function(data){},{com:"string"},function(err){});
    function loadData(file, f, obj, err) {
        if (!obj) obj = {};
        $.post(file, obj)
            .done(function (data) {

                f(data);//.children("div"));

            }).error(function (data) {
                if (err) err(data); else console.log(data)
            });
    }



    }//dnaCalendar













});


let CALTEMP=`
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
`;