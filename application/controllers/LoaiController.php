<?php

class LoaiController extends Zend_Controller_Action
{
	
    public function init()
    {
		$this->view->headLink()->appendStylesheet($this->view->baseUrl()."/public/Contents/stylecategory.css","all");
    }

    public function indexAction()
    {
    	$model=new Model_LoaiSP();
		$info=$model->getInfo($this->_request->getParam('name'));
		if(count($info)==0){
			$this->_redirect('/');
		}
		$this->view->info=$info[0];
		$this->view->headTitle($this->view->info["TEN_LOAI"]);
		$sort="MASP DESC";
		if($this->_request->getParam("sort")!=null){
			switch ($this->_request->getParam("sort")) {
				case '2':
					$sort="MASP";
					break;
				case '3':
					$sort="GIA";
					break;
				case '4':
					$sort="GIA DESC";
					break;
			}
		}

		$start=0;
        $count=16;

        if($this->_request->getParam('page')!=null){
            $start=$this->_request->getParam('page');
        }
        $where="";

        if($this->_request->getParam('bgia')!=null){
        	$where="GIA>".$this->_request->getParam('bgia');
        }
        if($this->_request->getParam('egia')!=null){
        	if($this->_request->getParam('bgia')!=null)
        		$where.=" AND GIA<".$this->_request->getParam('egia');
        	else
        		$where="GIA<".$this->_request->getParam('egia');
        }
        if($this->_request->getParam('gg')!=null){
        	$where="GIAM_GIA>0";
        }
        if($this->_request->getParam('km')!=null){
        	$where="KHUYENMAI<>'&lt;br&gt;'";
        }
        if($this->_request->getParam('rm')!=null){
        	$where="TEN_TRANGTHAI=2";
        }
        if($where!=""){
        	$where.=" and ";
        }

		if($this->view->info["LSP_CHA"]==0){
			$this->view->sp=$model->getSpAll($this->view->info["MALSP"],$sort,$start*$count,$count,$where);
			$sum=$model->countSpAll($this->view->info["MALSP"],$where);
			$this->view->hangsx=$model->getHang($this->view->info["MALSP"]);
			$this->view->nav="<li class=\"active\"><h1>".$this->view->info["TEN_LOAI"]."</h1></li>";
		}else{
			$this->view->sp=$model->getSp($this->view->info["MALSP"],$sort,$start*$count,$count,$where);
			$sum=$model->countSp($this->view->info["MALSP"],$where);
			$lspcha=$model->getLSPCHA($this->view->info["LSP_CHA"]);
			$this->view->nav="<li><a href='".$lspcha["URL"].".html'>".$lspcha['TEN_LOAI']."</a><span style=\"margin-left:5px;\">»</span></li><li class=\"active\"><h1>".$this->view->info["TEN_LOAI"]."</h1></li>";
		}

		$this->view->arr_page=array(
            'sum'=>$sum,
            'current'=>$start,
            'count'=>$count
        );

    }

}