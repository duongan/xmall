<?php
class Model_Admin extends Model_Database{

	/**
	*	Chức năng: thêm mới 1 dòng vào csdl.
	*	@param: 	
	*		- $arr: dữ liệu thêm vào.
	*		- $table: table cần thêm
	* 	@return: id vừa thêm vào
	*/
	public function Them($arr,$table){
		return $this->from($table)->insert($arr);
	}


	/**
	*	Chức năng: sửa dữ liệu.
	*	
	* 	@param:
	*		- $arr: dữ liệu cần sửa.
	*		- $table: table cần sửa
	*		- $where: điều kiện sửa
	*
	*	@return: số dòng được sửa
	*/
	public function Sua($arr,$table,$where){
		return $this->from($table)->where($where)->update($arr);
	}

	public function SuaQuery($query){
		return $this->query($query)->_update();
	}


	/**
	*	Chức năng: Xóa dữ liệu
	*	@param:
	*		- $table: table cần xóa
	*		- $where: điều kiện xóa
	*
	*	@return: số được xóa	
	*/
	public function Xoa($table,$where){

		return $this->from($table)->delete($where);
	}

	/**
	*	Chức năng: Đếm số dòng không có điều kiện
	*	
	*	@param:
	*		- $table: table cần đếm
	*		- $countby: đếm theo cột nào trong csdl
	*
	*	@return: số dòng đến được
	*/
	public function countAll($table,$countby='*'){
		return $this->from($table)->count($countby);
	}

	/**
	*	Chức năng: Đếm số dòng có điều kiện
	*	
	*	@param:
	*		- $table: table cần đếm
	*		- $where: điều kiện đếm
	*		- $countby: đếm theo cột nào trong csdl
	*
	*	@return: số dòng đến được
	*/
	public function dem($table,$where,$countby='*'){

		return $this->from($table)->where($where)->count($countby);
	}

	/**
	*	Chức năng: Kiểm tra 1 giá trị có tồn tại
	*	
	*	@param:
	*		- $where: điều kiện kiểm tra
	*		- $table: table cần kiểm tra
	*
	*	@return: true nếu tồn tại. false không tồn tại
	*/
	public function Check($where,$table){
		$kq=$this->from($table)->where($where)->count();
		return $kq>0;
	}

	/**
	*	Chức năng: Tìm kiếm giá trị lớn nhất trong table
	*	
	*	@param:
	*		- $table: table cần tìm
	*		- $column: tìm theo cột nào
	*
	*	@return: giá trị lớn nhất của column table đó
	*/
	public function maxAll($table,$column){
		return $this->from($table)->max($column);
	}
	/**
	* Chức năng: Lấy 1 row trong table
	* @param:
	*		-	$table: table muốn lấy
	*		-	$where: điều kiện lấy
	*		-	$column: cốt lấy
	* @return: row lấy được
	*/
	public function getRow($table,$where,$column='*',$order=null){
		return $this->select($column)->from($table)->where($where)->orderBy($order)->toArray()[0];
	}

	/**
	*	Chức năng: Tìm kiếm giá trị lớn nhất trong table có điều kiện
	*	
	*	@param:
	*		- $table: table cần tìm
	*		- $column: tìm theo cột nào
	*
	*	@return: giá trị lớn nhất của column table đó
	*/
	public function maxwhere($table,$where,$column){
		return $this->from($table)->where($where)->max($column);
	}

	public function selectAll($table,$where,$column,$order=null,$start=null,$count=null,$join=null,$on=null,$join2=null,$on2=null)
	{
		return $this->from($table)->select($column)->where($where)->orderBy($order)->limit($start,$count)->join($join,$on)->join($join2,$on2)->toArray();
	}

	public function thongke(){
		return $this->from('don_hang')->select('sum(SOLUONG) as s,year(NGAYLAP) as y,month(NGAYLAP) as t')->join('ctdh','don_hang.MADH=ctdh.MADH')->groupBy('year(NGAYLAP),month(NGAYLAP)')->where("don_hang.HOANTHANH=1")->toArray();
	}

	public function Tong($table,$column,$where=null,$join=null,$on=null){
		return $this->from($table)->where($where)->join($join,$on)->sum($column);
	}

	public function thongke2(){
		return $this->from('don_hang')->select('sum(SOLUONG*GIA) as s,year(NGAYLAP) as y,month(NGAYLAP) as t')->join('ctdh','don_hang.MADH=ctdh.MADH')->groupBy('year(NGAYLAP),month(NGAYLAP)')->where("don_hang.HOANTHANH=1")->toArray();
	}

	public function dem2($table,$where,$countby='*',$join,$on,$join2,$on2){

		return $this->from($table)->where($where)->join($join,$on)->join($join2,$on2)->count($countby);
	}

	public function createFileName($name,$basename,$i,$root,$duoi){
		$var=new Zend_Validate_File_Exists();
		$var->addDirectory($root);

		if($var->isValid($name.".$duoi")){

			return $this->createFileName($basename."(".$i.")",$basename,$i+1,$root,$duoi);
		}

		return $name.".".$duoi;
	}

	public function currentDate(){
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		return getdate();
	}

	public function getFileName(){
		
		$now=$this->currentDate();

		return $now["mday"].$now["mon"].$now["year"].$now["hours"].$now["minutes"].$now["seconds"];
	}


	public function insertLichSu($message,$id){
		$now=$this->currentDate();
		if($this->dem('lich_su','NOIDUNG="'.$message.'" AND DAY(THOIGIAN)='.$now['mday'].' AND MONTH(THOIGIAN)='.$now['mon'].' AND YEAR(THOIGIAN)='.$now['year'],'MALS')==0){

			$this->from('lich_su')->insert(array('MALS'=>'NULL','MANV'=>$id,'NOIDUNG'=>$message,'THOIGIAN'=>array()));

		}
	}



	public function getWebsiteActive(){
		date_default_timezone_set('Asia/Ho_Chi_Minh');

		$now=getdate();

		$first = strtotime('2015/1/1'); 
        $second = strtotime($now["year"].'/'.$now["mon"].'/'.$now["mday"]); 
        return round(abs($first-$second)/(60*60*24)); 
	}

	public function isImage($type){
		return $type=="image/jpeg" || $type=="image/png" || $type=="image/gif";
	}


	public function upload($inputname,$path,$namen=null){

		$image="";

		if(isset($_FILES[$inputname])){

			if(!isset($namen)){
				$name=$this->getFileName();

				$arr=explode(".",$_FILES[$inputname]['name']);

				$filename=$this->createFileName($name,$name,1,$path,$arr[count($arr)-1]);
			}else{
				$filename=$namen;
			}

			if(move_uploaded_file($_FILES[$inputname]['tmp_name'],$path.$filename)){
				$image=$filename;
			}
		}
		return $image;
	}

	public function removeFile($inputname){
		$str=trim($_POST["removefile"]);

		if($str!=""){
			$arr=explode(" ", $str);

			if(isset($_FILES[$inputname])){
				for($i=0;$i<count($_FILES[$inputname]['name'])-1;$i++){
					if($_FILES[$inputname]['error'][$i]==0){
						foreach ($arr as $item) {
							if($item==$i){
								$_FILES[$inputname]['error'][$i]=4;
								break;
							}
						}
					}
				}
			}
		}
	}

	public function uploads($inputname,$path,$max=15){

		 $this->removeFile($inputname);

		$image=array();

		if(isset($_FILES[$inputname])){
			$count=0;
			for($i=0;$i<count($_FILES[$inputname]['name']);$i++){
				if($_FILES[$inputname]['error'][$i]==0){
					if($count<$max){
						$name=$this->getFileName();

						$arr=explode(".",$_FILES[$inputname]['name'][$i]);

						$filename=$this->createFileName($name,$name,1,$path,$arr[count($arr)-1]);

						if(move_uploaded_file($_FILES[$inputname]['tmp_name'][$i],$path.$filename)){
							$image[]=$filename;
							$count++;
						}
					}else{
						break;
					}
				}
			}
		}
		return $image;
	}

	public function LayDanhSachLSP($start,$count=10,$likecl=null,$likev=null){

		return $this->from('loai_san_pham')->where('LSP_CHA=0')->like($likecl,$likev)->orderBy('SAP_XEP_TC')->limit($start,$count)->toArray();
	}

	public function LayDanhSachLSPCon(){
	
		return $this->from('loai_san_pham')->where('LSP_CHA<>0')->toArray();
	}

	public function EditXmlFile($tag,$value){
		$dom=new DOMDocument();
        $dom->load('website.xml');
        $root=$dom->documentElement;
        if(gettype($tag)=="string"){
	        foreach ($root->getElementsByTagName($tag) as $item) {
	        	$item->nodeValue=$value;
	        }
    	}else{
	        for($i=0;$i<count($tag);$i++){
	        	foreach ($root->getElementsByTagName($tag[$i]) as $item) {
		            $item->nodeValue=$value[$i];
		        }
	        }
    	}
        $dom->save('website.xml');
	}

	public function EditMeutop($id,$arr){
		$dom=new DOMDocument();
        $dom->load('website.xml');
        $root=$dom->documentElement;
       	foreach ($root->getElementsByTagName('item') as $item) {
       		if($item->getAttribute('id')==$id){
       			foreach ($item->childNodes as $itemchild) {
       				if($itemchild->nodeType!=XML_TEXT_NODE)
       				{
       					if($itemchild->nodeName=="text"){
       						$itemchild->nodeValue=$arr[0];
       					}else{
       						if($itemchild->nodeName=="desc"){
       							$itemchild->nodeValue=$arr[1];
       						}else{
       							if($itemchild->nodeName=="keys"){
	       							$itemchild->nodeValue=$arr[2];
	       						}else{
	       							if($itemchild->nodeName=="t"){
		       							$itemchild->nodeValue=$arr[3];
		       						}
	       						}
       						}
       					}
       				}
       			}
       		}
       	}
       	 $dom->save('website.xml');
	}


	public function KhongDau($str){
		$str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
		$str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
		$str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
		$str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
		$str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
		$str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
		$str = preg_replace("/(đ)/", 'd', $str);

		return $str;
	}

	public function formatToUrl($name){

		$name=$this->KhongDau(trim(mb_strtolower($name,'UTF-8')));

		if (preg_match_all("/[a-za-z0-9 ]*/", $name, $matches)) {
			$str="";
			foreach($matches[0] as $value)
	        {
	        	$str.=$value;
	        }

			$str=str_replace(" ", "-", $str);
			$str=str_replace("--", "-", $str);
			$str=str_replace("--", "-", $str);
			return $str;		  
		}



		return $name;

	}

	 private function convert($type,$v){
        switch ($type) {
            case 'string':
                return $v;
            case 'br':
                return str_replace("\n", "<br>", $v);
            case 'int':
                return (int)$v;
            case 'boolean':
                return (boolean)$v;
            case 'md5':
                return md5($v);
            case 'time':
                return "getdate()";
            case 'price':
            	return preg_replace("/(\.|-| |\,)*/", "", $v);
            case 'decode':
            	return htmlentities($v,ENT_QUOTES,'UTF-8',false);
        }
    }

    public function post_r(array $remove,$id=null,$key=null){
    	$arr=$this->post($id,$key);

    	foreach ($remove as $item) {
    		unset($arr[$item]);
    	}
    	return $arr;
    }

	public function post($id=null,$key=null){
        $arr=array();
        if($id!=null)
            $arr[$id]=null;
        if($key==null){
            foreach($_POST as $name=>$value){
                $strarr=explode('-', $name);
                if(isset($strarr[1])){
                    $vvv=$this->convert($strarr[1],$value);
                    $arr[$strarr[0]]=$vvv;
                }else{
                    $arr[$name]=$value;
                }
            }
        }else{
            $key=$key."-";
            foreach($_POST as $name=>$value){
                if($this->startWith($name,$key)){
                    $strarr=explode('-', $name);
                    if(isset($strarr[2])){
                        $vvv=$this->convert($strarr[2],$value);
                        $arr[$strarr[1]]=$vvv;
                    }else{
                        $arr[$strarr[1]]=$value;
                    }
                }
            }
        }
        return $arr;
    }

    public function isDate($day){
    	$arrday=explode("/", $day);
    	$dat=array();


    	switch (count($arrday)) {
    		case 1:
    			if((int)$arrday[0]!=0)
    				$dat["year"]=$arrday[0];
    			break;
    		case 2:
    			if((int)$arrday[0]!=0 && (int)$arrday[1]!=0){
    				$dat["month"]=$arrday[0];
    				$dat["year"]=$arrday[1];
    			}
    			break;
    		case 3:
    			if((int)$arrday[0]!=0 && (int)$arrday[1]!=0 && (int)$arrday[2]!=0){
    				$dat["day"]=$arrday[0];
    				$dat["month"]=$arrday[1];
    				$dat["year"]=$arrday[2];
    			}
    			break;
    		default:
    			# code...
    			break;
    	}

    	if(count($dat)>0){
    		return $dat;
    	}
    	return false;
    }

    public function isPhoneNumber($number){
    	return preg_match("/^0([0-9]{2,3})(\.|-| )?([0-9]{3,4})(\.|-| )?([0-9]{3,4})$/", $number);
    }


}