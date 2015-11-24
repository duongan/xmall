<?php 
class Model_DangNhap extends Zend_Db_Table_Abstract{
	
	protected $_name='khach_hang';
	protected $_primary='MAKH';


	public function checklogin($email,$password){ 
		$row= $this->fetchAll(
			$this->select()->from('khach_hang',array('MAKH','TENKH','KHOA'))->where('EMAIL="'.$email.'" and MATKHAU="'.$password.'"')
		)->toArray();

		if(count($row)>0){
			return $row[0];
		}
		return null;
		
    }

    public function changePass($email,$pass){
    	$this->update(array('MATKHAU'=>$pass),'EMAIL="'.$email.'"');
    }

    public function checkEmail($email){
    	$row= $this->fetchAll(
			$this->select()->from('khach_hang',array('MAKH'))->where('EMAIL="'.$email.'"')
		)->toArray();

		if(count($row)>0){
			return true;
		}
		return false;
    }
 }
 ?>