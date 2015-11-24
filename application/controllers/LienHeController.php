<?php

class LienHeController extends Zend_Controller_Action
{
	
    public function init()
    {
    	$this->view->headTitle("Liên hệ");
		$this->view->headLink()->appendStylesheet($this->view->baseUrl()."/public/Contents/stylelienhe.css","all");
    }

    public function indexAction()
    {
        // if(isset($_POST['name']))
        // {
        //     $s=new Zend_Session_Namespace();
        //     if($this->_request->getPost('captcha')==$s->captcha){
        //         $nguoigui=$_POST['name'];
        //         $email=$_POST['email'];
        //         $noidung=$_POST['contents'];
        //         $tieude=$_POST['tieude'];

        // 		$connmail = new Zend_Mail_Transport_Smtp ( 'smtp.gmail.com', array ('auth' => 'login', 'username' => 'vienthonga1203@gmail.com', 'password' => '123456789zz', 'ssl' => 'ssl', 'port' => 465 ) );
        //         Zend_Mail::setDefaultTransport ( $connmail );
        //         $mail = new Zend_Mail ( 'UTF-8' );
        //         $mail->setBodyHtml ('To: '.$nguoigui.'<br><br><b>'.$email.'</b><br>--------------------------------------------------------------------------------------------------------------<br>'. $noidung);
        //         $mail->addTo ( 'vienthonga1203@gmail.com' );
        //         $mail->setSubject ($tieude);       
                

        //         if(!$mail->send())
        //         {
        //             $this->view->send="khong";
        //         }
        //         else
        //         {
        //             $this->view->send="duoc";
        //         }
        //     }else{
        //         $this->view->message="Mã xác nhận sai";
        //     }
        // }

        if($this->_request->isPost()){
            if($this->_request->getPost('aaaaa')!=null){
                $s=new Zend_Session_Namespace();
                if($this->_request->getPost('captcha')==$s->captcha){
                    $this->view->send="re";   
                    $this->view->to="vienthonga1203@gmail.com";
                    $this->view->content="To: ".$this->_request->getPost("name")."<br><br>Email: ".$this->_request->getPost('email')."<br><br>----------------------------------------------------------------------------------<br>".$this->_request->getPost('contents');
                    $this->view->subject="Liện hệ - ".$this->_request->getPost('tieude');
                }else{
                    $this->view->message="Mã xác nhận sai";
                }

            }else{
                $this->view->send="duoc";
            }
        }

    }

}