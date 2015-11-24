<?php

class ChiTietController extends Zend_Controller_Action
{
	
    public function init()
    {
        $this->view->headScript()->appendFile($this->view->baseUrl()."/public/Scripts/jsdetail.js","text/javascript");
        $this->view->headLink()->appendStylesheet($this->view->baseUrl()."/public/Contents/detail.css","all");
    }

    public function indexAction()
    {
		$model=new Model_ChiTiet;
        $this->view->info=$model->getInfo($this->_request->getParam("id"));
        $nav="<li><a href='/'>Trang Chủ</a><span style='margin-left:5px;'>»</span></li>";
        if(count($this->view->info)==0){
            $this->view->message="Sản phẩm không tồn tại";
            $nav.='<li class="active"><h1>Thông Báo</h1></li>';
            $this->view->headTitle("Không tìm thấy sản phẩm");
        }else{

            if($this->_request->isPost()){
                $s=new Zend_Session_Namespace();
                if($s->captcha==$this->_request->getPost('captcha')){
                    $result=$model->insertComment(array(
                        "MANX"=>'NULL',
                        "MASP"=>$this->_request->getPost("masp"),
                        "EMAIL"=>trim($this->_request->getPost("email")),
                        "HOTEN"=>trim($this->_request->getPost("hoten")),
                        "NGAYDANG"=>$model->getCurrentDate(),
                        "NOIDUNG"=>trim($this->_request->getPost("valuecomment")),
                        "NX_CHA"=>0,
                        "DANH_GIA"=>$this->_request->getPost("danhgia"),
                        "THICH"=>0,
                        "VI_PHAM"=>0,
                        "HIEN_THI"=>1,
                        'ADMIN'=>(isset($s->loginadmin))?true:false,
                        'DA_XEM'=>false
                    ));
                    if($result>0){
                        $_POST["valuecomment"]=null;
                    }else{
                        switch ($result) {
                            case -1:
                                echo "<script type='text/javascript'>alert('Có lỗi. Gửi thất bại.');</script>";
                                break;
                            case -2:
                                echo "<script type='text/javascript'>alert('Email không hợp lệ.');</script>";
                                break;
                            case -3:
                                echo "<script type='text/javascript'>alert('Vui lòng nhập tên của bạn.');</script>";
                                break;
                            case -4:
                                echo "<script type='text/javascript'>alert('Vui lòng nhập nội dung nhận xét.');</script>";
                                break;
                        }
                    }
                }else{
                    echo "<script type='text/javascript'>alert('Mã xác nhận sai.');</script>";
                }
            }


            $oplsp=$model->getOplsp($this->view->info["LOAISP"]);
            if($oplsp["LSP_CHA"]!=0){
                $truoc=array("URL"=>$oplsp["URL"],"TEN"=>$oplsp["TEN_LOAI"]);
                $oplsp=$model->getOplsp($oplsp["LSP_CHA"]);
                 $nav.="<li><a href='".$this->view->baseUrl()."/".$oplsp["URL"].".html'>".$oplsp["TEN_LOAI"]."</a><span style='margin-left:5px;'>»</span></li>";
                $nav.="<li><a href='".$this->view->baseUrl()."/".$truoc["URL"].".html'>".$truoc["TEN"]."</a><span style='margin-left:5px;'>»</span></li>";
            }else{
                 $nav.="<li><a href='".$this->view->baseUrl()."/".$oplsp["URL"].".html'>".$oplsp["TEN_LOAI"]."</a><span style='margin-left:5px;'>»</span></li>";
            }
            $nav.='<li class="active"><h1>'.$this->view->info["TENSP"].'</h1></li>';
            $this->view->headTitle($oplsp["TEN_LOAI"]." :: ".$this->view->info["TENSP"]);
            $this->view->oplsp=$oplsp;
            $this->view->cunggia=$model->getSpCungGia($this->view->info["GIA"],$this->view->info["MASP"],$this->view->oplsp["MALSP"]);
            $this->view->cungloai=$model->getSpCungLoai($this->view->info["MASP"],$this->view->info["LOAISP"]);
            if($this->view->oplsp["TIN_TUC"]==1)
                $this->view->tintuc=$model->getTinTuc($this->view->info["TUKHOA"]);
            $this->view->nhanxet=$model->getNhanXet($this->view->info["MASP"]);
            $this->view->countnx=$model->countComment($this->view->info["MASP"]);
            $this->view->danhgia=$model->DanhGiaSP($this->view->info["MASP"]);
            $model->updateView(array("XEM"=>new Zend_Db_Expr('XEM+1')),$this->view->info["MASP"]);
        }
         $this->view->navs=$nav;
    }

}