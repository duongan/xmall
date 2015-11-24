<?php
class Model_Login extends Zend_Db_Table_Abstract{
	
	protected $_name='nhan_vien';
	protected $_primary='MANV';

	public function login($taikhoan,$matkhau){
		$row= $this->fetchAll(
			$this->select()->from('nhan_vien',array('MANV','HOTEN',"KHOA","MATKHAU2"))->where('TAIKHOAN="'.$taikhoan.'"')->where("MATKHAU='".md5($matkhau)."'")
		)->toArray();
		if(count($row)>0){
			return $row[0];
		}
		return null;
	}	
	public function changeps2($taikhoan,$matkhau,$matkhaumoi){
		$row= $this->fetchAll(
			$this->select()->from('nhan_vien',array('MANV'))->where('TAIKHOAN="'.$taikhoan.'"')->where("MATKHAU2='".md5($matkhau)."'")
		)->toArray();
		if(count($row)>0){
			$this->update(array('MATKHAU'=>md5($matkhaumoi)),'MANV='.$row[0]['MANV']);
			return true;
		}
		return false;
	}	
}