$(function(){

$.fn.preloadify = function(options){

var defaults = {
delay:0,
imagedelay:0,
mode:"parallel",
preload_parent:"a",
check_timer:300,
ondone:function(){ },
oneachload:function(image){ },
fadein:700
};

// variables declaration and precaching images and parent container
var options = $.extend(defaults, options),
parent = $(this),
timer,i=0,j=options.imagedelay,counter=0,images = parent.find("img").css({display:"block",visibility:"hidden",opacity:0}),
check_flag = new Array(),
imagedelayer = function(image,time){

$(image).css("visibility","visible").delay(time).animate({opacity:1},options.fadein,function(){ $(this).parent().removeClass("preloader"); });

};

// add preloader to parent or wrap anchor depending on option
images.each(function(){

if($(this).parent(options.preload_parent).length==0)
$(this).wrap("<a class='preloader' />");
else
$(this).parent().addClass("preloader");

check_flag[i++] = false;

});

// convert into image array
images = $.makeArray(images);
counter = 0;

// function to show image
function showimage(i)
{
if(check_flag[i]==false)
{
counter++;
options.oneachload(images[i]);
check_flag[i] = true;
}

if(options.imagedelay==0&&options.delay==0)
$(images[i]).css("visibility","visible").animate({opacity:1},700);
else if(options.delay==0)
{
imagedelayer(images[i],j);
j += options.imagedelay;
}
else if(options.imagedelay==0)
{
imagedelayer(images[i],options.delay);

}
else
{
imagedelayer(images[i],(options.delay+j));
j += options.imagedelay;
}

}

// preload images parallel
function preload_parallel()
{
for(i=0;i<images.length;i++)
{
if(images[i].complete==true)
{
showimage(i);

}
}
}

// shows images based on index with respect to parent container
function preload_sequential()
{

if(images[i].complete==true)
{
showimage(i);
i++;
}
}

i=0;j=options.imagedelay;
// keep on checking after predefined time, if image is loaded
timer = setInterval(function(){

if(counter>=check_flag.length)
{
clearInterval(timer);
options.ondone();

return;
}

if(options.mode=="parallel")
preload_parallel();
else
preload_sequential();

},options.check_timer)
}
})

// load

$(function(){
    //$(".wrap_pro").preloadify({ imagedelay:500 });
});