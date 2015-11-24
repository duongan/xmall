<?php

class AjaxController extends Zend_Controller_Action
{

	public function init()
    {	 
		$this->getHelper("layout")->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
    }

    public function hienthispAction()
    {
    	if($this->_request->isPost()){
	    	$model=new Model_Admin;
	    	$arr=array($this->_request->getPost("name")=>($this->_request->getPost("l")=="true")?true:false);
	    	echo $model->Sua($arr,'san_pham','MASP='.$this->_request->getPost('id'));
    	}
    }

    public function xoaspAction()
    {
    	if($this->_request->isPost()){
            $model=new Model_Admin;
    		foreach ($this->_request->getPost('arr') as $item) {
               $model->Sua(array('DA_XOA'=>true),'san_pham','MASP='.$item);
            }

            echo "1";
    	}
    }
	
	public function xoalspAction()
    {
    	$kq=0;
    	if($this->_request->isPost()){
	    	$model=new Model_Admin;
	    	$id=$this->_request->getPost('id');
	    	if(!$model->Check('LSP_CHA='.$id,'loai_san_pham')){
		    	if($model->Xoa('loai_san_pham','MALSP='.$id)>0){
		    		$kq=1;
		    	}else{
		    		$kq=2;
		    	}
	    	}else{
	    		$kq=3;
	    	}
    	}
    	echo $kq;
    }

    public function insertlsAction(){

        if($this->_request->isPost()){
            $model=new Model_Admin;
            $s=new Zend_Session_Namespace();
            $model->insertLichSu($this->_request->getPost('message'),$s->loginadmin['ID']);
        }
    }

    public function loadlsAction(){
        if($this->_request->isPost()){
            $model=new Model_Admin;
            echo json_encode($model->selectAll('lich_su',null,'nhan_vien.HOTEN,NOIDUNG,THOIGIAN','MALS DESC',$this->_request->getPost("start"),5,'nhan_vien','lich_su.MANV=nhan_vien.MANV'));
        }
    }

    public function changehttintucAction()
    {
    	if($this->_request->isPost()){
	    	$model=new Model_Admin;
	    	$arr=array("HIEN_THI"=>($this->_request->getPost("HT")=="true")?true:false);
	    	echo $model->Sua($arr,'tin_tuc','MATT='.$this->_request->getPost('MATT'));
    	}
    }

    public function suahienthilspAction()
    {
        if($this->_request->isPost()){
            $model=new Model_Admin;
            $arr=array($this->_request->getPost("loai")=>($this->_request->getPost("check")=="true")?true:false);
            echo $model->Sua($arr,'loai_san_pham','MALSP='.$this->_request->getPost('id'));
        }
    }

    public function changegroupAction()
    {
        if($this->_request->isPost()){
           $model=new Model_Admin;
           $arr=array("MANHOM"=>$this->_request->getPost("group"));
           echo $model->Sua($arr,'nhan_vien','MANV='.$this->_request->getPost('id'));
        }
    }

    public function changenhomnvAction()
    {
        if($this->_request->isPost()){
           $model=new Model_Admin;
           $arr=array("MANHOM"=>$this->_request->getPost("group"));
           echo $model->Sua($arr,'nhan_vien','MANV='.$this->_request->getPost('id'));
        }
    }



	public function slideAction()
    {
        $model=new Model_Admin;
        $arr=array();
        foreach ($model->selectAll('tin_tuc','SLIDESHOW=1','MATT,TIEUDE,URL,HINHANH_SLIDE','MATT DESC') as $item) {
            $arr[]=array("name"=>$item['HINHANH_SLIDE'],"alt"=>$item["TIEUDE"],"href"=>$item["MATT"]."_".$item['URL'].".html");
        }
		echo json_encode($arr);
    }

    public function datasearchAction()
    {
		$model=new Model_Admin;

        $arr=array();

        foreach ($model->selectAll('san_pham',null,'MASP,TENSP,URL,GIA,GIAM_GIA,HINHANH',"XEM DESC",0,70) as $item) {
            $arr[]=array("MASP"=>$item["MASP"],"TENSP"=>$item["TENSP"],"URL"=>$item["URL"],"GIA"=>$this->giathat($item["GIA"],$item["GIAM_GIA"]),'HINHANH'=>$item["HINHANH"],"count"=>0);
        }

        foreach ($model->selectAll('tin_tuc',null,'MATT,TIEUDE,URL,HINHANH',"XEM DESC",0,30) as $item) {
            $arr[]=array("MASP"=>$item["MATT"],"TENSP"=>$item["TIEUDE"],"URL"=>$item["URL"],"GIA"=>"0",'HINHANH'=>$item["HINHANH"],"count"=>0);
        }

		echo json_encode($arr);
    }

    public function spmoiAction(){
        if($this->_request->isPost()){
            $model=new Model_Admin;
            $arr=array();
            foreach ($model->selectAll('san_pham',null,'MASP,TENSP,URL,GIA,GIAM_GIA,HINHANH,TOM_TAT_TS,ICON_TRANGTHAI','MASP DESC',0,8) as $item) {
                $arr[]=array("MASP"=>$item["MASP"],"TENSP"=>$item["TENSP"],"URL"=>$item["URL"],"GIA"=>$this->giathat($item["GIA"],$item["GIAM_GIA"]),"HINHANH"=>$item["HINHANH"],"TOM_TAT_TS"=>$item["TOM_TAT_TS"],"ICON_TRANGTHAI"=>$item["ICON_TRANGTHAI"]);
            }
            
            echo json_encode($arr);
        }
    }

    public function spbanchayAction(){
        if($this->_request->isPost()){
            $model=new Model_Admin;
            $arr=array();
            foreach ($model->selectAll('san_pham',null,'MASP,TENSP,URL,GIA,GIAM_GIA,HINHANH,TOM_TAT_TS,ICON_TRANGTHAI','SOLUONGBANDUOC DESC',0,8) as $item) {
                $arr[]=array("MASP"=>$item["MASP"],"TENSP"=>$item["TENSP"],"URL"=>$item["URL"],"GIA"=>$this->giathat($item["GIA"],$item["GIAM_GIA"]),"HINHANH"=>$item["HINHANH"],"TOM_TAT_TS"=>$item["TOM_TAT_TS"],"ICON_TRANGTHAI"=>$item["ICON_TRANGTHAI"]);
            }
            
            echo json_encode($arr);
        }
    }

    private function giathat($gia,$giam){
        if($gia>0){
            if($giam==0)
                return number_format($gia,0,',','.')." VND";
            return number_format(((100-$giam)*$gia)/100,0,',','.')." VND";
        }
        return "Liên Hệ";
    }

    public function sendcommentAction(){
        if($this->_request->isPost()){
                $s=new Zend_Session_Namespace();
                if($s->captcha==$this->_request->getPost('captcha') || $this->_request->getPost('captcha')==null){
                    $model=new Model_ChiTiet;
                    $model2=new Model_Admin;
                    echo $model2->Them(array(
                        "MANX"=>'NULL',
                        "MASP"=>$this->_request->getPost("masp"),
                        "EMAIL"=>trim($this->_request->getPost("email")),
                        "HOTEN"=>trim($this->_request->getPost("hoten")),
                        "NGAYDANG"=>$model->getCurrentDate(),
                        "NOIDUNG"=>trim($this->_request->getPost("valuecomment")),
                        "NX_CHA"=>$this->_request->getPost('nx_cha'),
                        "DANH_GIA"=>$this->_request->getPost("danhgia"),
                        "THICH"=>0,
                        "VI_PHAM"=>0,
                        "HIEN_THI"=>1,
                        'ADMIN'=>(isset($s->loginadmin))?true:false,
                        'DA_XEM'=>false
                    ),'nhan_xet');
                }else{
                    echo -2;
                }
        }
    }

    public function likecmAction(){
        if($this->_request->isPost()){
            $model=new Model_Admin;
            echo $model->Sua(array("THICH"=>$this->_request->getPost("sl")),'nhan_xet','MANX='.$this->_request->getPost("idnx"));
        }
    }

    public function baocaovpAction(){
        if($this->_request->isPost()){
            $s=new Zend_Session_Namespace();
            if($s->login==null){
                echo -2;
            }else{
                $model=new Model_Admin;
                $sl=$model->getRow('nhan_xet','MANX='.$this->_request->getPost('idnx'),'VI_PHAM')["VI_PHAM"];
                $sl++;
                echo $model->Sua(array("VI_PHAM"=>$sl),'nhan_xet','MANX='.$this->_request->getPost("idnx"));
            }
        }
    }

    public function hienthicmAction(){
        if($this->_request->isPost()){
            $model=new Model_Admin;
            echo $model->Sua(array("HIEN_THI"=>($this->_request->getPost("HT")=="true")?true:false),'nhan_xet','MANX='.$this->_request->getPost("MANX"));
        }
    }

    public function laadmincmAction(){
        if($this->_request->isPost()){
            $model=new Model_Admin;
            echo $model->Sua(array("ADMIN"=>($this->_request->getPost("AD")=="true")?true:false),'nhan_xet','MANX='.$this->_request->getPost("MANX"));
        }
    }

    public function changecartAction(){
        if($this->_request->isPost()){
           $s=new Zend_Session_Namespace();
           $model=new Model_GioHang;
           $model->addSession($s);
           $result=$model->tangsl($this->_request->getPost("id"),$this->_request->getPost("sl"),false);
        echo json_encode(array("SUM"=>number_format($model->getSum(),0,',','.'),"TT"=>number_format($model->getTT($this->_request->getPost("id")),0,',','.'),"SUCCESS"=>$result)); 
        }
    }

    public function changeqhdAction(){
        if($this->_request->isPost()){
            $model=new Model_Admin;
            $soluong=$model->getRow('san_pham','MASP='.$this->_request->getPost('idsp'),'SOLUONG-SOLUONGBANDUOC AS SL')['SL'];
            if($soluong>=$this->_request->getPost('sl')){
                $result=true;
                $model->Sua(array("SOLUONG"=>$this->_request->getPost('sl')),'ctdh','MADH='.$this->_request->getPost('id').' AND MASP='.$this->_request->getPost('idsp'));
            }
            else{
                $result=false;
                $model->Sua(array("SOLUONG"=>$soluong),'ctdh','MADH='.$this->_request->getPost('id').' AND MASP='.$this->_request->getPost('idsp'));
            }
            $tong=$model->Tong('ctdh','SOLUONG*GIA','MADH='.$this->_request->getPost('id').' AND MASP='.$this->_request->getPost('idsp'));
            $model->Sua(array("SOTIENNO"=>$tong),'don_hang','MADH='.$this->_request->getPost('id'));
            $thanhtien=$model->getRow('ctdh','MASP='.$this->_request->getPost('idsp').' AND MADH='.$this->_request->getPost('id'),'SOLUONG*GIA AS TT')['TT'];
            echo json_encode(array("TT"=>number_format($thanhtien,0,',','.'),"max"=>$soluong,"SUCCESS"=>$result)); 
         
        }
    }

    public function addtocartAction(){
        if($this->_request->isPost()){
           $s=new Zend_Session_Namespace();
           $model=new Model_GioHang;
           $model->addSession($s);
           if($model->AddToCart($this->_request->getPost("id"))){
                echo $model->getSL();
           }else{
                echo 0;
           }
        }
    }

    public function addtolikeAction(){
        if($this->_request->isPost()){
           
             $model=new Model_YeuThich;
             echo $model->add($this->_request->getPost("id"));
        }
    }

    public function countlikeAction(){
        if(isset($_COOKIE['like'])){
            echo json_encode(array("c"=>count(explode(" ", $_COOKIE["like"]))));
        }else{
            echo json_encode(array("c"=>0));
        }
    }

    public function getdatalikeAction(){
        if(isset($_COOKIE["like"])){
            $model=new Model_Admin;
            $in=str_replace(" ", ",", $_COOKIE["like"]);

            $arr=array();
            foreach ($model->selectAll('san_pham','MASP in('.$in.')','MASP,TENSP,URL,GIA,GIAM_GIA,HINHANH,TOM_TAT_TS,ICON_TRANGTHAI','MASP DESC') as $item) {
                $arr[]=array("MASP"=>$item["MASP"],"TENSP"=>$item["TENSP"],"URL"=>$item["URL"],"GIA"=>$this->giathat($item["GIA"],$item["GIAM_GIA"]),"HINHANH"=>$item["HINHANH"],"TOM_TAT_TS"=>$item["TOM_TAT_TS"],"ICON_TRANGTHAI"=>$item["ICON_TRANGTHAI"]);
            }

            echo json_encode($arr);

        }else{
            echo json_encode(array());
        }
    }

    public function xoalikeAction(){
        if($this->_request->isPost()){
            if(isset($_COOKIE['like'])){
                setcookie('like',null,time()-60*60*24*30);
                echo "0";
            }
        }
    }

    public function getcaptchaAction(){
        if($this->_request->isPost()){
           $s=new Zend_Session_Namespace();
           echo json_encode(array($s->captcha));
        }
    }


    public function uploadAction(){
		if($this->_request->isPost()){
    		$model=new Model_Admin;
    		$image=$model->upload('image',UPLOAD_PATH.'/images/');
    		
             $size=getimagesize(UPLOAD_PATH.'/images/'.$image);
            $image=$this->view->baseUrl()."/uploads/images/".$image;

        	echo json_encode(array("upload"=>array("links"=>array("original"=>$image),"image"=>array("width"=>$size[0],"height"=>$size[1]))));
    	}
    }

    public function kiemtraxoaAction(){
        if($this->_request->isPost()){
            $s=new Zend_Session_Namespace();

            if($s->loginadmin["MATKHAU2"]==md5($this->_request->getPost("mk2"))){
               if($s->captcha==$this->_request->getPost("ma")){
                    echo 1;
               } else{
                echo 2;
               }
            }else{
                echo 0;
            }
        }
    }
}