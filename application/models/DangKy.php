<?php
class Model_DangKy extends Zend_Db_Table_Abstract{
	
	protected $_name='khach_hang';
	protected $_primary='MAKH';

	public function DangKy($arr=null){
		return $this->insert($arr,'khach_hang');
	}

	public function KiemTra($where){
		$row= $this->fetchAll(
			$this->select()->from('khach_hang',array('MAKH'))->where($where)
		)->toArray();
	
		return count($row)>0;
	}
	
}