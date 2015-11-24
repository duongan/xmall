<?php
class Model_GioHang extends Zend_Db_Table_Abstract{
	
	protected $_name='san_pham';
	private $s;

	public function addSession($s1){
		$this->s=$s1;
	}

	public function AddToCart($id){
		if($this->kt($id)){
			$this->tangsl($id,1,true);
			return true;
		}
		$spnew=$this->getSP($id);
		if(isset($spnew) && $spnew["GIA"]>0 && ($spnew["TEN_TRANGTHAI"]==0 || $spnew["TEN_TRANGTHAI"]==1 || $spnew["TEN_TRANGTHAI"]==3 || $spnew["TEN_TRANGTHAI"]==6)){
			$arrnew=array(
					"MASP"=>$spnew["MASP"],
					"TENSP"=>$spnew["TENSP"],
					"URL"=>$spnew["URL"],
					"HINHANH"=>$spnew["HINHANH"],
					"DG"=>(((100-$spnew["GIAM_GIA"])*$spnew["GIA"])/100),
					"SL"=>1,
					"SLCON"=>$spnew["SLCON"],
					"TT"=>(((100-$spnew["GIAM_GIA"])*$spnew["GIA"])/100)
				);

			if(!isset($this->s->cart)){
				$this->s->cart=array();
			}

			$this->s->cart[]=$arrnew;
			return true;
		}
		return false;

	}

	public function tangsl($id,$sl,$f){
		if(isset($this->s->cart)){
			$arr=$this->s->cart;
			$flag=true;
			for ($i=0; $i < count($arr); $i++) { 
				if($arr[$i]["MASP"]==$id){
					if($f){
						if($arr[$i]["SL"]+1<=$arr[$i]["SLCON"])
							$arr[$i]["SL"]++;
						else{
							$arr[$i]["SL"]=$arr[$i]["SLCON"];
							$flag=false;
						}
					}else{
						if($sl<=$arr[$i]["SLCON"])
							$arr[$i]["SL"]=$sl;
						else{
							$arr[$i]["SL"]=$arr[$i]["SLCON"];
							$flag=false;
						}
					}
					$arr[$i]["TT"]=$arr[$i]["DG"]*$arr[$i]["SL"];
				}
			}

			$this->s->cart=$arr;
			return $flag;

		}
		return $flag;
	}

	public function kt($id){
		if(!isset($this->s->cart)){
			return false;
		}
		foreach ($this->s->cart as $item) {
			if($item["MASP"]==$id)
				return true;
		}
		return false;
	}

	public function getSum(){
		if(isset($this->s->cart)){
			$sum=0;
			foreach ($this->s->cart as $item) {
			 	$sum=$sum+$item["TT"];
			 } 
			 return $sum;
		}
		return 0;
	}

	public function getSL(){
		if(isset($this->s->cart)){
			return count($this->s->cart);
		}
		return 0;
	}

	public function getTT($id){
		if(isset($this->s->cart)){
			foreach ($this->s->cart as $item) {
				if($item["MASP"]==$id)
					return $item["TT"];
			}
			return null;
		}
		return null;
	}

	private function getSP($id){
		$this->_name="san_pham";
		$row=$this->fetchAll(
			$this->select()->from('san_pham',array("MASP","TENSP","URL","HINHANH","GIA","GIAM_GIA","TEN_TRANGTHAI","(SOLUONG-SOLUONGBANDUOC) as SLCON"))->where('MASP='.$id)
		)->toArray();
		if(count($row)>0)
			return $row[0];
		return null;	
	}

	public function remove($id){
		if(isset($this->s->cart)){
			if(count($this->s->cart)==1){
				unset($this->s->cart);
			}else{
				foreach ($this->s->cart as $key => $value) {
					if($id==$value['MASP'])
					unset($this->s->cart[$key]);	
				}
				
			}
		}
	}

	public function getEmail($email){
		$this->_name="khach_hang";
		return count($this->fetchAll(
			$this->select()->from('khach_hang',array('MAKH'))->where("EMAIL='".$email."'")
		)->toArray())>0;
	}

	public function getThongTinKH($id){
		$this->_name="khach_hang";
		return $this->fetchAll(
			$this->select()->from('khach_hang',array('TENKH','EMAIL','DIENTHOAI','DIACHI','TENNN','DIENTHOAINN','DIACHINN'))->where("MAKH=".$id)
		)->toArray();	
	}

	public function getCurrentDate(){
		date_default_timezone_set('Asia/Ho_Chi_Minh');

		$now=getdate();

		return $now["year"].'/'.$now["mon"].'/'.$now["mday"].' '.$now["hours"].':'.$now["minutes"].':'.$now["seconds"];
	}

	public function tangdiem($makh,$diem){
		$this->_name="khach_hang";
		return $this->update(array("DIEM"=>new Zend_Db_Expr('DIEM+'.$diem)),"MAKH=".$makh);
	}

	public function getIDHD(){
		$this->_name="don_hang";
		return $this->fetchAll(
			$this->select()->from('don_hang',array('max'=>new Zend_Db_Expr('max(MADH)')))
		)->toArray()[0]['max']+1;
	}
	
}