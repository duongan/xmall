<?php

class DangKyController extends Zend_Controller_Action
{
    
    public function init()
    {
        $this->view->headTitle("Đăng ký tài khoản");
        $this->view->headLink()->appendStylesheet($this->view->baseUrl()."/public/Contents/styledangky.css","all");
    }

    public function indexAction()
    {
         $s=new Zend_Session_Namespace();
         //neu da  dang nhap roi thi khong cho vao trang nay
        if(isset($s->login)){
             $this->_redirect("/");
        }
        if($this->_request->isPost()){
            if($this->_request->getPost("captcha")==$s->captcha){
                $ngay=1;
                $thang=1;
                $nam=1970;
                
                 

                if($this->_request->getPost("ngaysinh")!="0"){
                    $ngay=(int)$this->_request->getPost("ngaysinh");
                }
                if($this->_request->getPost("thangsinh")!="0"){
                    $thang=(int)$this->_request->getPost("thangsinh");
                }
                if($this->_request->getPost("namsinh")!="0"){
                    $nam=(int)$this->_request->getPost("namsinh");
                }
                if(!isset($_POST['s_name'])){
                    $namenhan=$this->_request->getPost("name");
                    $addressnhan=$this->_request->getPost("address");
                    $phonenhan=$this->_request->getPost("phone");
                }else{
                    $namenhan=$this->_request->getPost("s_name");
                    $addressnhan=$this->_request->getPost("s_address");
                    $phonenhan=$this->_request->getPost("s_phone");
                }
                
                $arr=array(
                    'MAKH'=>null,
                    'TENKH'=>$this->_request->getPost("name"),
                    'NGAYSINH'=>$nam."/".$thang."/".$ngay,
                    'CMND'=>$this->_request->getPost("cmnd"),
                    'EMAIL'=>$this->_request->getPost("email"),
                    'MATKHAU'=>$this->_request->getPost("password"),
                    'DIENTHOAI'=>$this->_request->getPost("phone"),
                    'DIACHI'=>$this->_request->getPost("address"),
                    'TENNN'=>$namenhan,
                    'DIENTHOAINN'=>$phonenhan,
                    'DIACHINN'=>$addressnhan,
                    'DIEM'=>0,
                    'KHOA'=>false
                    );


                $model=new Model_DangKy;

                if($model->KiemTra('EMAIL="'.$arr['EMAIL'].'"')){
                    $this->view->message="Email đã tồn tại.";
                }else{
                     if($arr['CMND']!="" && $model->KiemTra('CMND="'.$arr['CMND'].'"')){
                         $this->view->message="CMND đã tồn tại.";
                     }
                     else{
                        if($model->KiemTra('DIENTHOAI="'.$arr['DIENTHOAI'].'"')){
                         $this->view->message="Số điện thoại đã tồn tại.";
                     }
                     else{
                        if($model->DangKy($arr)==-1){
                            $this->view->message="Có lỗi. Đăng ký thất bại, vui lòng kiểm tra lại.";
                        }else{
                            $this->_redirect("/dang-nhap.html");
                        }
                    }
                    }
                }
            }else{
                $this->view->message="Mã xác nhận sai";
            }
        }
    }

}