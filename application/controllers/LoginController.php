<?php

class LoginController extends Zend_Controller_Action
{
	
	public function init()
    {
        $this->getHelper("layout")->disableLayout();
    }

    public function logoutAction(){
        $this->_helper->viewRenderer->setNoRender();

         $session=new Zend_Session_Namespace();
         unset($session->loginadmin);

         $this->_redirect("/login");
    }

    public function quyenmkAction(){
        if($this->_request->isPost()){
            $model=new Model_Login;
            if($model->changeps2($this->_request->getPost("taikhoan"),$this->_request->getPost("mk2"),$this->_request->getPost("matkhau"))){
                $this->view->message="Đổi mật khẩu thành công.";
            }else{
                $this->view->message="Tài khoản hoặc mật khẩu cấp 2 sai.";
            }
        }
    }

    public function indexAction()
    {
		if($this->_request->isPost()){
            $session=new Zend_Session_Namespace();
            if($this->_request->getPost("captcha")==$session->captcha){
                $model=new Model_Login;
                $result=$model->login($this->_request->getPost("taikhoan"),$this->_request->getPost("matkhau"));
                if($result!=null){
                    if($result["KHOA"]==1){
                        $this->view->message="Tài khoản này đã bị khóa. Vui lòng liên hệ admin.";
                    }else{
                        $session=new Zend_Session_Namespace();
                        $session->loginadmin=array("ID"=>$result["MANV"],"HOTEN"=>$result["HOTEN"],"MATKHAU2"=>$result["MATKHAU2"]);
                        $this->_redirect("/admin");
                    }
                }else{
                    $this->view->message="Đăng nhập thất bại. Tài khoản hoặc mật khẩu sai, nếu bạn quyên mật khẩu vui lòng liên hệ admin để reset lại mật khẩu.";
                }
            }else{
                $this->view->message="Mã bảo vệ sai.";
            }
        }
    }

}