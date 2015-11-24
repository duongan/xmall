<?php

class GiaoHangController extends Zend_Controller_Action
{
	
    public function init()
    {

        $this->view->headTitle("Giao Hàng Toàn Quốc Miễn Phí");
		$this->view->headLink()->appendStylesheet($this->view->baseUrl()."/public/Contents/stylelienhe.css","all");
    }

    public function indexAction()
    {
		
    }

}