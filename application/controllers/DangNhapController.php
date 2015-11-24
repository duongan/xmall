<?php

class DangNhapController extends Zend_Controller_Action
{
    
    public function init()
    {
        $this->view->headLink()->appendStylesheet($this->view->baseUrl()."/public/Contents/styledangnhap.css","all");
    }

    public function indexAction()
    {
        
        $s=new Zend_Session_Namespace();
         //neu da  dang nhap roi thi khong cho vao trang nay
        if(isset($s->login)){
             $this->_redirect("/");
        }
        $this->view->headTitle("Đăng nhập");
        if($this->_request->isPost())
        { 
                $email=$this->_request->getPost('email');
                $password=$this->_request->getPost('password');
                $login=new Model_DangNhap();


                $result=$login->checklogin($email,$password);
                if($result!=null)
                {
                    if($result['KHOA']==0){
                        $s->login = array("TEN"=>$result['TENKH'],'ID'=>$result['MAKH']);
                        if($this->_request->getPost('remember_me')!=null){
                            setcookie('login',$s->login["ID"],time()+60*60*24);
                        }
                        if($this->_request->getParam("r")==null)
                            $this->_redirect($_SERVER["HTTP_REFERER"]);
                        else
                             $this->_redirect($this->_request->getParam("r"));
                    }else{
                        $this->view->message="Tài khoản của bạn đã bị khóa. Vui lòng liên hệ email <b>tuan.trancong@uni.dntu.edu.vn</b> để biết thêm chi tiết";
                    }
                }else{
                    $this->view->message="Email hoặc mật khẩu sai";
                }
        }
        
    }

    public function quyenmkAction(){
         $s=new Zend_Session_Namespace();
         //neu da  dang nhap roi thi khong cho vao trang nay
        if(isset($s->login)){
             $this->_redirect("/");
        }
        $this->view->headTitle("Quyên mật khẩu");

        if($this->_request->getParam('step')!=null && $this->_request->getParam('user')!=null && $this->_request->getParam('code')!=null){
            $model=new Model_DangNhap();
            $pass=substr(md5(rand(0,999)),15,6);
            $model->changePass($this->_request->getParam('user'),$pass);

            $this->view->to=$this->_request->getParam('user');
            $this->view->content="Mật khẩu của bạn là: ".$pass;
            $this->view->send=true;
        }

        if($this->_request->isPost()){
            if($this->_request->getPost('submit')!=null){
               
                $this->view->to=$this->_request->getPost('to');
                $model=new Model_DangNhap();
                if($model->checkEmail($this->view->to)){
                    $this->view->send=true;
                }else{
                     $this->view->message="Email không tồn tại.";
                }
                $md5=substr(md5(rand(0,999)),0,15);
                $this->view->content="<div>Vui lòng click vào url dưới đế lấy lại mật khẩu.</div><a href='http://".$_SERVER['HTTP_HOST'].$this->view->baseUrl()."/quyen-mat-khau.html?step=1&user=".$this->_request->getPost('to')."&code=".$md5."'>http://".$_SERVER['HTTP_HOST'].$this->view->baseUrl()."/quyen-mat-khau.html?step=1&user=".$this->_request->getPost('to')."&code=".$md5."</a>";
            }else{
                $this->view->hide=true;
                $this->view->message="Mật khẩu đã được gửi đến email. Vui lòng truy cập email đế lấy lại mật khẩu.";
            }
        }

    }

}