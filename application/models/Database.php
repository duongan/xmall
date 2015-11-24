<?php
class Model_Database{

	protected $conn=null;
	protected $table=null;

	private $_table=null;
	private $_columns=null;
	private $_from=null;
	private $_where="";
	private $_order=null;
	private $_limit=null;
	private $_join="";
	private $_groupBy=null;
	private $_having=null;
	private $_query=null;
	
	public function __construct(){
		if($this->conn==null){

			$db=Zend_Registry::get('db');

			$this->conn=mysql_connect($db['resources']['db']['params']['host'],$db['resources']['db']['params']['username'],$db['resources']['db']['params']['password']);
			mysql_select_db($db['resources']['db']['params']['dbname'],$this->conn);
			mysql_query("set names '".$db['resources']['db']['params']['charset']."'");
		}
		
	}

	protected function select($columns=null){
		if($columns!=null){
			$this->_columns=$columns;
		}else{
			$this->_columns="*";
		}
		return $this;
	}

	protected function from($from=null){
		if($from!=null){
			$this->_table=$from;
		}
		return $this;
	}

	protected function where($where=null,$r=null){
		if($where!=null){
			if($this->_where==""){
				$this->_where=$where;
			}else{
				if(isset($r)){
					$this->_where.=" ".$r." ".$where;
				}else{
					$this->_where.=" and ".$where;
				}
			}
		}
		return $this;
	}

	protected function orderBy($order=null){
		if($order!=null){
			$this->_order=$order;
		}
		return $this;
	}

	protected function limit($start=null,$count=null){
		
		if($start==null && $count==null)
			return $this;

		if($start!=null){
			$this->_limit=((string)$start).','.((string)$count);	
		}else{
			$this->_limit='0,'.(string)$count;
		}
		return $this;
	}

	protected function join($tb=null,$on=null){
		
		if($tb==null || $on==null)
			return $this;

		$this->_join.=' inner join '.$tb.' on '.$on;
		
		return $this;
	}

	protected function leftJoin($tb=null,$on=null){
		
		if($tb==null || $on==null)
			return $this;

		$this->_join.=' left outer join '.$tb.' on '.$on;
		
		return $this;
	}

	protected function rightJoin($tb=null,$on=null){
		
		if($tb==null || $on==null)
			return $this;

		$this->_join.=' right outer join '.$tb.' on '.$on;
		
		return $this;
	}


	protected function groupBy($g=null){
		if($g!=null){
			$this->_groupBy=$g;
		}
		return $this;
	}

	protected function having($h=null){
		if($h!=null){
			$this->_having=$h;
		}
		return $this;
	}

	protected function like($cl,$vl,$l=null){
		if(!isset($cl) || !isset($vl))
			return $this;
		
		if($this->_where==""){
			$this->_where=$cl." like '%".$vl."%'";
		}else{
			if(isset($l)){
				$this->_where.=" ".$l." ".$cl." like '%".$vl."%'";
			}else{
				$this->_where.=" and ".$cl." like '%".$vl."%'";
			}
		}
		return $this;
	}

	protected function All($where=null,$count=null){
		
		if($where!=null){
			$this->where($where);
		}

		if($count!=null){
			$this->limit(null,$count);
		}

		return $this->toArray();
	}

	private function CreateQuery($v=null,$v2=null){
		if($this->_query==null){
			if($this->_columns==null)
				$this->_columns="*";
			$tb=$this->table;
			if($this->_table!=null)
				$tb=$this->_table;
			
			if($v==null)
				$this->_query="select ".$this->_columns;
			else
				$this->_query="select ".$v."(".$v2.") as c";

			$this->_query.=" from ".$tb;

			$this->_columns=null;
			$this->_table=null;

			if($this->_join!=""){
				$this->_query.=$this->_join;
				$this->_join="";
			}

			if($this->_where!=""){
				$this->_query.=" where ".$this->_where;
				$this->_where="";
			}

			if($this->_groupBy!=null){
				$this->_query.=" group by ".$this->_groupBy;
				$this->_groupBy=null;
			}

			if($this->_having!=null){
				$this->_query.=" having ".$this->_having;
				$this->_having=null;
			}

			if($this->_order!=null){
				$this->_query.=" order by ".$this->_order;
				$this->_order=null;
			}

			if($this->_limit!=null){
				$this->_query.=" limit ".$this->_limit;
				$this->_limit=null;
			}
		}
	}

	protected function getQuery(){
		return $this->_query;
	}

	protected function query($q){
		$this->_query=$q;

		return $this;
	}


	protected function toArray(){
		
		$this->CreateQuery();

		$result=mysql_query($this->_query);

		$this->_query=null;

		$arr=array();

		while($row=mysql_fetch_assoc($result)){
			$arr[] = $row;
		}

		return $arr;
	}

	private function f(){
		$result=mysql_query($this->_query);
		
		$this->_query=null;

		$count=0;

		$count=mysql_fetch_assoc($result);

		return $count['c'];
	}

	protected function count($cl=null){
		
		if($cl==null)
			$cl="*";

		$this->CreateQuery('count',$cl);

		return $this->f();
	}


	protected function sum($cl=null){
		
		if($cl==null)
			return 0;

		$this->CreateQuery('sum',$cl);

		return $this->f();
	}

	protected function avg($cl=null){
		
		if($cl==null)
			return 0;

		$this->CreateQuery('avg',$cl);

		return $this->f();
	}

	protected function max($cl=null){
		
		if($cl==null)
			return 0;

		$this->CreateQuery('max',$cl);

		return $this->f();
	}

	protected function min($cl=null){
		
		if($cl==null)
			return 0;

		$this->CreateQuery('min',$cl);

		return $this->f();
	}


	protected function delete($where=null,$tb=null){
		if($where==null && $this->_where==null)
			return -1;
		
		if($this->table==null && $tb==null && $this->_table==null)
			return -1;

		try {

			$this->_query="delete from ";
			if($tb!=null){
				$this->_query.=$tb;
			}else{
				if($this->_table!=null){
					$this->_query.=$this->_table;
					$this->_table=null;
				}
				else
					$this->_query.=$this->table;
			}

			if($where!=null)
				$this->_query.=" where ".$where;
			else
				$this->_query.=" where ".$this->_where;

			mysql_query($this->_query);
			$this->_query=null;
			return mysql_affected_rows();
		} catch (Exception $e) {
			return -1;
		}
	}

	protected function update(array $arr,$where=null,$tb=null){
		
		if($this->table==null && $tb==null && $this->_table==null)
			return -1;

		if(count($arr)==0)
			return -1;

		$this->_query="update ";
		
		if($tb!=null){
			$this->_query.=$tb;
		}else{
			if($this->_table!=null){
				$this->_query.=$this->_table;
				$this->_table=null;
			}
			else
				$this->_query.=$this->table;
		}

		$this->_query.=" set ";

		foreach ($arr as $key => $value) {


			switch (gettype($value)) {
					case 'string':
						$this->_query.=$key."='".$value."',";
						break;
					case 'boolean':
						$this->_query.=$key."=b'".$value."',";
						break;
					case 'integer':
						$this->_query.=$key."=".$value.",";
						break;
					case 'array':
							$this->_query.=$key."=".$value[0].",";
							break;
			}
		}

		$this->_query=substr($this->_query, 0,strlen($this->_query)-1);

		if($where!=null){
			$this->_query.=" where ".$where;
		}else{
			if($this->_where!=null){
				$this->_query.=" where ".$this->_where;
				$this->_where=null;
			}
		}



		mysql_query($this->_query);
		$this->_query=null;
		return mysql_affected_rows();

	}

	protected function _update(){
		mysql_query($this->_query);
		$this->_query=null;
		return mysql_affected_rows();
	}

	protected function _excute(){
		$result=mysql_query($this->_query);
		$this->_query=null;
		$arr=array();

		while($row=mysql_fetch_assoc($result)){
			$arr[] = $row;
		}

		return $arr;
	}

	protected function insert(array $arr,$tb=null){
		
		if($this->table==null && $tb==null && $this->_table==null)
			return -1;

		if(count($arr)==0)
			return -1;

		$this->_query="insert into ";
		
		if($tb!=null){
			$this->_query.=$tb;
		}else{
			if($this->_table!=null){
				$this->_query.=$this->_table;
				$this->_table=null;
			}
			else
				$this->_query.=$this->table;
		}

		$this->_query.=" values(";

		foreach ($arr as $key => $value) {
			if(!isset($value))
				$this->_query.="NULL,";
			else{
				switch (gettype($value)) {
						case 'string':
							$this->_query.="'".$value."',";
							break;
						case 'boolean':
							$this->_query.="b'".$value."',";
							break;
						case 'integer':
							$this->_query.=$value.",";
							break;
						case 'array':
							$this->_query.="now(),";
							break;
					}
			}
		}

		$this->_query=substr($this->_query, 0,strlen($this->_query)-1);
		$this->_query.=")";




		mysql_query($this->_query);
		$this->_query=null;
		return mysql_insert_id();

	}
}