<?php

class YeuThichController extends Zend_Controller_Action
{
	
    public function init()
    {
		
    }

    public function indexAction()
    {
        $this->view->headTitle("Sản phẩm của bạn");

        $this->view->headLink()->appendStylesheet($this->view->baseUrl()."/public/Contents/stylecategory.css","all");

        if($this->_request->getParam('id')!=null){
            $model=new Model_YeuThich();
            $model->add($this->_request->getParam('id'));
            $this->_redirect("yeu-thich");
        }

        if($this->_request->isPost()){
        	if(isset($_COOKIE['like'])){
				setcookie('like',null,time()-60*60*24*30);
			}
        }

    }

}