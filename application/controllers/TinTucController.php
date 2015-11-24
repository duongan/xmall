<?php

class TinTucController extends Zend_Controller_Action
{
	
    public function init()
    {
        
		$this->view->headLink()->appendStylesheet($this->view->baseUrl()."/public/Contents/styletintuc.css","all");
    }

    public function indexAction()
    {
       
       $xml=new Zend_Config_Xml('website.xml');

        $gt=null;

        foreach ($xml->info->menutop->item as $item) {
            if($item->id=="2"){
                $gt=$item;
                break;
            }
        }

        $this->view->headTitle($gt->t);
        $this->view->headMeta()->appendName("keywords",$gt->keys);
        $this->view->headMeta()->appendName("description",$gt->desc);

        $start=0;
        $count=5;

        if($this->_request->getParam('page')!=null){
            $start=$this->_request->getParam('page');
        }

        $model=new Model_TinTuc;
        $this->view->data=$model->getTinTuc($start*$count,$count);

        $this->view->arr_page=array(
            'sum'=>$model->countNews(),
            'current'=>$start,
            'count'=>$count,
            'ShowLabel'=>false
        );

        $this->view->tinhot=$model->getTinHot();
        $this->view->tinkm=$model->getKM();
    }

    public function detailAction()
    {
        $model=new Model_TinTuc;
        $this->view->info=$model->getInfo($this->_request->getParam('id'));
        if($this->view->info==null){
            $this->_redirect('/');
        }

        $this->view->headTitle($this->view->info["TIEUDE"]);
        $this->view->tinhot=$model->getTinHot();
        $this->view->tinmoi=$model->getTinMoi();
        $this->view->tinkm=$model->getKM();
        $model->updateView(array("XEM"=>new Zend_Db_Expr("XEM+1")),$this->view->info["MATT"]);

        $this->view->ttlienquan=$model->getTinLienQuan($this->view->info["TIEUDE"],$this->view->info["MATT"]);
    }

}