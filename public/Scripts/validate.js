/*
	1.
		max,min: kiểm tra chuỗi nhập vào có nằm trong khoảng từ min đên max không. VD:
		{
			'name':'hoten',
			'max':50,
			'min':5,
			'message':'tển đài từ 5 đến 50 khí tự'
		}
	2.
		trong: kiểm tra textbox có để trống không. VD
		{
			'name':'hoten',
			'rong':true,
			'message':'họ tên không được bỏ trống'
		}
	3.
		email: kiểm tra xem có phải là email không. VD:
		{
			'name':'email',
			'email':true,
			'message': 'email không hợp lệ'
		}
	4. 
		sodt: kiểm tra số điện thoại. VD:
		{
			'name':'sodt',
			'sodt':true,
			'message':'số điện thoại không hợp lệ'
		}
	5.
		sosanh: so sanh số nhập vào có bằng hoặc >= hoặc <= số quy định. VD:
		{
			'name':'tuoi',
			'sosanh':'>10 <20',
			'message':'tuổi phải từ 11 đến 19'
		} 
		{
			'name':'tuoi',
			'sosanh':'=10',
			'message':'tuổi phải bằng 10'
		} 
		{
			'name':'tuoi',
			'sosanh':'>=10 <=20',
			'message':'tuổi phải từ 10 đến 20'
		} 
		{
			'name':'tuoi',
			'sosanh':'>10 <=20',
			'message':'tuổi phải từ 11 đến 20'
		} 
		{
			'name':'tuoi',
			'sosanh':'>=10 <20',
			'message':'tuổi phải từ 10 đến 19'
		} 
	6. 
		date: kiểm tra có phải ngày tháng. VD
		{
			'name':'ngaysinh',
			'date':true,
			'message':'ngày sinh không hợp lệ'
		}
	7.
		so: có phải là số. VD
		{
			'name':'tuoi',
			'so':true,
			'message': 'tuổi phải là số từ 0-9'
		}
	8.
		string: có phải là ký tự. VD:
		{
			'name':'hoten',
			'string':true,
			'message':'họ tên phải là chữ. không có ký tự đặc biệt'
		}
	9.
		file: kiểm tra định dạng file. VD:
		{
			'name':'file',
			'file':true,
			'typefile':'image',
			'message':'vui lòng chọn 1 hình ảnh'
		}
		{
			'name':'file',
			'file':true,
			'typefile':'video',
			'message':'vui lòng chọn 1 video'
		}


*/


(function ($) {
    $.fn.kiemtra=function(options){
        check($(this),options);
    }
})($);

function checklength(obj,maxl,minl){
	if(obj.val().length>=minl && obj.val().length<=maxl){
		return true;
	}
	obj.on('keypress',function(){
		$(this).off('keypress').removeClass('error').attr('title','');
	});
	return false;
}


function isEmpty(obj){
	if(obj.is(":disabled")){
		obj.off('keypress').removeClass('error').attr('title','');
		return true;
	}
	if(obj.val().length>0){
		return true;
	}
	obj.on('keypress',function(){
		$(this).off('keypress').removeClass('error').attr('title','');
	});
	return false;
}

function isEmail(obj){
	if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(obj.val())) {
        return true
    }
    obj.on('keypress',function(){
		$(this).off('keypress').removeClass('error').attr('title','');
	});
	return false;
}

function isImage(file) {
    file = file.split(".").pop();
    switch (file) {
        case "jpg": case "png": case "gif": case "bimap": case "jpeg": case "ico":
            return true;
        default:
            return false;
    }
    return false;
}

function isVideo(file) {
    file = file.split(".").pop();
    switch (file) {
        case "mp4": case "avi": case "flv": case "3gp":
            return true;
        default:
            return false;
    }
    return false;
}

function isPhoneNumber(obj) {
	if (/^0([0-9]{2,3})(\.|-| )?([0-9]{3,4})(\.|-| )?([0-9]{3,4})/.test(obj.val())) {
        return true;
    }
    obj.on('keypress',function(){
		$(this).off('keypress').removeClass('error').attr('title','');
	});
    return false;

}


function checkIsNumber(obj) {
	return /^[0-9]+$/.test(obj.val());
}

function CompareObj(obj,c){
	var value=obj.val();
	if(c.indexOf("=")!=-1 && c.indexOf("<")==-1 && c.indexOf(">")==-1){
		return value==c.replace("=","").trim();
	}
	if(c.indexOf(">")!=-1){
		if(c.indexOf("<")!=-1){
			var nhohonbang=false;
			var lonhonbang=false;

			var mi;
			var ma;

			if(c.substring(c.indexOf(">")+1,c.indexOf(">")+2)=="="){
				nhohonbang=true;
				 mi=parseFloat(c.substring(c.indexOf(">")+2,c.indexOf("<")-1).trim());
			}else{
				 mi=parseFloat(c.substring(c.indexOf(">")+1,c.indexOf("<")-1).trim());
			}
			if(c.substring(c.indexOf("<")+1,c.indexOf("<")+2)=="="){
				lonhonbang=true;
				ma=parseFloat(c.substring(c.indexOf("<")+2,c.length).trim());
			}else{
				ma=parseFloat(c.substring(c.indexOf("<")+1,c.length).trim());
			}
			var v=parseFloat(obj.val().trim());

			if(nhohonbang && lonhonbang){
				return v>=mi && v<=ma;
			}

			if(nhohonbang){
				return v>=mi && v<ma;
			}

			if(lonhonbang){
				return v>mi && v<=ma;
			}

			return v>mi && v<ma;
			
		}
	}
}

function isDate(obj){
	var date=obj.val().trim().split("/");

	if(date.length==3){
		var ngay=parseInt(date[0]);
		var thang=parseInt(date[1]);
		var nam=parseInt(date[2]);

		if(thang>0 && thang<13){
			switch(thang){
				case 1: case 3: case 5: case 7: case 8: case 10: case 12:
					return ngay<32 && ngay>0;
				case 2:
					if((nam%4==0 && nam%100!=0) || nam%400==0)
						return ngay<30 && ngay>0;
					return ngay<29 && ngay>0;
				default:
					return ngay<31 && ngay>0;
			}
		}
		return false;
	}

	return false;
}

function isCharacter(obj){
	if(obj.is(":disabled")){
		obj.off('keypress').removeClass('error').attr('title','');
		return true;
	}
	return /^([a-zA-Z\u00A1-\uFFFF ])+$/.test(obj.val());
}

function isPrice(obj){
	if(obj.val().trim()=="")
		return false;
	if(obj.val().trim()=="0")
		return true;
	return /^[0-9]{1,3}( |-|\.|\,)?[0-9]{3}(( |-|\.|\,)?[0-9]{3})?(( |-|\.|\,)?[0-9]{3})?(( |-|\.|\,)?[0-9]{3})?$/.test(obj.val().trim());
}

function compare2obj(obj1,obj2){
	return obj1.val()==obj2.val();
}

function readURL(input) {
        if (input.files && input.files[0]) {
        	if(isImage(input.files[0].name)){
	            var reader = new FileReader();
	            reader.onload = function (e) {
	                $(input).parent().find(".asimg img").attr("src",e.target.result);
	            }
	            reader.readAsDataURL(input.files[0]);
            }else{
            	alert("vui lòng chọn 1 hình ảnh");
            }
        }
        else {
            $(input).parent().find(".asimg img").attr("src",  $(input).val());
        }
    }



function check(th,options){


	th.submit(function(){
		var flag=true;
		var error=false;

		for(var i=0;i<options.length;i++){
			error=false;
			if(options[i].name!=null){
				var obj=th.find('[name="'+options[i].name+'"]');
				if(options[i].max!=null && options[i].min!=null){
					if(!checklength(obj,options[i].max,options[i].min)){
						obj.addClass("error");
						if(options[i].message!=null)
								obj.attr('title',options[i].message);
							else
								obj.attr('title','chiều dài không hợp lệ');
						flag=false;
						error=true;
					}
				}else{
					if(options[i].trong!=null && options[i].trong){
						if(!isEmpty(obj)){
							obj.addClass("error");
							if(options[i].message!=null)
								obj.attr('title',options[i].message);
							else
								obj.attr('title','không được bỏ trống');
							flag=false;
							error=true;
						}
					}
				}
				if(options[i].email!=null && options[i].email && !error){
					if(!isEmail(obj)){
						obj.addClass("error");
						obj.attr('title','Email không hợp lệ');
						flag=false;
						error=true;
					}
				}
				if(options[i].sodt!=null && options[i].sodt && !error){
					if(!isPhoneNumber(obj)){
						obj.addClass("error");
						obj.attr('title','Số điện thoại không hợp lệ');
						flag=false;
						error=true;
					}
				}
				if(options[i].sosanh!=null && !error){
					if(!CompareObj(obj,options[i].sosanh)){
						obj.addClass("error");
						if(options[i].message!=null)
								obj.attr('title',options[i].message);
							else
								obj.attr('title','Không hợp lệ');
						obj.on('keypress',function(){
							$(this).off('keypress').removeClass('error').attr('title','');
						});
						flag=false;
						error=true;
					}
				}
				if(options[i].so!=null && options[i].so && !error){
					if(!checkIsNumber(obj)){
						obj.addClass("error");
						if(options[i].message!=null)
								obj.attr('title',options[i].message);
							else
								obj.attr('title','Phải là số');
						obj.on('keypress',function(){
							$(this).off('keypress').removeClass('error').attr('title','');
						});
						flag=false;
						error=true;
					}
				}
				if(options[i].date!=null && options[i].date && !error){
					if(!isDate(obj)){
						obj.addClass("error");
						if(options[i].message!=null)
								obj.attr('title',options[i].message);
							else
								obj.attr('title','thời gian không hợp lệ');
						obj.on('keypress',function(){
							$(this).off('keypress').removeClass('error').attr('title','');
						});
						flag=false;
						error=true;
					}
				}
				if(options[i].string!=null && options[i].string && !error){
					if(!isCharacter(obj)){
						obj.addClass("error");
						if(options[i].message!=null)
								obj.attr('title',options[i].message);
							else
								obj.attr('title','không được có ký tự đặc biệt');
						obj.on('keypress',function(){
							$(this).off('keypress').removeClass('error').attr('title','');
						});
						flag=false;
						error=true;
					}
				}
				if(options[i].gia!=null && options[i].gia && !error){
					if(!isPrice(obj)){
						obj.addClass("error");
						if(options[i].message!=null)
								obj.attr('title',options[i].message);
							else
								obj.attr('title','Giá không hợp lệ');
						obj.on('keypress',function(){
							$(this).off('keypress').removeClass('error').attr('title','');
						});
						flag=false;
						error=true;
					}
				}
				if(options[i].sosanhdoituong!=null && options[i].sosanhdoituong && !error){
					var obj2=th.find('[name="'+options[i].name2+'"]');
					if(!compare2obj(obj,obj2)){
						obj.addClass("error");
						if(options[i].message!=null)
								obj.attr('title',options[i].message);
							else
								obj.attr('title','sai');
						obj.on('keypress',function(){
							$(this).off('keypress').removeClass('error').attr('title','');
						});
						flag=false;
						error=true;
					}
				}
				if(options[i].file!=null && options[i].file){

					if(obj.val().length>0){
						if(options[i].typefile!=null){
							switch(options[i].typefile){
								case "image":
									if(!isImage(obj.val())){
										if(obj.hasClass("none")){
											obj.parent().find(".asimg").addClass('error').attr('title','Vui lòng chọn 1 hình ảnh');	
										}else{
											obj.attr('title','Vui lòng chọn 1 hình ảnh');
											obj.addClass("error");
										}
										obj.on("change",function(){
											$(this).off('change').removeClass('error').attr('title','');
										});
										flag=false;
									}
									break;
								case "video":
									if(!isVideo(obj.val())){
										if(obj.hasClass("none")){
											obj.parent().find(".asimg").addClass('error').attr('title','Vui lòng chọn 1 video');	
										}else{
											obj.attr('title','Vui lòng chọn 1 video');
											obj.addClass("error");
										}
										obj.on("change",function(){
											$(this).off('change').removeClass('error').attr('title','');
										});
										flag=false;
									}
									break;
							}
						}
					}else{
						flag=false;
						if(obj.hasClass("none")){
							obj.parent().find(".asimg").addClass('error').attr('title','Vui lòng chọn 1 file');	
						}else{
							obj.attr('title','Vui lòng chọn 1 file');
							obj.addClass("error");
						}
						obj.on("change",function(){
							$(this).off('change').removeClass('error');
						});
					}
				}
			}
		}
		return flag;
	});
	
}
