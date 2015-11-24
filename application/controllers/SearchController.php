<?php

class SearchController extends Zend_Controller_Action
{
	
    public function init()
    {
    	$this->view->headTitle("Kết Quả Tìm Kiếm");
		$this->view->headLink()->appendStylesheet($this->view->baseUrl()."/public/Contents/stylecategory.css","all");
    }

    public function indexAction()
    {
        if($this->_request->getParam('key')==null){
            $this->_redirect('/');
        }
		$model=new Model_Search;
        $this->view->lsp=$model->getLSP();

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
        if($this->_request->getParam('lsp')!=null){
            $where="(LOAISP=".$this->_request->getParam('lsp')." OR LOAISP in(select MALSP from loai_san_pham where LSP_CHA=".$this->_request->getParam('lsp')."))";
        }
        if($where!=""){
            $where.=" and ";
        }
        $where.="(";
        $arrkey=explode(" ", $model->ChuyenDauThanhKhongDau(mb_strtolower($this->_request->getParam("key"))));
        for($i=0;$i<count($arrkey)-1;$i++){
            $where.="TENSP like '%".$arrkey[$i]."%' OR ";
        }
        $where.="TENSP like '%".$arrkey[count($arrkey)-1]."%')";

        $sp=$model->getSp($sort,$start*$count,$count,$where);

        $arrsp=array();

        foreach ($sp as $item) {
            $tensp=explode(" ", $model->ChuyenDauThanhKhongDau(mb_strtolower($item["TENSP"])));
            $dem=0;
            foreach ($arrkey as $k) {
                foreach ($tensp as $s) {
                    if($k==$s){
                        $dem++;
                    }
                }
            }
            $arrsp[]=array("info"=>$item,"dem"=>$dem);
        }

        for ($in=0; $in <count($arrsp) ; $in++) { 
            for ($jn=$in+1; $jn <count($arrsp) ; $jn++) { 
                if($arrsp[$in]["dem"]<$arrsp[$jn]["dem"]){
                    $temp=$arrsp[$in];
                    $arrsp[$in]=$arrsp[$jn];
                    $arrsp[$jn]=$temp;
                }
            }
        }

        $this->view->sp=$arrsp;


        $sum=$model->countSp($where);
        $this->view->arr_page=array(
            'sum'=>$sum,
            'current'=>$start,
            'count'=>$count
        );

        $this->view->tintuc=$model->searchTinTuc($this->_request->getParam('key'));

        
    }

}