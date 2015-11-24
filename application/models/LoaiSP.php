<?php
class Model_LoaiSP extends Zend_Db_Table_Abstract{
	
	protected $_name='loai_san_pham';
	protected $_primary='MALSP';

	public function getInfo($url){
		$this->_name="loai_san_pham";
		return $this->fetchAll(
			$this->select()->from('loai_san_pham',array('MALSP','TEN_LOAI','URL','THONG_SO','LSP_CHA'))->where("HIEN_THI=1")->where("URL='".$url."'")
		)->toArray();
	}

	public function getSpAll($id,$order,$start,$count,$where){
		$this->_name="san_pham";
		return $this->fetchAll(
			$this->select()->from('san_pham',array('MASP','TENSP','URL','GIA','GIAM_GIA','HINHANH','TOM_TAT_TS','ICON_TRANGTHAI'))->where($where.'(LOAISP in(select MALSP from loai_san_pham where LSP_CHA='.$id.') or LOAISP='.$id.' ) and HIEN_THI=1 and DA_XOA=0')->order($order)->limit($count,$start)
		)->toArray();
	}

	public function getSp($id,$order,$start,$count,$where){
		$this->_name="san_pham";
		return $this->fetchAll(
			$this->select()->from('san_pham',array('MASP','TENSP','URL','GIA','GIAM_GIA','HINHANH','TOM_TAT_TS','ICON_TRANGTHAI'))->where($where.'LOAISP='.$id.' and HIEN_THI=1 and DA_XOA=0')->order($order)->limit($count,$start)
		)->toArray();
	}

	public function countSp($id,$where){
		$this->_name="san_pham";
		return count($this->fetchAll(
			$this->select()->from('san_pham',array('MASP'))->where($where.'LOAISP='.$id.' and HIEN_THI=1 and DA_XOA=0')
		)->toArray());
	}


	public function countSpAll($id,$where){
		$this->_name="san_pham";
		return count($this->fetchAll(
			$this->select()->from('san_pham',array('MASP'))->where($where.'(LOAISP in(select MALSP from loai_san_pham where LSP_CHA='.$id.') or LOAISP='.$id.' ) and HIEN_THI=1 and DA_XOA=0')
		)->toArray());
	}

	public function getHang($id){
		$this->_name="loai_san_pham";
		return $this->fetchAll(
			$this->select()->from('loai_san_pham',array('URL','TEN_LOAI'))->where('LSP_CHA='.$id.' and HIEN_THI=1')
		)->toArray();
	}

	public function getLSPCHA($id){
		$this->_name="loai_san_pham";
		return $this->fetchAll(
			$this->select()->from('loai_san_pham',array('URL','TEN_LOAI'))->where('MALSP='.$id)
		)->toArray()[0];
	}
	
}