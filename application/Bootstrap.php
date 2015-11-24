<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	protected function _initDatabase()
	{ 
		Zend_Registry::set('db',$this->getOptions());
	}


	protected function _initDefaultModuleAutoloader()
	{ 
		$resourceLoader = new Zend_Application_Module_Autoloader(array(
			'namespace' => '',
			'basePath'  => APPLICATION_PATH,
		));

		return $resourceLoader;
	}

	protected function _initRegistry(){
		$this->bootstrap('db');
		$db=$this->getResource('db');
		$model=new Model_Global;

		$this->bootstrap('layout');
		$layout=$this->getResource('layout');
		$layout->nav=$model->getDataMenu();

		$xml=new Zend_Config_Xml('website.xml');
		$layout->hotline=$xml->info->hotline;
		$layout->bt=$xml->info->bt;
		$arr=array();
		foreach ($xml->info->menutop->item as $item) {
			$arr[]=array('text'=>$item->text,'path'=>$item->path);
		}
		$layout->menutop=$arr;

		$s=new Zend_Session_Namespace();

		if($s->login==null){
			if(isset($_COOKIE['login'])){
				$result=$model->getInfo($_COOKIE['login']);
				if($result!=null)
					$s->login=$result;
			}
		}

		$layout->login=$s->login;
		if(isset($s->cart))
			$layout->qcart=count($s->cart);
		else
			$layout->qcart=0;

		

		$layout->sphot=$model->getSpHot();

		if(!isset($s->online)){
			$fil = fopen("online.txt", "r"); 
			$count = fread($fil, 8); 
			fclose($fil); 
			$count++;
			$fil = fopen("online.txt", "w"); 
			fwrite($fil, $count); 
			fclose($fil); 
			$s->online=true;
		}
	}
}

