
function initslide(obj,o){
	new slide(obj,(o!=null && typeof(o)=="object")?o:null).init();
}

function slide(obj,o){
	var th=this;
	this.content=null;
	var current=0;
	var max=0;
	this.sleepanimate=null;
	this.timer=null;
	this.sleep=null;
	this.init=function(){	
		LoadJson(info.base_url+"/ajax/slide",{},function(result){
			th.start(result);
		});
	};
	this.create=function(){
		sleepanimate=(o!=null && o.sleepanimate!=null)?o.sleepanimate:500;
		sleep=(o!=null && o.sleep!=null)?o.sleep:5000;
		
		
		if(o==null || (o!=null && ((o.nextprev!=null && o.nextprev) || o.nextprev==null))){
			var slidehide="slidehide";
			if(o!=null && (o.shownextprev!=null && o.shownextprev)){
				slidehide="";
			}
			obj.append("<div id='prev' class='"+slidehide+"'></div><div id='next' class='"+slidehide+"'></div>");

			obj.find("#prev").click(function(){
				if(--current<0){
					current=max-1;
				}
				th.move(current,sleepanimate);
				th.resetTimer();
			});
			obj.find("#next").click(function(){
				th.movenext();
				th.resetTimer();
			});
		}
		if(o==null || (o!=null && ((o.arrow!=null && o.arrow) || o.arrow==null))){
			var slidehide="slidehide";
			if(o!=null && (o.showarrow!=null && o.showarrow)){
				slidehide="";
			}
			obj.append("<div id='arrow' class='"+slidehide+"'></div>");

			for(var i=0;i<max;i++){
				obj.find("#arrow").append("<li></li>");
			}
			obj.find("#arrow li").click(function(){
				current=th.se($(this));
				th.resetTimer();
				th.move(current,500);
			}).eq(0).addClass("active");
		}
		th.content.mouseover(function(){
			clearInterval(timer);
		});
		th.content.mouseout(function(){
			th.run();
		});
	};
	this.se=function(cc){
		var aa=obj.find("#arrow li");
		for(var i=0;i<max;i++){
			if(aa.eq(i)[0]==cc[0])
				return i;
		}
		return 0;
	};
	this.resetTimer=function(){
		clearInterval(timer);
		th.run();
	};
	this.loadImage=function(arr,po){
		if(po<max){
			$("<img />", {
                src: info.base_url+"/uploads/images/slide/"+arr[po].name,
				alt:arr[po].alt,
                load: function () {
					th.content.append("<a href='"+arr[po].href+"'><img src='"+this.src+"' alt='"+this.alt+"' /></a>");
                    th.loadImage(arr,po+1);
                },
                error: function () {
					th.loadImage(arr,po+1);
                }
             });
			
		}else{
			th.create();
		

			th.run();
		}
	};
	this.start=function(arr){

		th.content=document.createElement("div");
		th.content.id="contentslide";

		obj.html(th.content);
		
		th.content=$(th.content);

		max=arr.length;
		th.content.css("width",max*600);
		th.loadImage(arr,0);
	};
	
	this.move=function(po,sle){
		obj.find("#arrow li.active").removeClass("active").parent().find("li").eq(po).addClass("active");
		th.content.animate({"margin-left":-(po*600)},sle);
	};

	this.movenext=function(){
			if(++current==max){
				current=0;
			}
			th.move(current,sleepanimate);
	};
	
	this.run=function(){
		timer=setInterval(function(){
			th.movenext();
		},sleep);
	};
}



(function ($) {
    $.fn.slider=function(options){
        initslide($(this),options);
    }
})($);

var active=false;

$(function(){
	$(window).load(function(){
		$("#slide").slider({
			"sleep":5000,
			"sleepanimate":500,
			"showarrow":true
		});
	});
	
	$(".boxfirst .tabs h1").click(function(){
		if(!$(this).hasClass("active")){
			$(this).parent().find("h1.active").removeClass("active");
			var con=$(this).parent().parent().find(".contentbox");
			con.find(".active").removeClass("active");
			var parenttab=con.find("."+$(this).attr("class"));
			parenttab.addClass("active");
			$(this).addClass("active");

			var th=$(this);
			
			if($(this).attr("alt")!="success"){
				var url=info.base_url+"/ajax/"+(($(this).hasClass("spmoi"))?"spmoi":"spbanchay");
				LoadJson(url,{},function(result){
					parenttab.html("");
					th.attr("alt","success");
					for(var i=0;i<result.length;i++){
						var html='<div class="item">';
							html+='<a href="'+info.base_url+'/'+result[i].URL+'_'+result[i].MASP+'.html" class="noneunder">';
							html+='<div class="beforhover">';
							html+='<i class="iconstatus '+result[i].ICON_TRANGTHAI+'"></i>';
							html+='<div class="product-image">';
							html+='<img width="150" height="120" src="'+info.base_url+'/uploads/images/sp/'+result[i].HINHANH+'" />';
							html+='</div>';
							html+='<div class="product-info">';
							html+='<div class="product-name">'+result[i].TENSP+'</div>';
							html+='<div class="product-price">';
							html+=result[i].GIA;
							html+='</div>';
							html+='</div>';
							html+='</div>';
							html+='<div class="afterhover">';
							html+='<div class="product-info">';
							html+='<div class="product-name">'+result[i].TENSP+'</div>';
							html+='<div class="product-price">';
							html+=result[i].GIA;
							html+='</div>';
							html+='</div>';
							html+='<div class="product-s">'
							html+=result[i].TOM_TAT_TS;
							html+='</div>';
							html+='<div class="promo-info"><span class="icon icon-product-promo"></span>Khuyến mãi</div>';		
							html+='<div class="product-o">';
							html+='<a class="btn-buy btn-a left" href="'+info.base_url+'/gio-hang?id='+result[i].MASP+'">';
							html+='<i class="icon left"></i>';
							html+='ĐẶT HÀNG';
							html+='</a><a href="'+info.base_url+'/yeu-thich?id='+result[i].MASP+'" title="Thêm vào yêu thích" class="icon icon-like left"></a><div class="clearleft"></div>';
							html+='</div>';
							html+='</div>';
							html+='</a>';
							html+='</div>';
						parenttab.append(html);
					}

				});
			}
		}
	});
	$(".boxproduct .item .promo-info li").mouseover(function(){
		var thp=$(this).parent().find(".none");
		var top=$(this).offset().top-$(window).scrollTop();
		var heightqc=thp.height();
		if(top-heightqc<=0){
			top=top+20;
		}else{
			top-=heightqc+20;
		}
		thp.css({"top":top,"left":$(this).offset().left+10}).fadeIn();
		
	}).mouseout(function(){
		$(this).parent().find(".none").hide();
	}).parent().find(".none").mouseover(function(){
		$(this).hide();
	});

});