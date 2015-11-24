<?php

class ThanhToanController extends Zend_Controller_Action
{
	
    public function init()
    {

        $this->view->headTitle("Thanh Toán Khi Nhận Hàng");
		$this->view->headLink()->appendStylesheet($this->view->baseUrl()."/public/Contents/stylelienhe.css","all");
    }

    public function indexAction()
    {
		$model=new Model_Myfile();

        $path=$_SERVER['DOCUMENT_ROOT'].$this->view->baseUrl()."/data/huongdan.dat";

        $this->view->data=$model->readFile($path);
    }

}