$(document).ready(function(){
    var x = $(window).height()-450;
    $("#wrap_content").css({"min-height":x});
    
    $('img[alt]').removeAttr('alt');
    // Menu Top
    $('#parents li').hover(function(){
        $(this).find('ul:first').stop().css({display:"block"});
        //$(this).find('ul:first').stop().slideDown(300);
    },function(){
        $(this).find('ul:first').stop().css({display:"none"});
    });
    
    // Slider banner top
    $("#slide").cycle({ 
		fx:    'fade',// http://jquery.malsup.com/cycle/browser.html
		speed:  1000,
		timeout: 3000,
		pager:  '#slide-nav',
	});
        
    // Slider detail product (detail page)
    $(".detail-slide").cycle({ 
		fx:    'scrollHorz',// http://jquery.malsup.com/cycle/browser.html
		speed:  1000,
		timeout: 3000,
		pager:  '#slide-nav'
	});
    // Marquee 1
    $("#marquee_p").cycle({ 
		fx:    'scrollHorz',
		speed:  1000,
		timeout: 4000,
		next:  '.next',
		prev:  '.prev',
	});
    
    $('.sp_daxem ul li').hover(function(){
        $(this).find(".icon_remove").stop().fadeIn();
    },function(){
        $(this).find(".icon_remove").stop().fadeOut();
    });
});