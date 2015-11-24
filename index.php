<?php
// Define path to application directory
defined('APPLICATION_PATH')
||  define('APPLICATION_PATH', 
realpath(dirname(__FILE__) . '/application'));

defined('UPLOAD_PATH')
||  define('UPLOAD_PATH', 
realpath(dirname(__FILE__) . '/uploads'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
realpath(APPLICATION_PATH . '/../library'),
get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

/** Routing Info **/
$FrontController = Zend_Controller_Front::getInstance();
$Router = $FrontController->getRouter();

$Router->addRoute("loaisp",
	new Zend_Controller_Router_Route_Regex("([a-zA-Z0-9-]*)\.html",array 
			(
				"controller" => "loai",
				"action" => "index"
			),
			array(1=>'name')
	)
);
$Router->addRoute("tintuc",
	new Zend_Controller_Router_Route("tin-tuc.html",array 
			(
				"controller" => "tin-tuc",
				"action" => "index"
			)
	)
);
$Router->addRoute("baohanh",
	new Zend_Controller_Router_Route("bao-hanh.html",array 
			(
				"controller" => "bao-hanh",
				"action" => "index"
			)
	)
);

$Router->addRoute("dangky",
	new Zend_Controller_Router_Route("dang-ky.html",array 
			(
				"controller" => "dang-ky",
				"action" => "index"
			)
	)
);

$Router->addRoute("dangnhap",
	new Zend_Controller_Router_Route("dang-nhap.html",array 
			(
				"controller" => "dang-nhap",
				"action" => "index"
			)
	)
);

$Router->addRoute("quyenmk",
	new Zend_Controller_Router_Route("quyen-mat-khau.html",array 
			(
				"controller" => "dang-nhap",
				"action" => "quyenmk"
			)
	)
);

$Router->addRoute("gioithieu",
	new Zend_Controller_Router_Route("gioi-thieu.html",array 
			(
				"controller" => "gioi-thieu",
				"action" => "index"
			)
	)
);

$Router->addRoute("hotro",
	new Zend_Controller_Router_Route("ho-tro.html",array 
			(
				"controller" => "ho-tro",
				"action" => "index"
			)
	)
);

$Router->addRoute("huongdan",
	new Zend_Controller_Router_Route("huong-dan.html",array 
			(
				"controller" => "huong-dan",
				"action" => "index"
			)
	)
);


$Router->addRoute("lienhe",
	new Zend_Controller_Router_Route("lien-he.html",array 
			(
				"controller" => "lien-he",
				"action" => "index"
			)
	)
);

$Router->addRoute("tuyendung",
	new Zend_Controller_Router_Route("tuyen-dung.html",array 
			(
				"controller" => "tuyen-dung",
				"action" => "index"
			)
	)
);

$Router->addRoute("dangxuat",
	new Zend_Controller_Router_Route("dang-xuat.html",array 
			(
				"controller" => "dang-xuat",
				"action" => "index"
			)
	)
);

$Router->addRoute("thongtincanhan",
	new Zend_Controller_Router_Route("thong-tin-ca-nhan.html",array 
			(
				"controller" => "user",
				"action" => "index"
			)
	)
);

$Router->addRoute("hoadoncuaban",
	new Zend_Controller_Router_Route("don-hang-cua-ban.html",array 
			(
				"controller" => "user",
				"action" => "order"
			)
	)
);

$Router->addRoute("giaohang",
	new Zend_Controller_Router_Route("giao-hang-toan-quoc.html",array 
			(
				"controller" => "giao-hang",
				"action" => "index"
			)
	)
);

$Router->addRoute("thanhtoan",
	new Zend_Controller_Router_Route("thanh-toan.html",array 
			(
				"controller" => "thanh-toan",
				"action" => "index"
			)
	)
);

$Router->addRoute("doctintuc",
	new Zend_Controller_Router_Route_Regex("([0-9]*)_([a-zA-Z0-9-]*)\.html",array 
			(
				"controller" => "tin-tuc",
				"action" => "detail"
			),
			array(1=>'id',2=>'name')
	)
);


$Router->addRoute("chitiet",
	new Zend_Controller_Router_Route_Regex("([a-zA-Z0-9-]*)_([0-9]*)\.html",array 
			(
				"controller" => "chi-tiet",
				"action" => "index"
			),
			array(1=>'name',2=>'id')
	)
);

$application->bootstrap()
            ->run();