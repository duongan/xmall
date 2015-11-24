<?php
class Model_Index extends Zend_Db_Table_Abstract{
	
	protected $_name='loai_san_pham';

	public function getBanner(){
		return $this->fetchAll(
			$this->select()->from('loai_san_pham',array('TEN_LOAI','ICON_LON','URL','LOAI_BANNER'))->order('SAP_XEP_BANNER')->where('HIEN_THI_BANNER=1')
		)->toArray();
	}	

	public function getSpGiamGia(){
		$this->_name="san_pham";
		return $this->fetchAll(
			$this->select()->from('san_pham',array('MASP','TENSP','URL','GIA','GIAM_GIA','HINHANH','TOM_TAT_TS','KHUYENMAI','ICON_TRANGTHAI'))->order('MASP DESC')->where('GIAM_GIA>0 and HIEN_THI=1 and HIEN_THI_TC=1 and DA_XOA=0')->limit(8,0)
		)->toArray();
	}

	public function getTinTuc(){
		$this->_name="tin_tuc";
		return $this->fetchAll(
			$this->select()->from('tin_tuc',array('MATT','TIEUDE','URL','HINHANH'))->order('MATT DESC')->where('HIEN_THI=1 AND SLIDESHOW=0')->limit(5,0)
		)->toArray();
	}

	private function getSubindex($id){
		$this->_name="loai_san_pham";
		return $this->fetchAll(
			$this->select()->from('loai_san_pham',array('TEN_LOAI','URL'))->where('HIEN_THI=1')->where('LSP_CHA='.$id)->limit(3,0)
		)->toArray();
	}

	private function getProduct($idlsp){
		$this->_name="san_pham";
		return $this->fetchAll(
			$this->select()->from('san_pham',array('MASP','TENSP','URL','GIA','GIAM_GIA','HINHANH','TOM_TAT_TS','KHUYENMAI','ICON_TRANGTHAI'))->order('MASP DESC')->where('(LOAISP in(select MALSP from loai_san_pham where LSP_CHA='.$idlsp.') or LOAISP='.$idlsp.') and HIEN_THI=1 and HIEN_THI_TC=1 and DA_XOA=0')->limit(6,0)
		)->toArray();
	}

	private function getProductHot($idlsp){
		$this->_name="san_pham";
		return $this->fetchAll(
			$this->select()->from('san_pham',array('MASP','TENSP','URL','GIA','GIAM_GIA','HINHANH'))->order('XEM DESC')->where('(LOAISP in(select MALSP from loai_san_pham where LSP_CHA='.$idlsp.') or LOAISP='.$idlsp.') and HIEN_THI=1 and HIEN_THI_TC=1 and DA_XOA=0')->limit(4,0)
		)->toArray();
	}

	private function getProductBC($idlsp){
		$this->_name="san_pham";
		$list= $this->fetchAll(
			$this->select()->from('san_pham',array('MASP','TENSP','URL'))->order('SOLUONGBANDUOC DESC')->where('(LOAISP in(select MALSP from loai_san_pham where LSP_CHA='.$idlsp.') or LOAISP='.$idlsp.') and HIEN_THI=1 and HIEN_THI_TC=1 and DA_XOA=0')->limit(10,0)
		)->toArray();
		$length=0;
		$arr=array();
		foreach ($list as $item) {
			$length=$length+strlen($item["TENSP"]);
			if($length<120){
				$arr[]=$item;
			}else{
				break;
			}
		}
		return $arr;
	}

	public function getIndexbox(){
		$this->_name="loai_san_pham";
		$arr=array();
		$row=$this->fetchAll(
			$this->select()->from('loai_san_pham',array('MALSP','TEN_LOAI'))->order('SAP_XEP_TC')->where('HIEN_THI=1')->where("HIEN_THI_TC=1")
		)->toArray();

		foreach ($row as $item) {
			$arr[]=array("MA"=>$item["MALSP"],"TEN_LOAI"=>$item["TEN_LOAI"],"SUB"=>$this->getSubindex($item["MALSP"]),"pro"=>$this->getProduct($item["MALSP"]),"prohot"=>$this->getProductHot($item["MALSP"]),"spbc"=>$this->getProductBC($item["MALSP"]));
		}

		return $arr;

	}	

	public function getTTHot(){
		$this->_name="tin_tuc";
		return $this->fetchAll(
			$this->select()->from('tin_tuc',array('MATT','TIEUDE','URL','HINHANH','MOTA','THOIGIAN'))->where("HIEN_THI=1")->limit(4,0)->order("XEM DESC")
		)->toArray();
	}
}