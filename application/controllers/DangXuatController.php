<?php

class DangXuatController extends Zend_Controller_Action
{
	
    public function init()
    {
      $this->getHelper("layout")->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function indexAction()
    {
        $s=new Zend_Session_Namespace();
         unset($s->login);
         if(isset($_COOKIE['login']))
         setcookie('login',null,time()-60*60*24);
         $this->_redirect($_SERVER["HTTP_REFERER"]);
    }

}