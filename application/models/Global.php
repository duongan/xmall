<?php
class Model_Global extends Model_Database{

	public function getDataMenu()
	{
		$arr=$this->select('MALSP,ICON_NHO,URL,TEN_LOAI,LSP_CHA')->where("HIEN_THI_MENU=1")->where("HIEN_THI=1")->from('loai_san_pham')->orderBy('SAP_XEP_TC,MALSP')->toArray();
		

		$menu="";

		$length=count($arr);
		for($i=0;$i<$length;$i++){
			if($arr[$i]["LSP_CHA"]==0){
				$menu.="<li id='menuid1'><a href='".$arr[$i]["URL"].".html'><i class='left ic' style='background:url(\"uploads/images/loaisp/".$arr[$i]["ICON_NHO"]."\") no-repeat center center'></i>".$arr[$i]["TEN_LOAI"]."</a>";

				$submenu="";
				$urlp=$arr[$i]["URL"];

				for($j=$i+1;$j<$length;$j++){
					if($arr[$j]['LSP_CHA']==$arr[$i]['MALSP']){
						$submenu.="<li><a href='".$arr[$j]["URL"].".html'>".$arr[$j]["TEN_LOAI"]."</a></li>";
					}else{
						break;
					}
				}
				$i=$j-1;

				if($submenu!=""){
					$menu.="<ul><div class='contentmenuitem'>".$submenu."<div class='clearleft'></div><hr><li><a href='".$urlp.".html?bgia=10000000'>HÀNG CAO CẤP</a></li><li><a href='".$urlp.".html?bgia=5000000&egia=10000000'>HÀNG TRUNG CẤP</a></li><li><a href='".$urlp.".html?bgia=0&egia=5000000'>HÀNG PHỔ THÔNG</a></li><li><a href='".$urlp.".html?gg=1'>HÀNG GIẢM GIÁ</a></li></div></ul>";
				}
				$menu.="</li>";
			}
		}

		return $menu;
	}

	public function getSpHot(){
		return $this->from('san_pham')->select("MASP,TENSP,URL")->where("HIEN_THI=1")->limit(0,15)->orderBy('XEM DESC')->toArray();
	}

	public function getInfo($id){
		$row=$this->select('MAKH,TENKH')->from('khach_hang')->where('MAKH='.$id)->where('KHOA=0')->toArray();

		if(count($row)>0){
			return array("TEN"=>$row[0]["TENKH"],"ID"=>$row[0]["MAKH"]);
		}

		return null;
	}
	
}