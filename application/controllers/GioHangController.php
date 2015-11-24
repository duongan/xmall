<?php

class GioHangController extends Zend_Controller_Action
{
	
    public function init()
    {
		
    }

    public function indexAction()
    {
        $s=new Zend_Session_Namespace();
        $model=new Model_GioHang;
        $model->addSession($s);
        if($this->_request->isPost()){
            if($this->_request->getPost('removeall')!=null){
                unset($s->cart);
                $this->_redirect($this->_request->getPost("re"));
            }else{
                if($this->_request->getPost("remove")){
                    foreach ($this->_request->getPost("sp") as $item) {
                        $model->remove($item);
                    }
                    if(!isset($s->cart)){
                        $this->_redirect($this->_request->getPost("re"));
                    }
                }else{
                    if(isset($s->login))
                            $s->radio=array("GHICHU"=>$this->_request->getPost('ghichu'),"TTTT"=>(bool)$this->_request->getPost('chttt'),"GHTN"=>(bool)$this->_request->getPost('htgh'),"a"=>(int)$this->_request->getPost('a'));
                        else
                            $s->infoCus=array("TENKH"=>$this->_request->getPost('tennm'),"EMAIL"=>$this->_request->getPost('emailnm'),"DIENTHOAI"=>$this->_request->getPost('dienthoainm'),"DIACHI"=>$this->_request->getPost('diachinm'),'TENNN'=>$this->_request->getPost('tennn'),'DIENTHOAINN'=>$this->_request->getPost('dienthoainn'),'DIACHINN'=>$this->_request->getPost('diachinn'),"GHICHU"=>$this->_request->getPost('ghichu'),"TTTT"=>(bool)$this->_request->getPost('chttt'),"GHTN"=>(bool)$this->_request->getPost('htgh'),"a"=>(int)$this->_request->getPost('a'));
                    if($this->_request->getPost("captcha")==$s->captcha){
                    $model2=new Model_Admin();
                    
                    if(!isset($s->login)){
                        if($model->getEmail($this->_request->getPost("emailnm"))){
                            $this->view->message="Email <b>".$this->_request->getPost("emailnm")."</b> đã là khách hàng của chúng tôi. Nếu bạn là chủ của email này vui lòng <u><a href='".$this->view->baseUrl()."/dang-nhap.html'>đăng nhập</a></u> vào website để tiếp tục mua hàng. Còn không phải vui lòng điền email khác.";
                        }else{
                            $tenn=$this->_request->getPost("tennm");
                            $dienthoainn=$this->_request->getPost('dienthoainm');
                            $diachinn=$this->_request->getPost('diachinm');
                            if($this->_request->getPost("a")==2){
                                $tenn=$this->_request->getPost('tennn');
                                $dienthoainn=$this->_request->getPost('dienthoainn');
                                $diachinn=$this->_request->getPost('diachinn');
                            }
                            $makh= $model2->Them(array(
                                "MAKH"=>'NULL',
                                'TENKH'=>$this->_request->getPost("tennm"),
                                'NGAYSINH'=>'1990/1/1',
                                'CMND'=>"",
                                'EMAIL'=>$this->_request->getPost("emailnm"),
                                'MATKHAU'=>'123456',
                                'DIENTHOAI'=>$this->_request->getPost('dienthoainm'),
                                'DIACHI'=>$this->_request->getPost('diachinm'),
                                'TENNN'=>$tenn,
                                'DIENTHOAINN'=>$dienthoainn,
                                'DIACHINN'=>$diachinn,
                                'DIEM'=>0,
                                'KHOA'=>false
                            ),'khach_hang');

                            if($makh!=0){
                                $this->view->taokh=true;
                                $this->view->kh=array("TENKH"=>$this->_request->getPost("tennm"),"DIENTHOAI"=>$this->_request->getPost('dienthoainm'),"DIACHI"=>$this->_request->getPost('diachinm'));
                                $s->login = array("TEN"=>$this->_request->getPost("tennm"),"ID"=>$makh);
                            }
                        }
                    }else{
                        $makh=$s->login["ID"];
                         $this->view->kh=$model->getThongTinKH($s->login["ID"])[0];
                    }
                   
                    if($makh==0){
                        if($this->view->message==null)
                        $this->view->message="Tạo khách hàng thất bại.";
                    }else{
                        $tienno=0;
                        if($this->_request->getParam('autosubmit')==null){
                            $tienno=$model->getSum();
                        }
                        $mahd=$model2->Them(array(
                                    'MADH'=>null,
                                    'MAKH'=>$makh,
                                    'NGAYLAP'=>$model->getCurrentDate(),
                                    'GHICHU'=>$this->_request->getPost('ghichu'),
                                    'SOTIENNO'=>$tienno,
                                    'DAXEM'=>false,
                                    'HOANTHANH'=>false,
                                    'GIAOHANG'=>(boolean)$this->_request->getPost('htgh'),
                                    'THANHTOAN'=>(boolean)$this->_request->getPost('chttt')
                                    ),'don_hang');
                                foreach ($s->cart as $item) {
                                    $model2->Them(array('MADH'=>$mahd,'MASP'=>$item["MASP"],'SOLUONG'=>$item["SL"],'GIA'=>$item["DG"]),'ctdh');
                                }
                                $this->view->success=true;
                                $this->view->cart=$s->cart;
                                $this->view->sum=$model->getSum();
                                $this->view->mahd=$mahd;
                                $model->tangdiem($makh,$this->view->sum/1000000);
                                unset($s->cart);
                            }
                    }else{
                        $this->view->message="Mã xác nhận sai";
                    }
                }
            }
        }
           
        $this->view->headLink()->appendStylesheet($this->view->baseUrl()."/public/Contents/stylegiohang.css","all");
        $this->view->headTitle("Giỏ hàng của bạn");
        if($this->view->success==null){
            
            if($this->_request->getParam("id")!=null){
                $model->AddToCart($this->_request->getParam("id"));
                $this->_redirect("/gio-hang");
            }
    		
        
            $this->view->cart=$s->cart;
            $this->view->sum=$model->getSum();
            
            if(isset($s->login)){
                $this->view->info=$model->getThongTinKH($s->login["ID"])[0];
                if(isset($s->radio)){
                    $this->view->info['TTTT']=$s->radio['TTTT'];
                    $this->view->info['GHTN']=$s->radio['GHTN'];
                    $this->view->info['a']=$s->radio['a'];
                    $this->view->info['GHICHU']=$s->radio['GHICHU'];
                }
            }else{
                if(isset($s->infoCus)){
                    $this->view->nodis=true;
                     $this->view->info=$s->infoCus;
                }
            }

            if(isset($s->message)){
                $this->view->message=$s->message;
                unset($s->message);
            }
        }
    }

    public function nlAction(){
       
        if(!$this->_request->isPost()){
            $this->_redirect("gio-hang");
        }
        $this->getHelper("layout")->disableLayout();
         $s=new Zend_Session_Namespace();
        $model=new Model_GioHang;
      
            if(!isset($s->login)){
                $s->infoCus=array("TENKH"=>$this->_request->getPost('tennm'),"EMAIL"=>$this->_request->getPost('emailnm'),"DIENTHOAI"=>$this->_request->getPost('dienthoainm'),"DIACHI"=>$this->_request->getPost('diachinm'),'TENNN'=>$this->_request->getPost('tennn'),'DIENTHOAINN'=>$this->_request->getPost('dienthoainn'),'DIACHINN'=>$this->_request->getPost('diachinn'),"GHICHU"=>$this->_request->getPost('ghichu'),"TTTT"=>(bool)$this->_request->getPost('chttt'),"GHTN"=>(bool)$this->_request->getPost('htgh'),"a"=>(int)$this->_request->getPost('a'));
                
                if($this->_request->getPost("captcha")!=$s->captcha){
                    if($model->getEmail($this->_request->getPost('emailnm')))
                    {
                      $s->message="Email <b>".$this->_request->getPost("emailnm")."</b> đã là khách hàng của chúng tôi. Nếu bạn là chủ của email này vui lòng <u><a href='".$this->view->baseUrl()."/dang-nhap.html'>đăng nhập</a></u> vào website để tiếp tục mua hàng. Còn không phải vui lòng điền email khác.";
                         $this->_redirect("gio-hang");   
                    }
                }else{
                    $s->message="Mã xác nhận sai";
                         $this->_redirect("gio-hang");   
                }
               
            }else{
                $s->radio=array("GHICHU"=>$this->_request->getPost('ghichu'),"TTTT"=>(bool)$this->_request->getPost('chttt'),"GHTN"=>(bool)$this->_request->getPost('htgh'),"a"=>(int)$this->_request->getPost('a'));
            }

        $this->view->hoadontt=$model->getIDHD();
        $model->addSession($s);
        $this->view->sum=$model->getSum();
    }

}