<?php

class UserController extends Zend_Controller_Action
{
	private $s;
    public function init()
    {
		$this->s=new Zend_Session_Namespace();
        if(!isset($this->s->login)){
             $this->_redirect("/");
        }
    }

    public function indexAction()
    {
		$this->view->headTitle("Thông tin cá nhân");
        $model=new Model_User;
        if($this->_request->isPost()){
            switch ($this->_request->getPost("action")) {
                case '1':
                    $pass=$model->getRow(array('MATKHAU'),'MAKH='.$this->s->login["ID"])["MATKHAU"];
                    if($pass!=$this->_request->getPost("passwordcu")){
                        $this->view->message="Mật khẩu cũ sai";
                    }else{
                        if($model->DoiMatKhau($this->s->login["ID"],$this->_request->getPost("passwordmoi"))==0){
                             $this->view->message="Có lỗi. Đổi mật khẩu thất bại";
                        }else{
                            $this->view->message="Đổi mật khẩu thành công";
                        }
                    }
                    break;
                case '2':
                    $arr=array(
                        'TENKH'=>$this->_request->getPost("name"),
                        'NGAYSINH'=>$this->_request->getPost("namsinh")."/".$this->_request->getPost("thangsinh")."/".$this->_request->getPost("ngaysinh"),
                        'CMND'=>$this->_request->getPost("cmnd"),
                        'DIENTHOAI'=>$this->_request->getPost("phone"),
                        'DIACHI'=>$this->_request->getPost("address"),
                        'TENNN'=>$this->_request->getPost("s_name"),
                        'DIENTHOAINN'=>$this->_request->getPost("s_phone"),
                        'DIACHINN'=>$this->_request->getPost("s_address")
                    );
                    if($model->DoiThongTin($this->s->login["ID"],$arr)==0){
                             $this->view->message2="Có lỗi. Cập nhật thông tin thất bại";
                        }else{
                            $this->view->message2="Cập nhật thông tin thành công";
                        }
                    break;
            
            }
        }
        
        $this->view->username=$this->s->login["TEN"];
        $this->view->headLink()->appendStylesheet($this->view->baseUrl()."/public/Contents/styledangky.css","all");
        $this->view->info=$model->getRow(array('TENKH','NGAYSINH','CMND','DIENTHOAI','DIACHI','TENNN','DIENTHOAINN','DIACHINN'),'MAKH='.$this->s->login["ID"]);
    }

    public function orderAction()
    {
        $model=new Model_Admin;
        $this->view->headLink()->appendStylesheet($this->view->baseUrl()."/public/Contents/stylegiohang.css","all");
        $this->view->hoadon=$model->selectAll('don_hang','MAKH='.$this->s->login["ID"],'MADH,NGAYLAP,GHICHU,SOTIENNO,HOANTHANH,(select sum(SOLUONG) from ctdh where ctdh.MADH=don_hang.MADH) as sl,(select sum(GIA*SOLUONG) from ctdh where ctdh.MADH=don_hang.MADH) as sum','MADH DESC');
        $this->view->diemtl=$model->getRow('khach_hang','MAKH='.$this->s->login["ID"],'DIEM')["DIEM"];
        $where="";
        foreach ($this->view->hoadon as $item) {
            $where.=$item["MADH"].",";
        }
        $where=mb_substr($where, 0,strlen($where)-1);
        $this->view->cthd=$model->selectAll('ctdh','MADH in ('.$where.')','MADH,TENSP,HINHANH,ctdh.GIA,ctdh.SOLUONG,san_pham.URL,san_pham.MASP',null,null,null,'san_pham','ctdh.MASP=san_pham.MASP');
        $this->view->headTitle("Đơn hàng của bạn");
    }

}