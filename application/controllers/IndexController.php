<?php



class IndexController extends Zend_Controller_Action
{

    public function init()
    {

    }

    public function indexAction()
    {
    	$xml=new Zend_Config_Xml('website.xml');

        $this->view->headTitle($xml->info->title);
        $this->view->headScript()->appendFile($this->view->baseUrl()."/public/Scripts/jshome.js","text/javascript");
        $this->view->headLink()->appendStylesheet($this->view->baseUrl()."/public/Contents/stylehome.css","all");
        $this->view->headMeta()->appendName("keywords",$xml->info->metakeys);
        $this->view->headMeta()->appendName("description",$xml->info->metadesc);

        $model=new Model_Index;

        $this->view->banner=$model->getBanner();
        $this->view->spgiamgia=$model->getSpGiamGia();
        $this->view->tintuc=$model->getTinTuc();
        $this->view->indexbox=$model->getIndexbox();
        $this->view->tintuchot=$model->getTTHot();
    }


}

