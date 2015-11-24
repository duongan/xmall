<?php
class Model_ChiTiet extends Zend_Db_Table_Abstract{
	
	protected $_name='san_pham';
	protected $_primary='MASP';

	public function getInfo($id){
		$this->_name="san_pham";

		return $this->fetchRow('MASP='.$id.' AND HIEN_THI=1 AND DA_XOA=0');
	}

	public function getOplsp($id){
		$this->_name="loai_san_pham";

		return $this->fetchAll(
			$this->select()->from('loai_san_pham',array("MALSP","TEN_LOAI","URL","VIDEO","THONG_SO","TIN_TUC","LSP_CHA"))->where('MALSP='.$id)
		)->toArray()[0];
	}

	public function getSpCungGia($gia,$id,$idlsp){
		if($gia!=0){
			$this->_name="san_pham";
			return $this->fetchAll(
				$this->select()->from('san_pham',array("MASP","TENSP","URL","HINHANH","GIA","GIAM_GIA"))->where('HIEN_THI=1 AND DA_XOA=0 AND GIA BETWEEN '.($gia-1500000).' AND '.($gia+1000000).' AND MASP<>'.$id.' AND LOAISP in(select MALSP from loai_san_pham where LSP_CHA='.$idlsp.')')->order('GIA')
			)->toArray();
		}
		return null;	
	}
	public function getSpCungLoai($id,$idlsp){
		$this->_name="san_pham";
		return $this->fetchAll(
			$this->select()->from('san_pham',array("MASP","TENSP","URL","HINHANH","GIA","GIAM_GIA"))->where('HIEN_THI=1 AND DA_XOA=0 AND MASP<>'.$id.' AND LOAISP='.$idlsp)->order('MASP DESC')
		)->toArray();	
	}
	public function getTinTuc($key){
		$this->_name="tin_tuc";
		$query="";
		$arr=explode(",", $key);

		for ($i=0; $i <count($arr)-1 ; $i++) { 
			$query.="TUKHOA like '%".trim($arr[$i])."%' OR ";
			
		}
		$query.="TUKHOA like '%".trim($arr[count($arr)-1])."%'";

		return $this->fetchAll(
			$this->select()->from('tin_tuc',array("MATT","TIEUDE","URL","HINHANH"))->where("HIEN_THI=1 AND (".$query.")")->order("MATT DESC")
		)->toArray();
	}
	private function isEmail($email){
		if (preg_match_all("/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/", $email, $matches)) {
			return true;
		}
		return false;
	}
	public function insertComment($arr){
		if(!$this->isEmail($arr["EMAIL"])){
			return -2;
		}
		if($arr["HOTEN"]==""){
			return -3;
		}
		if($arr["NOIDUNG"]==""){
			return -4;
		}
		$this->_name="nhan_xet";
		return $this->insert($arr);
	}


	public function getNhanXet($id){
		$this->_name="nhan_xet";
		return $this->fetchAll(
				$this->select()->from('nhan_xet',array('MANX','HOTEN','EMAIL','NGAYDANG','NOIDUNG','NX_CHA','DANH_GIA','THICH','ADMIN'))->where("HIEN_THI=1")->where("MASP=".$id)->order("MANX DESC")
			)->toArray();
	}

	public function countComment($id){
		$this->_name="nhan_xet";
		return count($this->fetchAll(
				$this->select()->from('nhan_xet',array('MANX'))->where("MASP=".$id)->where("HIEN_THI=1")->where("NX_CHA=0")
			)->toArray());
	}


	public function updateView(array $arr,$id){
		$this->_name="san_pham";
		$this->update($arr,'MASP='.$id);
	}

	public function DanhGiaSP($id){
		$this->_name="nhan_xet";
		return $this->fetchAll(
				$this->select()->from('nhan_xet',array('avg(DANH_GIA) as sum'))->where("MASP=".$id)->where("HIEN_THI=1")
			)->toArray()[0]["sum"];
	}


	public function getCurrentDate(){
		date_default_timezone_set('Asia/Ho_Chi_Minh');

		$now=getdate();

		return $now["year"].'/'.$now["mon"].'/'.$now["mday"].' '.$now["hours"].':'.$now["minutes"].':'.$now["seconds"];
	}
}