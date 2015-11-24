<?php

class GioiThieuController extends Zend_Controller_Action
{
	
    public function init()
    {
		
    }

    public function indexAction()
    {
		$xml=new Zend_Config_Xml('website.xml');

        $gt=null;

        foreach ($xml->info->menutop->item as $item) {
            if($item->id=="6"){
                $gt=$item;
                break;
            }
        }

        $this->view->headTitle($gt->t);
        $this->view->headMeta()->appendName("keywords",$gt->keys);
        $this->view->headMeta()->appendName("description",$gt->desc);
        $this->view->headLink()->appendStylesheet($this->view->baseUrl()."/public/Contents/stylelienhe.css","all");
    }

}