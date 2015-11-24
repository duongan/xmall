<?php

class TuyenDungController extends Zend_Controller_Action
{
	
    public function init()
    {
		$xml=new Zend_Config_Xml('website.xml');

        $gt=null;

        foreach ($xml->info->menutop->item as $item) {
            if($item->id=="5"){
                $gt=$item;
                break;
            }
        }

        $this->view->headTitle($gt->t);
        $this->view->headMeta()->appendName("keywords",$gt->keys);
        $this->view->headMeta()->appendName("description",$gt->desc);
		$this->view->headLink()->appendStylesheet($this->view->baseUrl()."/public/Contents/stylelienhe.css","all");
    }

    public function indexAction()
    {
		$model=new Model_Myfile();

        $path=$_SERVER['DOCUMENT_ROOT'].$this->view->baseUrl()."/data/tuyendung.dat";

        $this->view->data=$model->readFile($path);
    }

}