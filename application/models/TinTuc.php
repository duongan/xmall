<?php 
class Model_TinTuc extends Zend_Db_Table_Abstract{
	
	protected $_name='tin_tuc';
	protected $_primary='MATT';

	public function getTinTuc($start,$count){

		return $this->fetchAll(
			$this->select()->from('tin_tuc',array('MATT','TIEUDE','URL','MOTA','HINHANH','THOIGIAN','TUKHOA'))->where("HIEN_THI=1")->order("MATT DESC")->limit($count,$start)
		)->toArray();
    }

    public function countNews(){
        return count($this->fetchAll($this->select()->from('tin_tuc',array('MATT'))->where("HIEN_THI=1"))->toArray());
    }

    public function getInfo($id){
    	$row=$this->fetchAll(
    		$this->select()->where('MATT='.$id." AND HIEN_THI=1")
        )->toArray();

    	if(count($row)==0)
    		return null;
    	return $row[0];
    }

    public function getTinHot(){
    	return $this->fetchAll(
    		$this->select()->from("tin_tuc",array('MATT','TIEUDE','URL'))->where("HIEN_THI=1")->order("XEM DESC")->limit(5,0)
    	)->toArray();
    }

    public function getTinMoi(){
    	return $this->fetchAll(
    		$this->select()->from("tin_tuc",array('MATT','TIEUDE','URL'))->where("HIEN_THI=1")->order("MATT DESC")->limit(5,0)
    	)->toArray();
    }

    public function getKM(){
        return $this->fetchAll(
            $this->select()->from("tin_tuc",array('MATT','TIEUDE','URL'))->where("HINHANH_SLIDE<>'' AND HIEN_THI=1")->order("MATT DESC")->limit(5,0)
        )->toArray();
    }

    public function updateView(array $arr,$id){
        $this->update($arr,'MATT='.$id);
    }

    public function getTinLienQuan($title,$id){
        $where="MATT<>".$id." AND (";

        foreach (explode(" ", $title) as $item) {
                $where.="TIEUDE LIKE '%".$item."%' OR ";
        }
        $where=mb_substr($where, 0,strlen($where)-4).")";

         return $this->fetchAll(
            $this->select()->from("tin_tuc",array('MATT','TIEUDE','URL'))->where($where." AND HIEN_THI=1")->order("MATT DESC")->limit(5,0)
        )->toArray();
    }

 }
 ?>