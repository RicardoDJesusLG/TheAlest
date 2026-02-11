
$(function () {
    $("a[data-gallery]").click(function (e) {
        if (e) e.preventDefault();
        let o = {};
        let tag = $(this).attr('data-gallery');
        openGallery(tag);

    })


    $(".fullGallery nav > span").click(close);

});
function close() {
    $(".galeria").fadeIn();
    $(".fullGallery").removeClass('opened');
    $(".hero-noimage").fadeIn();
    $("footer").css({ 'margin-top': '0px' });
    $(".fullGallery .img").html('');
}


function openGallery(tag) {
    let active = false;
    $(".galeria").hide();
    $(".fullGallery").addClass('opened');
    let gallery = GALLERY[tag];
    let index = 0;
    let n = gallery.imgs.length;
    let gdiv = $(".fullGallery .img");
    let imgTitle=$(".fullGallery nav span.tit");
let navTitle=$(".fullGallery nav span").eq(0);
let nav=$(".fullGallery nav .dots");
navTitle.html(gallery.title);
    $(".hero-noimage").hide();
    $("footer").css({ 'margin-top': '90vh' });
   // if(n>1)gdiv.click(next);

initDots();

    showActual();
    $('html, body').animate({ scrollTop: 0 }, 'slow');
function initDots(){
    if(n<2){
        nav.html('');
        return;
    }
    let str='';

gallery.imgs.map((im,i)=>{
str+=`<i class='dot' data-n="${i}"></i>`;

})


nav.html(str);
nav.find('.dot').click(clk);
}
function updateDots(){
    nav.find('.dot').each(function(i){

        if(i==index)$(this).addClass("active"); else $(this).removeClass("active"); 
    });


}

function clk(e){
    if(e)e.preventDefault();
    if(active)return;
    index=$(this).attr('data-n');
    showActual();
}

    function next(e) {
        if (active) return;
        index++;
        if (index >= n) index = 0;
        showActual();

    }

    function showActual() {
        active = true;
        let im = new Image();
        updateDots();
        im.onload = function () {
            gdiv.find('img').addClass('toRemove');
            let img = $("<img src='" + gallery.imgs[index].src + "'>");
            gdiv.append(img);

imgTitle.html(gallery.imgs[index].title);
            img.velocity({ opacity: 1 }, 600, function () {
            
            img.click(next);
                gdiv.find('img.toRemove').remove();
                active = false;

            })

        };
        im.src = gallery.imgs[index].src;

    }


}