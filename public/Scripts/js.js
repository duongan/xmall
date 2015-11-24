function LoadJson(url,dt,callback) {
    
	$.ajax({
			type: "POST",
			url: url,
			dataType: 'json',
			data:dt,
			beforeSend: function(){
			},
			success: callback,
			error: function (e, e2, e3) {
			}
	});
}
function getKeyEvent(e) {
    if (window.event) {
        return window.event.keyCode;
    }
    else {
        return e.which;
    }
    return 0;
}

var posearch=null;
var inputs=null;
var resultsearch=[];

function addDataSearch(resultsp,resulttt){

    posearch.html("");

    var first=false;

    for (var i = 0; i < ((resultsp.length>10)?10:resultsp.length); i++) {
        if(!first){
            posearch.append("<h2>SẢN PHẨM</h2>");   
            first=true; 
        }
        posearch.append("<li class='itemsp'><a href='"+info.base_url+"/"+resultsp[i].URL+"_"+resultsp[i].MASP+".html'><img width='30' height='30' src='"+info.base_url+"/uploads/images/sp/"+resultsp[i].HINHANH+"' /><span>"+resultsp[i].TENSP+"</span><b>"+resultsp[i].GIA+"</b></a><div class='clearleft'></div></li>");
    }

    first=false;

    for (var i = 0; i < ((resulttt.length>10)?10:resulttt.length); i++) {
        if(!first){
            posearch.append("<h2>TIN TỨC</h2>");   
            first=true; 
        }
        posearch.append("<li class='itemtt'><a href='"+info.base_url+"/"+resulttt[i].MASP+"_"+resulttt[i].URL+".html'><img width='30' height='30' src='"+info.base_url+"/uploads/images/tintuc/"+resulttt[i].HINHANH+"' /><span>"+resulttt[i].TENSP+"</span></a><div class='clearleft'></div></li>");  
    }

    if(resultsp.length>0 || resulttt.length>0){
        posearch.show();
    }
}

 function trim(text) {
     return text.replace(/^\s+|\s+$/gm, '');
 }

function showsearch(){
    var key=trim(inputs.val());
    if(key!=""){

        posearch.hide();
        key=key.toLowerCase().split(" ");

        var arrsp=[];
        var arrtt=[];
        
        $.each(resultsearch, function (i, r) {
            var dem=0;
            for(var x=0;x<key.length;x++){
                if(r.URL.indexOf(key[x])!=-1){
                    dem++;
                }
            }
            if(dem>0){
                r.count=dem;
                if(r.GIA!="0")
                    arrsp.push(r);
                else
                    arrtt.push(r);
            }
        });

        for(var i=0;i<arrsp.length;i++){
            for(j=i+1;j<arrsp.length;j++){
                if(arrsp[i].count<arrsp[j].count){
                    var temp=arrsp[i];
                    arrsp[i]=arrsp[j];
                    arrsp[j]=temp;
                }
            }
        }

        for(var i=0;i<arrtt.length;i++){
            for(j=i+1;j<arrtt.length;j++){
                if(arrtt[i].count<arrtt[j].count){
                    var temp=arrtt[i];
                    arrtt[i]=arrtt[j];
                    arrtt[j]=temp;
                }
            }
        }

        addDataSearch(arrsp,arrtt);


    }else{
        posearch.hide();
    }
}

function search(th){
	if($(th).hasClass("first")){
    	th=$(th);
    	th.parent().parent().find("img#iconloads").show();
    	if(!th.hasClass("load")){
    		th.addClass("load");
    		LoadJson(info.base_url+"/ajax/datasearch",{},function(result){
                posearch=$("#datasearch");
                inputs=$("#search .inputsearch");
                   
                resultsearch=result;
                th.removeClass("first");
                th.parent().parent().find("img#iconloads").hide();
                if(th.val().length>0)
                    th.parent().parent().find("i").show();
                showsearch();
            });
    	}
    }else{
    	if(th.value.length>0)
    		$(th).parent().parent().find("i").show();
    	else{
    		$(th).parent().parent().find("i").hide();
    	}
        showsearch();
   	}
    return true;
}

var timersearch=null;

function ktsearchup(e,th){
	clearTimeout(timersearch);

	timersearch=setTimeout(function(){
		search(th);
	},500);
}

var timerwi=null;
var gotop=null;

$(document).ready(function(){
	gotop=$("#btn-go-top");
	$(window).scroll(function(){
		clearTimeout(timerwi);
		timerwi=setTimeout(function(){
			if($(window).scrollTop()>200){
				gotop.fadeIn();
			}else{
				gotop.fadeOut();
			}
		},1000);
	});

	$("#header #navtop_header #search i.icona").click(function(){
		var t=$(this).hide().parent().find(".inputsearch").val("");
        posearch.hide();
        setTimeout(function(){
            t.focus();
        },500);
	});
    $("#search .inputsearch").blur(function(){
        setTimeout(function(){
            if(posearch!=null){
                posearch.hide();
            }
        },500);
        
    }).click(function(){
        if(inputs!=null)
        showsearch();
    });
    $(".btn-buy,.adtocart").click(function(){
        var id=$(this).attr("href").split("?id=")[1].split("&")[0];
        LoadJson(info.base_url+"/ajax/addtocart",{"id":id},function(result){
            if(result==0){
                alert("Thêm vào giỏ hàng thất bại. Có thể sản phẩm đã hết hàng.");
            }else{
                $("#cart .total_item").html("("+result+")");
                if(confirm("Thêm thành công sản phẩm vào giỏ hàng. Bạn có muốn vào giỏ hàng?")){
                    window.location.href=info.base_url+"/gio-hang";
                }
            }
        });
        return false;
    });

    $(".boxproduct .item .icon-like,.btn-like").click(function(){
        var id=$(this).attr("href").split("?id=")[1].split("&")[0];
        LoadJson(info.base_url+"/ajax/addtolike",{"id":id},function(result){
           $("#like .total_item").html("("+result+")"); 
           if(confirm("Lưu thành công sản phẩm vào yêu thích. Bạn có muốn vào xem các sản phẩm yêu thích khác?")){
                    window.location.href=info.base_url+"/yeu-thich";
                }
        });
        return false;
    });
    $(".boxproduct .contentbox img,.boxproduct .news img").error(function(){
        $(this).attr("src",info.base_url+"/public/Contents/Images/imgnotfound.png");
    });
    setTimeout(function(){
        LoadJson(info.base_url+"/ajax/countlike",{},function(result){
            $("#like .total_item").html("("+result.c+")"); 
        });
    },1500);
    
    $("body").css("min-width",(screen.width-30)+"px");

});