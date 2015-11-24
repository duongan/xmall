var qcright=document.getElementById("iconeventright");

endResize();

var tiout=null;

window.onresize=function(){
	clearTimeout(tiout);
	tiout=setTimeout(endResize,500);	
};

function endResize(){
	if(document.body.clientWidth>1180){
		var w=(document.body.clientWidth-1186-30);
		if(w>199){
			w=199;
		}
		qcright.style.width=(w+50)+"px";
		qcright.style.right=-(w-10)+"px";
	}else{
		qcright.style.width=0+"px";
	}
}