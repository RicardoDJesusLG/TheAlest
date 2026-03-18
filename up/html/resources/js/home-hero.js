
//// - home-hero

$(function () {
    let hero = $(".hero-home");
    if (hero.length < 1) return;
    let nav = hero.find(".pager-slider");
    let active = false;
    let buts;
    let butT = nav.find('a').eq(0).clone();
    let index = 0;

let imgs;
try{
imgs=heroImages;
} catch(err){
imgs=[hero.find('.slide img').eq(0).attr('src')];

}
 
    let n = imgs.length, sto;

    let TIME = 4000, VEL = 3000;
 
    let fi = hero.find(".slide img").eq(0);
    fi.css('position', "absolute");
    waitLoad(fi, (img) => {
        fi.velocity({ opacity: 0.4 }, VEL, function () {
            sto = setTimeout(next, TIME);

        });

    });
    initDots();

    function next() {

        index++;
        if (index >= n) index = 0;
        showActual();

    }


    function updateDots() {

        nav.find('a').each(function (i) {
            if (i == index) $(this).addClass('active'); else $(this).removeClass('active');


        })

    }
    function initDots() {
        nav.html('');

        imgs.map((b, i) => {
            let but = butT.clone();
            nav.append(but);
            but.click(clk);

        });
        buts = nav.find('a');
        updateDots()
    }
    function clk(e) {
        if (e) e.preventDefault();
        if (active) return;
        let i = $(this).index();
        goTo(i);

    }

    function goTo(i) {
        clearTimeout(sto);
        index = i;
        showActual();
    }

    function showActual() {
        active = true;
        let nimg = $(` <img style='position:absolute;opacity:0;' src='${imgs[index]}' > `);

        hero.find(".slide img").addClass('toRemove');

        hero.find(".slide").append(nimg);
        updateDots();
        waitLoad(nimg, function (im) {
            nimg.velocity({ opacity: 0.4 }, VEL);

            hero.find(".slide .toRemove").velocity({
                opacity: 0
            }, VEL, function () {
                $(this).remove();
                active = false;
                sto = setTimeout(next, TIME);
            }
            );



        });

    }







    function waitLoad(img, f) {
        var im = new Image();
        im.onload = function () {
            console.log("loaded", $(img).attr("src"));
            img.attr("data-ratio", (this.naturalHeight / this.naturalWidth));
            f(img);
        };
        im.src = img.attr("src");

    }


});



//// - home-hero