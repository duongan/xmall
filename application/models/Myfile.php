<?php
class Model_Myfile{
	
	public function writeFile($path,$content){
		file_put_contents($path,"\xEF\xBB\xBF".$content);
	}

	public function readFile($path){
		return file_get_contents($path);
	}
	
}