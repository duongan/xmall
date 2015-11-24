$(document).ready(function(){
	var tabs=$("#tabs a");

	tabs.click(function(){
		$(this).parent().find(".currenttab").removeClass("currenttab");
		$(this).addClass("currenttab");
		var current=$($(this).attr("href").replace("#","."));
		current.parent().find(".tabitem.active").removeClass("active")
		current.addClass("active");
	});

	var href=window.location.href;

	var part=href.split("#");
	if(part.length>1){
		var id="#"+part[1];
		tabs.each(function(){
			if($(this).attr("href")==id){
				$(this).click();
			}
		});
	}else{
		tabs.eq(0).click();
	}
});