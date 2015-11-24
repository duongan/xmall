<?php 
class Model_Search extends Zend_Db_Table_Abstract{
	
	protected $_name='loai_san_pham';
	protected $_primary='MALSP';

	public function getLSP(){
		$this->_name="loai_san_pham";
		return $this->fetchAll(
			$this->select()->from('loai_san_pham',array('MALSP','URL','TEN_LOAI'))->where('HIEN_THI=1 AND LSP_CHA=0')
		)->toArray();
	}

	public function getSp($sort,$start,$count,$where){
		$this->_name="san_pham";
		return $this->fetchAll(
			$this->select()->from('san_pham',array('MASP','TENSP','URL','GIA','GIAM_GIA','HINHANH','TOM_TAT_TS','ICON_TRANGTHAI'))->where($where.' and HIEN_THI=1 and DA_XOA=0')->order($order)->limit($count,$start)
		)->toArray();
	}

	public function countSp($where){
		$this->_name="san_pham";
		return count($this->fetchAll(
			$this->select()->from('san_pham',array('MASP'))->where($where.' and HIEN_THI=1 and DA_XOA=0')
		)->toArray());
	}

	public function ChuyenDauThanhKhongDau($str){
		 $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
		  $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
		  $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
		  $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
		  $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
		  $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
		  $str = preg_replace("/(đ)/", 'd', $str);

		  return $str;
	}

	public function searchTinTuc($key){
		$this->_name="tin_tuc";
		return $this->fetchAll(
			$this->select()->from('tin_tuc',array('MATT','TIEUDE','URL','HINHANH','MOTA','THOIGIAN'))->where("(TIEUDE like '%".$key."%' OR TUKHOA like '%".$key."%') AND HIEN_THI=1")->order("MATT DESC")
		)->toArray();
	}

 }
 ?>