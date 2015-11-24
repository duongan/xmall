<?php

class CaptchaController extends Zend_Controller_Action
{
	
    public function init()
    {
    	
    }

    public function indexAction()
    {
		$this->getHelper("layout")->disableLayout();
		$s=new Zend_Session_Namespace();
        $str="qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM0123456789";
        $md5="";
        for($i=0;$i<5;$i++){
		  $md5.=substr($str, rand(0,61),1);
        }
		$this->view->md5=$md5;
		$s->captcha=$md5;
    }

}