<?php 
class Model_User extends Zend_Db_Table_Abstract{
	
	protected $_name='khach_hang';
	protected $_primary='MAKH';

	public function getRow($row,$where){
		$row= $this->fetchAll(
			$this->select()->from('khach_hang',$row)->where($where)
		)->toArray();
	
		return $row[0];
	}


	public function KiemTra($where){
		$row= $this->fetchAll(
			$this->select()->from('khach_hang',array('MAKH'))->where($where)
		)->toArray();
	
		return count($row)>0;
	}

	public function DoiMatKhau($id,$matkhau){
		return $this->update(array("MATKHAU"=>$matkhau),'MAKH='.$id);
	}
	public function DoiThongTin($id,$arr){
		return $this->update($arr,'MAKH='.$id);
	}

 }
 ?>