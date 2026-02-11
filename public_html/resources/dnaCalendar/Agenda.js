
function UID() {

    //return (new Date().getTime()) + "_" + (Math.random() * 100000);
    return uuid.v4();
}

$(function () {
    var agenda_template="resources/dnaCalendar/agenda-template.html";
    var mode = "fixed1";
    var hs = "00:00 00:30 01:00 01:30 02:00 02:30 03:00 03:30 04:00 04:30 05:00 05:30 06:00 06:30 07:00 07:30 08:00 08:30 09:00 09:30 10:00 10:30 11:00 11:30 12:00 12:30 13:00 13:30 14:00 14:30 15:00 15:30 16:00 16:30 17:00 17:30 18:00 18:30 19:00 19:30 20:00 20:30 21:00 21:30 22:00 22:30 23:00 23:30".split(" ");
    var MONTHS = "Enero Febrero Marzo Abril Mayo Junio Julio Agosto Septiembre Octubre Noviembre Diciembre".split(" ");
    var DAYS = "Dom Lun Mar Mie Jue Vie Sab".split(" ");
    var MONTHSshort = "Ene Feb Mar Abr May Jun Jul Ago Sep Oct Nov Dic".toUpperCase().split(" ");


    if(window.lenguaje=='pr'){

        MONTHS="Janeiro Fevereiro Março Abril Maio Junho Julho Agosto Setembro Outubro Novembro Dezembro".split(" ");
        MONTHSshort="Jan Fev Mar Abr Mai Jun Jul Ago Set Out Nov Dez".toUpperCase().split(" ");
     
     
     }
     


    var template;
  
    window.Agenda = function (div, fn) {
         var eventTemplate;
        var DATA = [];
        var today = new Date();
        var monthdays;
        var temp, monthTitle, arrows, daysDiv, amonth, ayear, aday;
        var ClosestItem;

        if (!template) {
            loadData(agenda_template, function (data) {

                temp = $(data);
                template = $(data);
                init();

            }, {}, function (err) {
                console.log(err);

            });
        } else {
            temp = template.clone();
            temp.find("style").remove()
            init();

        }

        function init() {
            var dat = div.find(".recordatorio");


            if (dat.length > 0) {
                DATA = [];


                /*
                 
                */

                dat.each(function () {
                    var ev = $(this);

                    var m = Number(ev.attr("month"));
                    var y = Number(ev.attr("year"));

                    //if(y>=today.getFullYear() && m>=today.getMonth()){

                    var nob = { d: ev.attr("day"), m: m, y: y };

                    if (ClosestItem) {
                        var d1 = DateObjToDate(nob);
                        var d2 = DateObjToDate(ClosestItem);
                        if (daysBetween(d1, today) <= daysBetween(d2, today)) {

                            ClosestItem = nob;
                        }

                    } else {
                        ClosestItem = nob;

                    }

                    var idd = ev.attr("id");
                    if (idd == undefined) {
                        idd = UID()
                    }

                    DATA.push({
                        date: ev.attr("day") + "-" + ev.attr("month") + "-" + ev.attr("year"),
                        label: ev.attr("label"),
                        text: ev.attr("text"),
                        hour: ev.attr("hour"),

                        id: idd,
                        day: ev.attr("day"),
                        month: ev.attr("month"),
                        year: ev.attr("year"),

                    });



                    //}  


                });




            }//



            div.html(temp.clone());
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




            $(".event-list .close,.event-list .closeBut ,.event-list .event-hit").click(function (e) {
                if (e) e.preventDefault();
                $(".event-list").fadeOut();
                $(".calendarBox").css("height", "auto");
                $(".calendarBox .dcdiv").show();

            });


            $(".event-list .event-new").click(function (e) {
                if (e) e.preventDefault();
                addEvent();

            });


            eventTemplate = $(".event-list .event-item").eq(0).clone();







            $(window).resize(align);
            align();
        }



        function align() {
            var spans = daysDiv.find("span");
            var tw = (div.width() - 2) / 7;

            var mm = parseInt(tw - 16) / 2;
            //spans.css({width:tw+"px",height:tw+"px","padding-top":mm+"px","padding-bottom":mm+"px"});
            var o = 1;
            if (div.width() < 400) {
                tw = div.width();
                o = 0;
                spans.each(function () {
                    $(this).parent().css({ width: tw + "px", height: (tw * 0.2) + "px" });

                    if ($(this).hasClass("dcEmpty"))
                        $(this).parent().hide();



                });

            } else {
                var th = tw * 0.618;
                if (th < 100) th = 100;
                spans.each(function () {
                    $(this).parent().css({ width: tw + "px", height: (th) + "px" });
                    if ($(this).hasClass("dcEmpty"))
                        $(this).parent().show();


                });

            }

            div.find(".dcHead .dcWeekDays span").css({ width: (div.width() / 7) + "px", opacity: o });


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

            if (!month && !year) {
                month = amonth;
                year = ayear;


            }

            amonth = parseInt(month);
            ayear = parseInt(year);
            monthdays = getDays(year, month);

            daysDiv.html('');
            for (var i = 0; i < monthdays.length; i++) {

                if (i == 0) {
                    var ds = monthdays[i].s;
                    for (var ii = 0; ii < ds; ii++) {
                        daysDiv.append("<div class='cbox'><span class='dcEmpty'></span></div>");

                    }
                }


                daysDiv.append("<div class='cbox'><span data-id='" + i + "'>" + monthdays[i].d + "</span></div>");

            }

            var spans = daysDiv.find("span");
            var tw = div.width() / 7;


            spans.each(function () {
                //$(this).parent().css({width:tw+"px",height:(tw*0.618)+"px"});

            });


            monthTitle.html(MONTHS[amonth] + " " + ayear);

            if (DATA) {
                spans.each(function () {
                    if ($(this).hasClass("dcEmpty")) {

                        if (div.width() < 400) {
                            $(this).parent().hide();

                        }

                        return;
                    }
var labs=$("<div class='event-labels'/>");

                    var sdate = monthdays[$(this).attr("data-id")];
                    var active = false;
                    var cc=0;
                    for (var i = 0; i < DATA.length; i++) {
                        if (sdate.d == DATA[i].day && sdate.m == DATA[i].month && sdate.y == DATA[i].year) {
                            if(cc++<2)labs.append('<i>'+(DATA[i].hour.trim())+' . '+(DATA[i].label.trim())+'</i>');

                            active = true;
                        }

                    }
                    if (active) {
                        $(this).addClass('active');
                        //$(this).click(showEvent);

                        $(this).parent().append(labs);

                        $(this).parent().click(dateClick);
                    } else {
                        $(this).parent().click(dateClick);

                    }

                });
            } else {

                spans.each(function () {
                    if ($(this).hasClass("dcEmpty")) return;
                    var sdate = monthdays[$(this).attr("data-id")];
                    $(this).parent().click(dateClick);
                    $(this).parent().css("cursor", "pointer");



                });

            }


            align();

        }


        function dateClick(e) {
            if (e) e.preventDefault();
            var id = $(this).find("span").attr("data-id");
            var sdate = monthdays[id];
            console.log(sdate);

            var nd = new Date(sdate.y, sdate.m, sdate.d);

            sdate.date = nd;
            if (fn) fn(sdate, div);
            else {


                showPopup(sdate);

            }
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

            // console.log("from",o.d+"/"+o.strMonth+"/"+o.y,"to:",d);

            return d;
        }



        function daysBetween(dd1, dd2) {
            var d = Math.floor((dd2 - dd1) / 86400000);
            d = Math.abs(d);
            //    console.log("distancia",d);
            return d;
        }






        function showPopup(res) {
            var qry = res.d + " de " + MONTHS[Number(res.m)] + " de " + res.y; //  Ej. 26/0/2020  (meses 0 a 11)
             
            var evList=$(".event-list");
            var fechaEl=evList.find(".event-list-fecha");
            evList.fadeIn();
            fechaEl.html(qry);

            fechaEl.attr("data-d", res.d);
            fechaEl.attr("data-m", res.m);
            fechaEl.attr("data-y", res.y);


            if (div.width() < 400) {

                $(".calendarBox .dcdiv").hide();

            }


            evList.find(".items").html("");

            for (var i = 0; i < DATA.length; i++) {
                if (DATA[i].day == res.d && DATA[i].month == res.m && DATA[i].year == res.y) {
                    var t = eventTemplate.clone();
                    t.attr("data-id", DATA[i].id);
                    t.find(".event-label b").text(DATA[i].label);
                    t.find(".event-content p").text(DATA[i].text);
                    t.find(".event-time .event-time-val").text(DATA[i].hour);
                    t.find(".eig").click(guardarEvento);
                    t.find(".eib").click(borrarEvento);


                    t.find(".eib").click(borrarEvento);


                    t.find(".event-time .event-time-less").click(function (e) {
                        $(this).parent().find(".event-time-val").text(getPrevH($(this).parent().find(".event-time-val").text()));
                        $(this).parent().parent().find(".eig").addClass("active");
                    });
                    t.find(".event-time .event-time-more").click(function (e) {
                        $(this).parent().find(".event-time-val").text(getNextH($(this).parent().find(".event-time-val").text()));
                        $(this).parent().parent().find(".eig").addClass("active");

                    });


                    evList.find(".items").append(t);
                    t.find(".event-content p,.event-label b").keydown(function () {
                        $(this).closest(".event-item").find(".eig").addClass("active");
                    });

                }

                alignPop();

                orderItems();


            }

            function getPrevH(h) {
                var id = 16;
                for (var i = 0; i < hs.length; i++) {
                    if (hs[i] == h) id = i - 1;
                }
                if (id == -1) id = hs.length - 1;
                return hs[id];

            }

            function getNextH(h) {
                var id = 16;
                for (var i = 0; i < hs.length; i++) {
                    if (hs[i] == h) id = i + 1;
                }
                if (id >= hs.length) id = 0;

                return hs[id];

            }

            function guardarEvento(e) {
                var item = $(this).closest(".event-item");
                item.find(".eig").removeClass("active");
var fechadiv=$(".event-list .event-list-fecha");
                var o = {
                    day: fechadiv.attr("data-d"),
                    month: fechadiv.attr("data-m"),
                    year: fechadiv.attr("data-y"),
                    text: item.find(".event-content").text(),
                    label: item.find(".event-label").text(),
                    id: item.attr("data-id"),
                    hour: item.find(".event-time-val").text()

                };
                o.text = o.text.trim();
                o.label = o.label.trim();
                o.date = o.day + "-" + o.month + "-" + o.year;

                //console.log(o);

                var exist = false;
                for (var i = 0; i < DATA.length; i++) {
                    //  console.log(DATA[i].id,o.id,DATA[i].id==o.id);
                    if (DATA[i].id == o.id) {
                        DATA[i] = o;
                        exist = true;
                    }

                }
                if (!exist) {
                    DATA.push(o);
                }
                orderItems();

                GATEWAY.guardarEvento(o);
                showMonth(o.month, o.year);

            }



            function borrarEvento(e) {
                var item = $(this).closest(".event-item");
                var id = item.attr("data-id");
                var tmp = [];
                for (var i = 0; i < DATA.length; i++) {
                    if (DATA[i].id != id) {
                        tmp.push(DATA[i]);
                    }

                }
                DATA = tmp;

                showMonth(false, false);
                item.remove();

                GATEWAY.borrarEvento(id);
                alignPop();
            }


            window.addEvent = function () {
                var t = eventTemplate.clone();
                t.attr("data-id", UID());
                t.find(".event-label b").text("título...");
                t.find(".event-content p").text("texto...");
                t.find(".eig").click(guardarEvento);
                t.find(".eib").click(borrarEvento);
                t.find(".eig").addClass("active");
                t.find(".event-time-val").text("12:00");

                $(".event-list .items").append(t);


                t.find(".event-content p,.event-label b").keydown(function () {
                    $(this).closest(".event-item").find(".eig").addClass("active");
                });


                t.find(".event-time .event-time-less").click(function (e) {
                    $(this).parent().find(".event-time-val").text(getPrevH($(this).parent().find(".event-time-val").text()));
                    $(this).parent().parent().find(".eig").addClass("active");
                });

                t.find(".event-time .event-time-more").click(function (e) {
                    $(this).parent().find(".event-time-val").text(getNextH($(this).parent().find(".event-time-val").text()));
                    $(this).parent().parent().find(".eig").addClass("active");

                });

                alignPop();

            }


        }



        function alignPop() {

            var lh = $(".event-list").height() + 100;
            var ch = $(".calendarBox").height();
            if (lh > ch) {
                $(".calendarBox").css("height", lh + "px");

            } else {
                $(".calendarBox").css("height", "auto");

            }

        }



        function orderItems() {
            var lista = $(".event-list .lista .items");
            var items = lista.find('.event-item');
            var its = [];
            items.each(function () {
                var t = $(this).find(".event-time-val").text();
                var ind = hs.indexOf(t);
                if (ind == -1) { ind = 0; }
                its.push({ index: ind, node: $(this) });
                console.log(ind);
            });

            its = its.sort(function (a, b) {
                var av = a.index;
                var bv = b.index;
                return av - bv;


            });

            for (var i = 0; i < its.length; i++) {
                lista.append(its[i].node);
            }



        }

        return {
            showPopup: showPopup
        }


    }//Agenda



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







});











!function(n){"use strict";function e(){var e=n.crypto||n.msCrypto;if(!f&&e&&e.getRandomValues)try{var r=new Uint8Array(16);s=f=function(){return e.getRandomValues(r),r},f()}catch(o){}if(!f){var t=new Array(16);i=f=function(){for(var n,e=0;16>e;e++)0===(3&e)&&(n=4294967296*Math.random()),t[e]=n>>>((3&e)<<3)&255;return t},"undefined"!=typeof console&&console.warn&&console.warn("[SECURITY] node-uuid: crypto not usable, falling back to insecure Math.random()")}}function r(){if("function"==typeof require)try{var n=require("crypto").randomBytes;c=f=n&&function(){return n(16)},f()}catch(e){}}function o(n,e,r){var o=e&&r||0,t=0;for(e=e||[],n.toLowerCase().replace(/[0-9a-f]{2}/g,function(n){16>t&&(e[o+t++]=y[n])});16>t;)e[o+t++]=0;return e}function t(n,e){var r=e||0,o=v;return o[n[r++]]+o[n[r++]]+o[n[r++]]+o[n[r++]]+"-"+o[n[r++]]+o[n[r++]]+"-"+o[n[r++]]+o[n[r++]]+"-"+o[n[r++]]+o[n[r++]]+"-"+o[n[r++]]+o[n[r++]]+o[n[r++]]+o[n[r++]]+o[n[r++]]+o[n[r++]]}function u(n,e,r){var o=e&&r||0,u=e||[];n=n||{};var a=null!=n.clockseq?n.clockseq:g,f=null!=n.msecs?n.msecs:(new Date).getTime(),i=null!=n.nsecs?n.nsecs:C+1,c=f-h+(i-C)/1e4;if(0>c&&null==n.clockseq&&(a=a+1&16383),(0>c||f>h)&&null==n.nsecs&&(i=0),i>=1e4)throw new Error("uuid.v1(): Can't create more than 10M uuids/sec");h=f,C=i,g=a,f+=122192928e5;var s=(1e4*(268435455&f)+i)%4294967296;u[o++]=s>>>24&255,u[o++]=s>>>16&255,u[o++]=s>>>8&255,u[o++]=255&s;var l=f/4294967296*1e4&268435455;u[o++]=l>>>8&255,u[o++]=255&l,u[o++]=l>>>24&15|16,u[o++]=l>>>16&255,u[o++]=a>>>8|128,u[o++]=255&a;for(var d=n.node||w,v=0;6>v;v++)u[o+v]=d[v];return e?e:t(u)}function a(n,e,r){var o=e&&r||0;"string"==typeof n&&(e="binary"===n?new d(16):null,n=null),n=n||{};var u=n.random||(n.rng||f)();if(u[6]=15&u[6]|64,u[8]=63&u[8]|128,e)for(var a=0;16>a;a++)e[o+a]=u[a];return e||t(u)}var f,i,c,s,l;n?e():r();for(var d="function"==typeof Buffer?Buffer:Array,v=[],y={},m=0;256>m;m++)v[m]=(m+256).toString(16).substr(1),y[v[m]]=m;var p=f(),w=[1|p[0],p[1],p[2],p[3],p[4],p[5]],g=16383&(p[6]<<8|p[7]),h=0,C=0,R=a;R.v1=u,R.v4=a,R.parse=o,R.unparse=t,R.BufferClass=d,R._rng=f,R._mathRNG=i,R._nodeRNG=c,R._whatwgRNG=s,"undefined"!=typeof module&&module.exports?module.exports=R:"function"==typeof define&&define.amd?define(function(){return R}):(l=n.uuid,R.noConflict=function(){return n.uuid=l,R},n.uuid=R)}("undefined"!=typeof window?window:null);