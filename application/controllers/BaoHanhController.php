<?php

class BaoHanhController extends Zend_Controller_Action
{
	
	public function init()
    {

    }

    public function indexAction()
    {
		$xml=new Zend_Config_Xml('website.xml');

        $gt=null;

        foreach ($xml->info->menutop->item as $item) {
            if($item->id=="3"){
                $gt=$item;
                break;
            }
        }

        $this->view->headTitle($gt->t);
        $this->view->headMeta()->appendName("keywords",$gt->keys);
        $this->view->headMeta()->appendName("description",$gt->desc);
        $model=new Model_Myfile();

        $path=$_SERVER['DOCUMENT_ROOT'].$this->view->baseUrl()."/data/baohanh.dat";

        $this->view->data=$model->readFile($path);
    }

}