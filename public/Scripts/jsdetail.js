var arrtop=[];

var tmers=null;

function isEmail(obj){
	if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(obj)) {
        return true
    }
	return false;
}


$(document).ready(function(){
	var images=$("#selectimages #contentimages");
	var sizeimg=images.find("img").size();
	images.css("width",sizeimg*35);
	var counts=0;
	var maxs=parseInt(sizeimg/4);
	var currentselect=0;
	if(maxs%4!=0){
		maxs++;
	}
	if(sizeimg==1){
		images.parent().hide();
	}else{
		if(sizeimg<4){
			images.parent().css("width",35*sizeimg);
		}
		if(sizeimg>4){
			images.parent().parent().append("<div id='moveleft' class='cimages'><</div><div id='moveright' class='cimages'>></div>");
			$("#moveleft").click(function(){
				if(counts>0){
					counts--;
				}else{
					counts=maxs-1;
				}
				images.animate({"margin-left":-35*(counts*4)},500);
			});
			$("#moveright").click(function(){
				if(counts<maxs-1){
					counts++;
				}else{
					counts=0;
				}
				images.animate({"margin-left":-35*(counts*4)},500);
			});
		}
		for(var i=0;i<sizeimg;i++){
			images.find("img").eq(i).attr("alt",i);
		}
		images.find("img").click(function(){
			if(!$(this).hasClass("active")){
				var th=$(this);
				th.parent().find(".active").removeClass("active");
				th.addClass("active");
				if(currentselect>th.attr("alt")){
					$("#product_images #imagesp").animate({"margin-left":-280},500,function(){
						$("#product_images #imagesp").css("margin-left",280).find("img").attr("src",th.attr("src"));
					});
				}else{
					$("#product_images #imagesp").animate({"margin-left":280},500,function(){
						$("#product_images #imagesp").css("margin-left",-280).find("img").attr("src",th.attr("src"));
					});
				}
				currentselect=th.attr("alt");
				$("#product_images #imagesp").animate({"margin-left":0},500);
			}
		});
	}
	

	arrtop.push({"obj":$("#tabs"),"top":$("#tabs").offset().top});
	var ultabs=arrtop[0].obj.find("ul a").click(function(){
		$(this).parent().find("a.active").removeClass("active");
		$(this).addClass("active");
	});

	ultabs.each(function(){
		arrtop.push({"obj":$(this),"top":$($(this).attr("href")).offset().top});
	});

	$(window).scroll(function(){
		clearTimeout(tmers);
		tmers=setTimeout(function(){
			var topwin=$(window).scrollTop();
			for(var i=0;i<arrtop.length;i++){
				if(topwin+150>arrtop[i].top){
					arrtop[i].obj.parent().find("a.active").removeClass("active");
					arrtop[i].obj.addClass("active");
				}else{
					arrtop[i].obj.removeClass("active");
				}
			}
		},200);
	});
	
	$("#frmcomment").submit(function(){
		if($(this).find(".inputsubmit").val()=="Gửi"){
			var hoten=$(this).find("input[name='hoten']").val().trim();
			var email=$(this).find("input[name='email']").val().trim();
			var valuecomment=$(this).find("textarea[name='valuecomment']").val().trim();
			var captcha=$(this).find("#captcha").val();
			var danhgia=$(this).find("#vdanhgia").val();
			var masp=$(this).find("input[name='masp']").val();
			if(hoten==""){
				alert('Vui lòng nhập họ tên.');
				$(this).find("input[name='hoten']").focus();
			}else{
				if(!isEmail(email)){
					alert('Email không hợp lệ.');
					$(this).find("input[name='email']").focus();
				}else{
					if(valuecomment==""){
						alert('Vui lòng nhập nội dung nhận xét.');
						$(this).find("textarea[name='valuecomment']").focus();
					}else{
						if(captcha!=""){
							var thhh=$(this);
							thhh.find(".inputsubmit").val("Đang gửi...");
							var objdt={'hoten':hoten,'email':email,'valuecomment':valuecomment,'captcha':captcha,'danhgia':danhgia,'masp':masp,'nx_cha':0};
							LoadJson(info.base_url+'/ajax/sendcomment',objdt,function(result){
								switch(result){
									case -1:
										alert("Có lỗi gửi thất bại.");
										break;
									case -2:
										alert("Mã xác nhận sai.");
										thhh.find("#imgcaptcha").attr("src",info.base_url+"/captcha");
										thhh.find("#captcha").val("");
										thhh.find("#captcha").focus();
										break;
									default:
										thhh.find("textarea[name='valuecomment']").val("");
										thhh.find("#imgcaptcha").attr("src",info.base_url+"/captcha");
										thhh.find("#captcha").val("");
										var item=document.createElement("div");
										item.setAttribute("class","item");

										item.innerHTML=insertComment(0,result,objdt);
										$(item).hide();
										$("#contentcomment").prepend(item);
										$(item).fadeIn(1000);
										 removeEvent();
										addEvent();
										break;
								}
								thhh.find(".inputsubmit").val("Gửi");
							});
						}else{
							alert("Vui lòng nhập mã xác nhận.");
							$(this).find("#captcha").focus();
						}
					}
				}
			}
		}
		return false;
	});

	
	addEvent();

	var countcm=0;
	var objitemcm=$("#nhanxet .itemparent");


	$("#morecomment span").click(function(){
		countcm+=10;
		for (var icm = countcm; icm <countcm+10 ; icm++) {
			objitemcm.eq(icm).fadeIn();
		}		
		if(countcm+10>=scm){
			$(this).parent().hide();
		}
	});

	$(".addthis_toolbox a.at300b").click(function () {
        if (!$(this).hasClass("printpage")) {
            window.open($(this).attr("href"), '_blank', "menubar=no,toolbar=no,resizable=no,scrollbars=no,height=450,width=710");

            return false;
        }
    });

});

function addEvent(){
	var currentover=null;
	var objdanhgia=$("#danhgiasp img,.dgspsub img");
	objdanhgia.on('mouseover',function(){
		$(this).parent().find("img").attr("src","public/Contents/Images/star-off.png");
		for(var i=0;i<$(this).attr("alt");i++){
			$(this).parent().find("img").eq(i).attr("src","public/Contents/Images/star-on.png");
		}
		currentover=$(this);
	}).on('mouseout',function(){
		$(this).parent().find("img").attr("src","public/Contents/Images/star-off.png");
		for(var i=0;i<$(this).parent().find("input[type='hidden']").val();i++){
			$(this).parent().find("img").eq(i).attr("src","public/Contents/Images/star-on.png");
		}
	}).on('click',function(){
		$(this).parent().find("input[type='hidden']").val($(this).attr("alt"));
		$(this).parent().find("img").attr("src","public/Contents/Images/star-on.png");
	});

	$("#nhanxet .item .ctcomment .reply").on('click',function(){
		$(this).parent().parent().parent().find(".replycomment").fadeToggle();
	});

	$("#nhanxet .item .ctcomment .like").on('click',function(){
		var th=$(this);
		var sl=1;
		if(th.find("strong").text()!="")
			sl=parseInt(th.find("strong").text())+1;
		LoadJson(info.base_url+"/ajax/likecm",{"idnx":$(this).parent().find(".idnx").val(),"sl":sl},function(result){
			if(result==-2){
				if(confirm("Đăng nhập để thích nhận xét?")){
					window.location.href=info.base_url+"/dang-nhap.html?r="+window.location.href;
				}
			}else{
				th.find("strong").text(sl+"");
				th.off("click").addClass("success");
			}
		});
	});

	$("#nhanxet .item .ctcomment .warning").on('click',function(){
		var th=$(this);
		LoadJson(info.base_url+"/ajax/baocaovp",{"idnx":$(this).parent().find(".idnx").val()},function(result){
			if(result==-2){
				if(confirm("Đăng nhập để báo cáo vi phạm?")){
					window.location.href=info.base_url+"/dang-nhap.html?r="+window.location.href;
				}
			}else{
				alert("Yêu cầu của bạn đã đã được ghi nhận. Chúng tôi sẽ kiểm tra lại nhận xét này.");
				th.off("click").addClass("success");
			}
		});
	});

	$("#nhanxet .item .replycomment .inputsubmit").on('click',function(){
		var th=$(this);
		if(th.val()=="Gửi"){
			th=th.parent().parent().parent();
			var hoten=th.find("input[name='hoten']").val().trim();
			var email=th.find("input[name='email']").val().trim();
			var valuecomment=th.find("textarea").val().trim();
			var danhgia=th.find(".vdg").val();
			var masp=th.find(".v_masp").val();
			var nx_cha=th.find(".v_nx_cha").val();
			
			if(hoten==""){
				alert('Vui lòng nhập họ tên.');
				th.find("input[name='hoten']").focus();
			}else{
				if(!isEmail(email)){
					alert('Email không hợp lệ.');
					th.find("input[name='email']").focus();
				}else{
					if(valuecomment==""){
						alert("Vui lòng nhập nội dung nhận xét.");
						th.find("textarea").focus();
					}else{
						th.find(".inputsubmit").val("Đang gửi...");
						var objdt={'hoten':hoten,'email':email,'valuecomment':valuecomment,'danhgia':danhgia,'masp':masp,'nx_cha':nx_cha};
						LoadJson(info.base_url+"/ajax/sendcomment",objdt,function(result){
							switch(result){
								case -1:
									alert("Có lỗi gửi thất bại.");
									break;
								default:
									th.find("textarea").val("");
									th.fadeOut();
									var item=document.createElement("div");
									item.setAttribute("class","item");

									item.innerHTML=insertComment(1,result,objdt);
									$(item).hide();
									th.parent().find(".subcomment").prepend(item);
									$(item).fadeIn(1000);

									if (navigator.onLine) {
										var noidunggui="<p><b>"+hoten+"</b> đã trả lời nhận xét của bạn trong sản phẩm <a href='"+$("#currenturl").html()+"'>"+$("title").text()+"</a></p><div><b>Bạn</b>: "+th.parent().parent().find(".contentcomment").eq(0).text().trim();
										noidunggui+=" - <i>"+th.parent().parent().find("h2 span").eq(0).text()+"</i><blockquote><b>"+hoten+"</b>: "+valuecomment+"</blockquote></div>";
										LoadJson("http://tienghoadidong.com/sendmail.php",{"content":noidunggui,"to":th.parent().parent().find("h2 i").eq(0).attr("ref"),"title":"Trả lời nhận xét của bạn trong vienthonga"},function(result){
											
										});									
									}

									removeEvent();
									addEvent();
									break;
							}
							th.find(".inputsubmit").val("Gửi");
						});
					}
				}
			}
		}
	});

}

function removeEvent(){
	var objdanhgia=$("#danhgiasp img,.dgspsub img");
	objdanhgia.off('mouseover');
	objdanhgia.off('mouseout');
	objdanhgia.off('click');
	$("#nhanxet .item .ctcomment .reply").off('click');
	$("#nhanxet .item .replycomment .inputsubmit").off('click');
	$("#nhanxet .item .ctcomment .like").off('click');
	$("#nhanxet .item .ctcomment .warning").off('click');
}

function getCurrentDate(){
	var currentdate = new Date();
    return (currentdate.getDay()+1)+"/"+(currentdate.getMonth()+1)+"/"+currentdate.getFullYear()+" "+currentdate.getHours() + ":" + currentdate.getMinutes()+":"+currentdate.getSeconds();
}


function insertComment(cha,idcha,obj){
	var htmlcode="<div class=\"left\">";
	htmlcode+="<img src=\""+info.base_url+"/public/Contents/Images/user.png\" />";
	htmlcode+="</div>";
	if(cha==0)
		htmlcode+="<div class=\"left\" style=\"width:750px;margin-left:10px;\">";
	else
		htmlcode+="<div class=\"left\" style=\"width:650px;margin-left:10px;\">";
	htmlcode+="<div class=\"infocomment\">";
	htmlcode+="<div class=\"left\">";
	htmlcode+="<h2><i>"+obj.hoten+"</i> - <span>"+getCurrentDate()+"</span></h2>";
	htmlcode+="</div>";
	htmlcode+="<div class=\"right\"><i>Đánh giá sản phẩm: </i>";
	for(var dx=0;dx<5;dx++){
		if(dx<obj.danhgia){
			htmlcode+="<img src="+info.base_url+"/public/Contents/Images/star_full.gif"+" />";
		}else{
			htmlcode+="<img src="+info.base_url+"/public/Contents/Images/star_empty.gif"+" />";	
		}
	}
	htmlcode+="</div><div class=\"clear\"></div>";
	htmlcode+="<div class=\"contentcomment\">"+obj.valuecomment+"</div>";
	htmlcode+="<div class=\"ctcomment\"><input type=\"hidden\" class=\"idnx\" value="+idcha+" />";
	if(cha==0)
	htmlcode+="<a href=\"javascript:void(0)\" class=\"reply\"><img src=\""+info.base_url+"/public/Contents/Images/reply.png\"> Trả Lời</a>";
	htmlcode+="<a href=\"javascript:void(0)\" class=\"like\"><img src=\""+info.base_url+"/public/Contents/Images/like.png\"> Thích <strong></strong></a>";
	htmlcode+="<a href=\"javascript:void(0)\" class=\"warning\"><img src=\""+info.base_url+"/public/Contents/Images/warning.png\"> Báo cáo vi phạm</a></div>";
	htmlcode+="</div>";
	if(cha==0){
		htmlcode+="<div class=\"replycomment\">";
		htmlcode+="<textarea placeholder=\"Nội dung nhận xét (vui lòng nhập tiếng việt có đấu)...\" class=\"inputstyle\"></textarea><br />";
		htmlcode+="<div><input type=\"text\" placeholder=\"Họ tên\" class=\"inputstyle\" size=\"30\" name=\"hoten\" />";
		htmlcode+="<input type=\"email\" placeholder=\"Email\" class=\"inputstyle\" size=\"30\" name=\"email\" /></div>";
		htmlcode+="<div style=\"margin-top:5px;\"><div class=\"left\" style=\"margin-top:5px\">Đánh giá sản phẩm: </div>";
		htmlcode+="<div class=\"left dgspsub\" style=\"margin-top:5px\">";
		htmlcode+="<img src=\"public/Contents/Images/star-off.png\" alt=\"1\" title=\"Xấu\" />";
		htmlcode+="<img src=\"public/Contents/Images/star-off.png\" alt=\"2\" title=\"Bình Thường\" />";
		htmlcode+="<img src=\"public/Contents/Images/star-off.png\" alt=\"3\" title=\"Tạm Được\" />";
		htmlcode+="<img src=\"public/Contents/Images/star-off.png\" alt=\"4\" title=\"Tốt\" />";
		htmlcode+="<img src=\"public/Contents/Images/star-off.png\" alt=\"5\" title=\"Cực Tốt\" />";
		htmlcode+="<input type=\"hidden\" class=\"vdg\" value=\"0\" />";
		htmlcode+="</div>";
		htmlcode+="<div class=\"left\" style=\"margin-left:10px\">";
		htmlcode+="<input type=\"hidden\" value="+idcha+" class=\"v_nx_cha\" />";
		htmlcode+="<input type=\"hidden\" value="+obj.masp+" class=\"v_masp\" />";
		htmlcode+="<input type=\"button\" value=\"Gửi\" class=\"inputsubmit\" />";
		htmlcode+="</div><div class=\"clearleft\"></div></div></div>";
		htmlcode+="<div class='subcomment'></div>";
	}
	htmlcode+="</div><div class=\"clearleft\"></div>";

	return htmlcode;
}