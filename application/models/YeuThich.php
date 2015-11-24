<?php
class Model_YeuThich extends Zend_Db_Table_Abstract{
	
	protected $_name='san_pham';
	private $arr=array();
	public function __construct(){
		if(isset($_COOKIE["like"])){
			$this->arr=explode(" ", $_COOKIE["like"]);
		}

	
	}

	public function add($id){
		if(!$this->check($id))
			$this->arr[]=$id;
		if(isset($_COOKIE["like"])){
			setcookie('like',null,time()-60*60*24*30);
		}

		setcookie("like",$this->format(),time()+60*60*24*30);

		return count($this->arr);
	}

	private function format(){
		$str="";
		foreach ($this->arr as $item) {
			$str.=$item." ";
		}

		return trim($str);
	}

	private function check($id){
		foreach ($this->arr as $item) {
            if($item==$id){
               return true;
            }
        }
        return false;
	}


}