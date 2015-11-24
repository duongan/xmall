
function dialog(obj,options){
	var th=this;

	this.init=function(){	
		if(!obj.hasClass("dialog")){
			obj.addClass("dialog");
		}

		if(options.width==null){
			options.width=500;
		}

		if(options.height==null){
			options.height=300;
		}

		if(options.title==null){
			options.title="Tiêu Đề Dailog";
		}

		var widthw=$(window).width();
		var widthh=$(window).height();

		var left=(widthw-options.width)/2;
		var top=(widthh-options.height)/2;
		left=left*100/widthw;
		top=top*100/widthh;

		obj.css({"width":options.width,"height":options.height,"left":left+"%","top":(top-5)+"%"});

		if(!$(".dimb").length){
			$("body").append("<div class='dimb'></div>");
			$(".dimb").click(function(){
				th.hide();
			});
		}

	};
	this.show=function(){
		obj.fadeIn();
		$(".dimb").fadeIn();
	};
	this.hide=function(){
		$(".dialog").fadeOut();
		$(".dimb").fadeOut();
	};
}
