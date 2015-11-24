<?php

class AdminController extends Zend_Controller_Action
{
	private $model;
    public function init()
    {	
        $session=new Zend_Session_Namespace();
        if(!isset($session->loginadmin)){
            $this->_redirect("/login");
        }
		 $option=array( 
			"layout" => "layout", 
			"layoutPath" => APPLICATION_PATH."/views/layouts/admin"
		); 
		Zend_Layout::startMvc($option);

		$this->model=new Model_Admin; 


         $this->view->user=$session->loginadmin["HOTEN"];
         $this->view->iduser=$session->loginadmin["ID"];
         $manhom=$this->model->getRow("nhan_vien",'MANV='.$session->loginadmin["ID"],'MANHOM')["MANHOM"];
         $this->view->quyen=$this->model->getRow('nhom','MANHOM='.$manhom);
    }

    public function indexAction()
    {
		$this->view->headTitle("Admin Control Panel");
        $session=new Zend_Session_Namespace();
        $matkhau=$this->model->getRow("nhan_vien",'MANV='.$session->loginadmin["ID"],'MATKHAU,MATKHAU2');
        if($matkhau['MATKHAU']==md5('123456')){
            $this->view->updatepass=true;
        }

        if($matkhau['MATKHAU2']==md5('123456')){
            $this->view->updatepass2=true;
        }

        if($this->_request->isPost()){
            $this->model->Sua(array("VI_PHAM"=>0),"nhan_xet",'MANX='.$this->_request->getPost("id"));
            $this->_redirect("/admin");
        }

        $this->view->spganhet=$this->model->selectAll('san_pham','SOLUONG-SOLUONGBANDUOC<10','MASP,TENSP,URL,HINHANH,GIA,(SOLUONG-SOLUONGBANDUOC) SLCON,(SELECT SUM(ctdh.SOLUONG) FROM ctdh GROUP BY ctdh.MASP having ctdh.MASP=san_pham.MASP) SOHD',"SOLUONGBANDUOC DESC");
        $this->view->spxemnhieu=$this->model->selectAll('san_pham',null,"MASP,TENSP,URL","XEM DESC",0,10);
        $this->view->count=$this->model->getRow('san_pham',null,'(select count(MASP) from san_pham) as SLSP,(select count(MADH) from don_hang) as SLHD,(select count(MANX) from nhan_xet) as SLNX, (select count(MAKH) from khach_hang) as SLKH,(select count(MATT) from tin_tuc) as SLTT,(select count(MALSP) from loai_san_pham where LSP_CHA=0) as SLLSP,(select count(MANHOM) from nhom) as SLN,(select count(MANV) from nhan_vien) as SLNV');
        $this->view->nxchuadoc=$this->model->dem('nhan_xet','DA_XEM=0','MANX');
        $this->view->hoadoncx=$this->model->dem('don_hang','DAXEM=0','MADH');
        $this->view->hoadoncht=$this->model->dem('don_hang','HOANTHANH=0','MADH');
        $this->view->thongke=$this->model->thongke();
        $this->view->thongke2=$this->model->thongke2();
        $this->view->tienbanduoc=$this->model->Tong('ctdh','SOLUONG*GIA','don_hang.HOANTHANH=1','don_hang','ctdh.MADH=don_hang.MADH');
        $this->view->slbanduoc=$this->model->Tong('ctdh','SOLUONG','don_hang.HOANTHANH=1','don_hang','ctdh.MADH=don_hang.MADH');
        $this->view->active=$this->model->getWebsiteActive();
        $this->view->nxvp=$this->model->selectAll('nhan_xet','nhan_xet.HIEN_THI=1 AND VI_PHAM>0','MANX,EMAIL,HOTEN,NGAYDANG,NOIDUNG,VI_PHAM,san_pham.TENSP,san_pham.MASP,san_pham.URL','VI_PHAM DESC',null,null,'san_pham','nhan_xet.MASP=san_pham.MASP');
        $this->view->sinhnhatkh=$this->model->selectAll('khach_hang','DAY(NGAYSINH)=DAY(now()) AND MONTH(NGAYSINH)=MONTH(now())','TENKH,NGAYSINH,EMAIL,DIENTHOAI,DIACHI');
    }
    /**
    * Chức năng: Hiển Thị Danh Sách Sản Phẩm
    */
    public function sanphamAction()
    {
        if($this->view->quyen["XEMDSSP"]==0){
            $this->_redirect("admin/error");
        }
        $this->view->headTitle("Danh sách sản phẩm - Admin Control Panel");

        $start=0;
        $count=10;

        if($this->_request->getParam('page')!=null){
            $start=$this->_request->getParam('page');
        }

        $query="";

        $sortby='MASP DESC';
        if($this->_request->getParam('sort')!=null){
            if(substr($this->_request->getParam('sort'), 0,2)=="f-"){
                switch ($this->_request->getParam('sort')) {
                    case 'f-chuaban':
                        $query.="SOLUONGBANDUOC=0";
                        break;
                    case 'f-sapramat':
                        $query.="TEN_TRANGTHAI=2";
                        break;
                    case 'f-thongso':
                        $query.="THONG_SO='' OR THONG_SO='&lt;br&gt;'";
                        break;
                    case 'f-khuyenmai':
                        $query.="KHUYENMAI='&lt;br&gt;'";
                        break;
                    case 'f-baiviet':
                        $query.="BAIVIET='&lt;br&gt;'";
                        break;
                    case 'f-hide':
                        $query.="HIEN_THI=0";
                        break;
                    case 'f-video':
                        $query.="VIDEO=''";
                        break;
                    case 'f-hh':
                        $query.="SOLUONG-SOLUONGBANDUOC<10";
                        break;
                    default:
                        # code...
                        break;
                }
            }else
                $sortby=str_replace("-", " ", $this->_request->getParam('sort'));
        }

        if($this->_request->getParam('q')==null){
            if($query!=""){
                $query=" AND ".$query;
            }
            $this->view->data=$this->model->selectAll('san_pham','DA_XOA=0'.$query,'MASP,TENSP,NGAYCAPNHAT,san_pham.URL,GIA,GIAM_GIA,HINHANH,XEM,SOLUONG,SOLUONGBANDUOC,san_pham.HIEN_THI,san_pham.HIEN_THI_TC,TEN_LOAI,HOTEN',$sortby,$start*$count,$count,'loai_san_pham','san_pham.LOAISP=loai_san_pham.MALSP','nhan_vien','san_pham.NGUOIGUI=nhan_vien.MANV');
            $sum=$this->model->dem('san_pham','DA_XOA=0'.$query,'MASP');
        }else{
            

            $arrcut=explode("-", $this->_request->getParam('q'));
            $d=$this->model->isDate($arrcut[0]);
            if(isset($arrcut[1])){
                $d1=$this->model->isDate($arrcut[1]);
            }

            if((gettype($d)=="array" && count($d)>1) || (isset($d1) && count($d1)>0)){
                if(!isset($d1)){
                    if(isset($d["day"])){
                        $query.="day(NGAYGUI)=".$d["day"];
                    }
                    if(isset($d["month"])){
                        if(isset($d["day"]))
                            $query.=" and ";
                        $query.="month(NGAYGUI)=".$d["month"];
                        $query.=" and year(NGAYGUI)=".$d["year"];
                    }
                }else{
                    $dbegin="";
                    if(isset($d["year"])){
                        $dbegin.=$d["year"];
                    }
                    if(isset($d["month"])){
                        if(isset($d["year"]))
                            $dbegin.="/";
                        $dbegin.=$d["month"];
                    }else{
                        $dbegin.="/1";
                    }
                    if(isset($d["day"])){
                        if(isset($d["year"]) || isset($d["month"]))
                            $dbegin.="/";
                        $dbegin.=$d["day"];
                    }else{
                        $dbegin.="/1";
                    }
                    
                    

                    $dend="";
                    if(isset($d["year"])){
                        $dend.=$d1["year"];
                    }
                    if(isset($d1["month"])){
                        if(isset($d1["year"]))
                            $dend.="/";
                        $dend.=$d1["month"];
                    }
                    else{
                        $dend.="/1";
                    }
                    if(isset($d1["day"])){
                         if(isset($d1["year"]) || isset($d1["month"]))
                            $dend.="/";
                        $dend.=$d1["day"];
                    }else{
                        $dend.="/1";
                    }
                    
                    $query.="NGAYGUI between '".$dbegin."' and '".$dend."'";
                }
            }else{
                if($query!="")
                    $query.=" AND ";
                if((int)$this->_request->getParam('q')==0)
                    $query.='TENSP like "%'.$this->_request->getParam('q').'%" OR loai_san_pham.TEN_LOAI like "'.$this->_request->getParam('q').'" OR nhan_vien.HOTEN like "%'.$this->_request->getParam('q').'%"';
                else
                    $query.='MASP='.$this->_request->getParam('q');
            }
            $this->view->data=$this->model->selectAll('san_pham','DA_XOA=0 AND '.$query,'MASP,TENSP,NGAYCAPNHAT,san_pham.URL,GIA,GIAM_GIA,HINHANH,XEM,SOLUONG,SOLUONGBANDUOC,san_pham.HIEN_THI,san_pham.HIEN_THI_TC,TEN_LOAI,HOTEN',$sortby,$start*$count,$count,'loai_san_pham','san_pham.LOAISP=loai_san_pham.MALSP','nhan_vien','san_pham.NGUOIGUI=nhan_vien.MANV');
            $sum=$this->model->dem2('san_pham','DA_XOA=0 AND '.$query,'MASP','nhan_vien','san_pham.NGUOIGUI=nhan_vien.MANV');
        }
        $this->view->arr_page=array(
            'sum'=>$sum,
            'current'=>$start,
            'count'=>$count,
        );
    }

     /**
    * Chức năng: Thung rác các sản phẩm đã xóa
    */
    public function thungracAction()
    {
        if($this->view->quyen["SUASP"]==0){
            $this->_redirect("admin/error");
        }
        if($this->_request->getParam('id')!=null){
            $this->model->Sua(array('DA_XOA'=>false),'san_pham','MASP='.$this->_request->getParam('id'));
            $this->_redirect("admin/thungrac");
        }

        if($this->_request->isPost()){
            if($this->model->Xoa('san_pham','MASP='.$this->_request->getPost('idx'))!=-1){
                if($this->_request->getPost("hinhanh")!="")
                    unlink(UPLOAD_PATH."/images/sp/".$this->_request->getPost("hinhanh"));
                if($this->_request->getPost("hinhanhkhac")!=""){
                    $arr=explode(" ",$this->_request->getPost("hinhanhkhac"));

                    foreach ($arr as $item) {
                        unlink(UPLOAD_PATH."/images/sp/".$item);
                    }
                }
                $this->model->insertLichSu('Xóa sản phẩm '.$this->_request->getPost('namex'),$this->view->iduser);
                $this->view->message="Xóa thành công.";
            }else{
                $this->view->message="Xóa thất bại. Có thể sản phẩm có ràng buộc. Như có bình luận hoặc đã lập hóa đơn";
            }
        }

        $this->view->headTitle("Thùng rác - Admin Control Panel");

        $start=0;
        $count=10;

        if($this->_request->getParam('page')!=null){
            $start=$this->_request->getParam('page');
        }

        if($this->_request->getParam('q')==null){
            $this->view->data=$this->model->selectAll('san_pham','DA_XOA=1','MASP,TENSP,GIA,GIAM_GIA,HINHANH,HINH_ANH_KHAC,XEM,SOLUONG,SOLUONGBANDUOC,TEN_LOAI,HOTEN','MASP DESC',$start*$count,$count,'loai_san_pham','san_pham.LOAISP=loai_san_pham.MALSP','nhan_vien','san_pham.NGUOIGUI=nhan_vien.MANV');
            $sum=$this->model->dem('san_pham','DA_XOA=1','MASP');
        }else{
            $this->view->data=$this->model->selectAll('san_pham','DA_XOA=1 AND TENSP like "%'.$this->_request->getParam('q').'%"','MASP,TENSP,GIA,GIAM_GIA,HINHANH,HINH_ANH_KHAC,XEM,SOLUONG,SOLUONGBANDUOC,TEN_LOAI,HOTEN','MASP DESC',$start*$count,$count,'loai_san_pham','san_pham.LOAISP=loai_san_pham.MALSP','nhan_vien','san_pham.NGUOIGUI=nhan_vien.MANV');
            $sum=$this->model->dem('san_pham','DA_XOA=1 AND TENSP like "%'.$this->_request->getParam('q').'%"','MASP');
        }

        $this->view->arr_page=array(
            'sum'=>$sum,
            'current'=>$start,
            'count'=>$count,
        );
    }

    /**
    * Chức năng: Thêm sản phẩm mới
    */
    public function themspAction()
    {
        if($this->view->quyen["THEMSP"]==0){
            $this->_redirect("admin/error");
        }
        $this->view->headTitle("Thêm Sản Phẩm Mới - Admin Control Panel");

        if($this->_request->isPost()){
            $arrimgs=$this->model->uploads('hinhanhkhac',UPLOAD_PATH."/images/sp/");
            $image=$this->model->upload("hinhanh",UPLOAD_PATH."/images/sp/");

            $images="";

            foreach ($arrimgs as $item) {
                $images.=" ".$item;
            }
            $arr=array(
                'MASP'=>null,
                'LOAISP'=>$this->_request->getPost('loaisp'),
                'NGUOIGUI'=>$this->view->iduser,
                'NGAYGUI'=>array(),
                'NGAYCAPNHAT'=>array(),
                'TENSP'=>trim($this->_request->getPost('tensp')),
                'URL'=>$this->model->formatToUrl(trim($this->_request->getPost('tensp'))),
                'GIA'=>preg_replace("/(\.|-| |\,)*/", "", trim($this->_request->getPost('giasp'))),
                'GIAM_GIA'=>0,
                'HINHANH'=>$image,
                'HINH_ANH_KHAC'=>trim($images),
                'TOM_TAT_TS'=>str_replace("\n", "<br>", trim($this->_request->getPost('tomtat'))),
                'LINH_KIEN'=>trim($this->_request->getPost('linhkien')),
                'KHUYENMAI'=>htmlentities($this->_request->getPost('khuyenmai'),ENT_QUOTES,'UTF-8',false),
                'ICON_TRANGTHAI'=>$this->_request->getPost('trangthai'),
                'TEN_TRANGTHAI'=>$this->_request->getPost('tentrangthai'),
                'VIDEO'=>"",
                'THONG_SO'=>"",
                'BAIVIET'=>htmlentities($this->_request->getPost('baiviet'),ENT_QUOTES,'UTF-8',false),
                'BAOHANH'=>(int)$this->_request->getPost('baohanh'),
                'XEM'=>0,
                'TUKHOA'=>$this->_request->getPost('tukhoa'),
                'SOLUONG'=>$this->_request->getPost('soluong'),
                'SOLUONGBANDUOC'=>0,
                'HIEN_THI_TC'=>(boolean)$this->_request->getPost('hienthitrangchu'),
                'HIEN_THI'=>(boolean)$this->_request->getPost('hienthi'),
                'DA_XOA'=>false
            );
            $idsp=$this->model->Them($arr,'san_pham');
            if($idsp>0){
                $this->model->insertLichSu('Thêm sản phẩm '.$arr['TENSP'],$this->view->iduser);
                $this->_redirect("admin/capnhatsp?id=".$idsp);
            }else{
                $this->view->message="Thêm thất bại. Có thể sản phẩm đã tồn tại.";

                unlink(UPLOAD_PATH."/images/sp/".$arr["HINHANH"]);

                foreach ($arrimgs as $item) {
                    unlink(UPLOAD_PATH."/images/sp/".$item);
                }
            }
        }

        $this->view->lsp=$this->model->selectAll('loai_san_pham',null,'MALSP,TEN_LOAI,LSP_CHA','SAP_XEP_TC,LSP_CHA');
    }

    /**
    * Chức năng: Cập nhật sản phẩm
    */
    public function capnhatspAction(){
        if($this->view->quyen["SUASP"]==0){
            $this->_redirect("admin/error");
        }
        $this->view->headTitle("Cập nhật sản phẩm - Admin Control Panel");

        if($this->_request->getParam("id")==null){
             $this->_redirect("admin/sanpham");
        }

        if($this->_request->isPost()){
            switch ($this->_request->getPost('action')) {
                case '1': 
                case '2':
                case '4':
                case '5':
                case '6':
                case '7':
                    $arr=$this->model->post_r(array('id','action'));
                    if($this->model->Sua($arr,'san_pham','MASP='.$this->_request->getPost('id'))==0){
                        $this->view->message="Sửa thất bại";
                    }else{
                        $this->view->message="Sửa thành công";
                    }
                    break;
                case '3':
                    $image=$this->model->upload("hinhanh",UPLOAD_PATH."/images/sp/");
                    if($image!=""){
                        unlink(UPLOAD_PATH."/images/sp/".$this->_request->getPost('hinhanhcu'));
                    }else{
                        $image=$this->_request->getPost('hinhanhcu');
                    }

                    $arrxoa=explode(" ", trim($this->_request->getPost("arrxoa")));

                    foreach ($arrxoa as $item) {
                       unlink(UPLOAD_PATH."/images/sp/".$item);
                    }

                    $images="";

                    $arrimg=explode(" ", trim($this->_request->getPost("arrimage")));
                    $arrimgs=$this->model->uploads('hinhanhkhac',UPLOAD_PATH."/images/sp/",15-count($arrimg));

                    foreach ($arrimg as $item) {
                        $images.=" ".trim($item);
                    }
                    foreach ($arrimgs as $item) {
                        $images.=" ".trim($item);
                    }


                    $arr=array("HINHANH"=>$image,'HINH_ANH_KHAC'=>trim($images));

                    if($this->model->Sua($arr,'san_pham','MASP='.$this->_request->getPost('id'))==0){
                        $this->view->message="Sửa thất bại";
                    }else{
                        $this->view->message="Sửa thành công";
                    }

                    break;
            }
            $flag=true;
        }

        $id=$this->_request->getParam("id");

        $this->view->lsp=$this->model->selectAll('loai_san_pham',null,'MALSP,TEN_LOAI,LSP_CHA','SAP_XEP_TC,LSP_CHA');
        $this->view->thongtin=$this->model->getRow('san_pham','MASP='.$id,'TENSP,URL,LOAISP,GIA,TOM_TAT_TS,LINH_KIEN,TUKHOA,BAOHANH,SOLUONG');
        if(count($this->view->thongtin)==0)
           $this->_redirect("admin/sanpham");
        if(isset($flag)){
            $this->model->insertLichSu('Sửa sản phẩm '.$this->view->thongtin['TENSP'],$this->view->iduser);
        }
        $lspcha=$this->model->getRow("loai_san_pham",'MALSP='.$this->view->thongtin["LOAISP"],'LSP_CHA')["LSP_CHA"];
        if($lspcha!=0){
            $this->view->clsp=$this->model->getRow("loai_san_pham",'MALSP='.$lspcha,'VIDEO,THONG_SO');
        }else{
            $this->view->clsp=$this->model->getRow("loai_san_pham",'MALSP='.$this->view->thongtin["LOAISP"],'VIDEO,THONG_SO');
        }
        $row=$this->model->getRow('san_pham','MASP='.$id,'KHUYENMAI,GIAM_GIA,THONG_SO,BAIVIET,HINHANH,HINH_ANH_KHAC,VIDEO,ICON_TRANGTHAI,TEN_TRANGTHAI');
        if($this->view->clsp["THONG_SO"]==1)
        $this->view->thongso=$row["THONG_SO"];
        $this->view->baiviet=$row["BAIVIET"];
        $this->view->khuyenmai=array("km"=>$row["KHUYENMAI"],"gg"=>$row["GIAM_GIA"]);
        $this->view->trangthai=array("icon"=>$row["ICON_TRANGTHAI"],"trangthai"=>$row["TEN_TRANGTHAI"]);
        if($this->view->clsp["VIDEO"]==1)
        $this->view->video=$row["VIDEO"];
        $this->view->hinhanh=array("HINHANH"=>$row["HINHANH"],'HINH_ANH_KHAC'=>$row['HINH_ANH_KHAC']);
    }

    /**
    * Chức năng: hiển thị danh sách nhận xét
    */
    public function nhanxetAction(){
        if($this->view->quyen["XEMDSNX"]==0){
            $this->_redirect("admin/error");
        }
        $this->view->headTitle("Nhận xét - Admin Control Panel");

        if($this->_request->isPost()){
            if($this->_request->getPost("noidung")==null){
                $this->view->message="";
                $item=$this->_request->getPost("arr");
                for($idem=count($item)-1;$idem>=0;$idem--) {
                    if($this->model->dem('nhan_xet','NX_CHA='.$item[$idem],'MANX')>0)
                        $this->view->message.="Xóa thất bại nhận xét ".$item[$idem].". Vì nhận xét này đã có nhận xét con<br>";
                    else{
                        if($this->model->Xoa('nhan_xet','MANX='.$item[$idem])>0)
                            $this->view->message.="Xóa thành công nhận xét ".$item[$idem]."<br>";
                    }
                }
                

            }else{
                $this->model->Them(array(
                    "MANX"=>'NULL',
                    'MASP'=>$this->_request->getPost("idsp"),
                    'EMAIL'=>'vienthonga1203@gmail.com',
                    'HOTEN'=>$this->view->user,
                    'NGAYDANG'=>array(),
                    'NOIDUNG'=>$this->_request->getPost('noidung'),
                    'NX_CHA'=>$this->_request->getPost('idnxc'),
                    'DANH_GIA'=>5,
                    'THICH'=>0,
                    'VI_PHAM'=>0,
                    'HIEN_THI'=>true,
                    'ADMIN'=>true,
                    'DA_XEM'=>true
                ),'nhan_xet');
                $this->view->message="Trả lời thành công nhận xét ".$this->_request->getPost("idnxc");
                unset($_POST['noidung']);
            }
        }

        $start=0;
        $count=10;

        if($this->_request->getParam('page')!=null){
            $start=$this->_request->getParam('page');
        }

        $query="NX_CHA=0";
        $sort="MANX DESC";

        if($this->_request->getParam('q')!=null){
            $query="NX_CHA=0 AND (san_pham.TENSP like '%".$this->_request->getParam('q')."%' OR NOIDUNG like '%".$this->_request->getParam('q')."%' OR HOTEN like '%".$this->_request->getParam('q')."%')";
        }

        if($this->_request->getParam('sort')!=null){
            $sort=str_replace("-", " ", $this->_request->getParam('sort'));
        }

        if($this->_request->getParam('id')!=null){
            $query="MANX=".$this->_request->getParam('id');
        }

        // if($this->_request->getParam('new')!=null){
        //     $query="DA_XEM=0";
        // }

        $this->view->nx=$this->model->selectAll('nhan_xet',$query,'MANX,EMAIL,HOTEN,NGAYDANG,NOIDUNG,DANH_GIA,THICH,VI_PHAM,ADMIN,DA_XEM,nhan_xet.HIEN_THI,san_pham.MASP,san_pham.TENSP,san_pham.URL',$sort,$start*$count,$count,'san_pham','nhan_xet.MASP=san_pham.MASP');

        $in="";

        foreach ($this->view->nx as $item) {
            $in.=$item["MANX"].",";
        }

        $in="(".mb_substr($in, 0,strlen($in)-1).")";

        $this->view->subcomment=$this->model->selectAll('nhan_xet','NX_CHA<>0 AND NX_CHA in'.$in,'MANX,EMAIL,HOTEN,NGAYDANG,NOIDUNG,DANH_GIA,THICH,VI_PHAM,ADMIN,NX_CHA,HIEN_THI','MANX DESC');

        $this->view->arr_page=array(
            'sum'=>$this->model->dem('nhan_xet',$query,'MANX'),
            'current'=>$start,
            'count'=>$count,
        );
        $this->model->Sua(array("DA_XEM"=>true),'nhan_xet','DA_XEM=0');
    }

    /**
    * Chức năng: hiển thị danh sách loại sản phẩm
    */
    public function loaispAction(){
        if($this->view->quyen["XEMDSLSP"]==0){
            $this->_redirect("admin/error");
        }
    	$this->view->headTitle("Loại Sản Phẩm - Admin Control Panel");
    	$this->view->headLink()->appendStylesheet($this->view->baseUrl()."/public/admin/loaisp.css","all");

    	$start=0;
    	$count=3;

    	if($this->_request->getParam('page')!=null){
    		$start=$this->_request->getParam('page');
    	}


        if($this->_request->getParam("q")==null){
           $this->view->data=$this->model->LayDanhSachLSP($start*$count,$count);
           $sum=$this->model->dem('loai_san_pham','LSP_CHA=0','MALSP');
        }else{
            $this->view->data=$this->model->LayDanhSachLSP($start*$count,$count,"TEN_LOAI",$this->_request->getParam("q"));
             $sum=$this->model->dem('loai_san_pham','LSP_CHA=0 AND TEN_LOAI like "%'.$this->_request->getParam("q").'%"','MALSP');
        }

    	$this->view->arr_page=array(
    		'sum'=>$sum,
    		'current'=>$start,
    		'count'=>$count,
    	);

        
    	$this->view->datachild=$this->model->LayDanhSachLSPCon();
    	$this->view->max=$this->model->maxwhere('loai_san_pham','LSP_CHA=0','SAP_XEP_TC');
        $this->view->maxbanner=$this->model->maxwhere('loai_san_pham',null,'SAP_XEP_BANNER');
    }

    /**
    * Chức năng: Thêm lsp mới
    */
    public function themloaispAction()
    {
        if($this->view->quyen["THEMLSP"]==0){
            $this->_redirect("admin/error");
        }
		$this->view->headTitle("Thêm Loại Sản Phẩm - Admin Control Panel");

		if($this->_request->isPost()){

            $iconnho=$this->model->upload("iconnho",UPLOAD_PATH."/images/loaisp/");
            $iconlon=$this->model->upload("iconlon",UPLOAD_PATH."/images/loaisp/");

			$arr=array(
				'MALSP'=>null,
				'TEN_LOAI'=>trim($this->_request->getPost('ten_loai')),
				'ICON_NHO'=>$iconnho,
				'ICON_LON'=>$iconlon,
				'URL'=>$this->model->formatToUrl(trim($this->_request->getPost('ten_loai'))),
				'HIEN_THI'=>(($this->_request->getPost('hienthi'))?true:false),
				'HIEN_THI_MENU'=>(($this->_request->getPost('hienthimenu'))?true:false),
				'HIEN_THI_BANNER'=>(($this->_request->getPost('hienthibanner'))?true:false),
				'SAP_XEP_BANNER'=>($this->model->maxAll('loai_san_pham','SAP_XEP_BANNER')+1),
				'LOAI_BANNER'=>(boolean)$this->_request->getPost('lbanner'),
				'HIEN_THI_TC'=>(($this->_request->getPost('hienthitc'))?true:false),
				'SAP_XEP_TC'=>($this->model->maxAll('loai_san_pham','SAP_XEP_TC')+1),
				'VIDEO'=>(boolean)$this->_request->getPost('video'),
				'THONG_SO'=>(boolean)$this->_request->getPost('thongso'),
				'TIN_TUC'=>(boolean)$this->_request->getPost('tintuc'),
				'LSP_CHA'=>0,
			);
			
			if($this->model->Check("TEN_LOAI='".$this->_request->getPost('ten_loai')."'",'loai_san_pham')){
				$this->view->message="Loại sản phẩm đã tồn tại. Vui lòng điền tên khác.";
				$this->view->arrdata=$arr;
                if($iconnho!=""){
                        unlink(UPLOAD_PATH."/images/loaisp/".$iconnho);
                    }
                    if($iconlon!=""){
                        unlink(UPLOAD_PATH."/images/loaisp/".$iconlon);
                    }
			}else{
				if($this->model->Them($arr,'loai_san_pham')!=-1){
                    $this->model->insertLichSu('Thêm loại sản phẩm '.$arr['TEN_LOAI'],$this->view->iduser);
					$this->view->message="Thêm thành công";
				}else{
					$this->view->message="Có lỗi. Thêm thất bại";
                    if($iconnho!=""){
                        unlink(UPLOAD_PATH."/images/loaisp/".$iconnho);
                    }
                    if($iconlon!=""){
                        unlink(UPLOAD_PATH."/images/loaisp/".$iconlon);
                    }
				}
			}
		}
    }

    /**
    * Chức năng: Sửa lsp
    */
    public function sualspAction(){

        if($this->view->quyen["SUALSP"]==0){
            $this->_redirect("admin/error");
        }
        if($this->_request->getParam('id')!=null){

            if($this->_request->isPost()){
                
                $iconnho=$this->model->upload("iconnho",UPLOAD_PATH."/images/loaisp/");

                if($iconnho!=""){
                    if(!unlink(UPLOAD_PATH."/images/loaisp/".$this->_request->getPost("iconn"))){
                       
                    }
                }else{
                    $iconnho=$this->_request->getPost("iconn");
                }


                $iconlon=$this->model->upload("iconlon",UPLOAD_PATH."/images/loaisp/");

                if($iconlon!=""){
                    if(!unlink(UPLOAD_PATH."/images/loaisp/".$this->_request->getPost("iconl"))){
                       
                    }
                }else{
                    $iconlon=$this->_request->getPost("iconl");
                }
                if($this->_request->getPost("lsp_cha")==0){
                    $arr=array(
                        'TEN_LOAI'=>$this->_request->getPost('ten_loai'),
                        'ICON_NHO'=>$iconnho,
                        'ICON_LON'=>$iconlon,
                        'URL'=>$this->_request->getPost('url')
                    );
                }else{
                    $arr=array(
                        'TEN_LOAI'=>$this->_request->getPost('ten_loai'),
                        'ICON_NHO'=>$iconnho,
                        'ICON_LON'=>$iconlon,
                        'URL'=>$this->_request->getPost('url'),
                        'SAP_XEP_TC'=>$this->model->getRow('loai_san_pham','MALSP='.$this->_request->getPost('lsp_cha'),'SAP_XEP_TC')['SAP_XEP_TC'],
                        'LSP_CHA'=>$this->_request->getParam('lsp_cha')
                    );
                }
                
                 if($this->model->Sua($arr,'loai_san_pham','MALSP='.$this->_request->getPost('id'))!=-1){
                    $this->model->insertLichSu('Sửa loại sản phẩm '.$arr['TEN_LOAI'],$this->view->iduser);
                    $this->view->message="Sửa thành công";
                }else{
                    $this->view->message="Có lỗi. Sửa thất bại";
                    if($iconnho!=""){
                        unlink(UPLOAD_PATH."/images/loaisp/".$iconnho);
                    }
                    if($iconlon!=""){
                        unlink(UPLOAD_PATH."/images/loaisp/".$iconlon);
                    }
                }

            }

            $this->view->data=$this->model->getRow('loai_san_pham','MALSP='.$this->_request->getParam('id'));
            if(count($this->view->data)==0){
                 $this->_redirect('admin/loaisp');
            }
            if($this->view->data["LSP_CHA"]!=0){
                $this->view->listloaisp=$this->model->selectAll('loai_san_pham','LSP_CHA=0','MALSP,TEN_LOAI');
            }

            $this->view->headTitle("Sửa Loại Sản Phẩm ".$this->view->data["TEN_LOAI"]." - Admin Control Panel");
        

        }else{
            $this->_redirect('admin/loaisp');
        }
    }

    /**
    * Chức năng: thêm hãng sản xuất
    */
    public function themhangspAction(){
        if($this->view->quyen["THEMLSP"]==0){
            $this->_redirect("admin/error");
        }
        if($this->_request->getParam('id')==null){
            $this->_redirect('admin/loaisp');
        }

        if(count($this->model->getRow('loai_san_pham','MALSP='.$this->_request->getParam('id'),'MALSP'))==0){
             $this->_redirect('admin/loaisp');
        }

        $this->view->headTitle("Thêm Hãng Sản Xuất - Admin Control Panel");

        if($this->_request->isPost()){

            $iconnho=$this->model->upload("iconnho","uploads/images/loaisp/");
            $iconlon=$this->model->upload("iconlon","uploads/images/loaisp/");

            $arr=array(
                'MALSP'=>null,
                'TEN_LOAI'=>$this->_request->getPost('ten_loai'),
                'ICON_NHO'=>$iconnho,
                'ICON_LON'=>$iconlon,
                'URL'=>$this->model->formatToUrl($this->_request->getPost('ten_loai')),
                'HIEN_THI'=>(($this->_request->getPost('hienthi'))?true:false),
                'HIEN_THI_MENU'=>(($this->_request->getPost('hienthimenu'))?true:false),
                'HIEN_THI_BANNER'=>(($this->_request->getPost('hienthibanner'))?true:false),
                'SAP_XEP_BANNER'=>($this->model->maxAll('loai_san_pham','SAP_XEP_BANNER')+1),
                'LOAI_BANNER'=>(boolean)$this->_request->getPost('lbanner'),
                'HIEN_THI_TC'=>(($this->_request->getPost('hienthitc'))?true:false),
                'SAP_XEP_TC'=>$this->model->getRow('loai_san_pham','MALSP='.$this->_request->getPost('lsp_cha'),'SAP_XEP_TC')["SAP_XEP_TC"],
                'VIDEO'=>0,
                'THONG_SO'=>0,
                'TIN_TUC'=>0,
                'LSP_CHA'=>$this->_request->getPost("lsp_cha"),
            );
            
            if($this->model->Check("TEN_LOAI='".$this->_request->getPost('ten_loai')."'",'loai_san_pham')){
               $arr["URL"]=$this->model->getRow('loai_san_pham','MALSP='.$arr["LSP_CHA"],'URL')["URL"].'-'.$arr["URL"];
            } 
            if($this->model->Them($arr,'loai_san_pham')!=-1){
                $flag=true;
                $this->view->message="Thêm thành công";
            }else{
                $this->view->message="Có lỗi. Thêm thất bại";
                if($iconnho!="")
                unlink(UPLOAD_PATH."/images/loaisp/".$iconnho);
                if($iconlon!="")
                unlink(UPLOAD_PATH."/images/loaisp/".$iconlon);
            }
        }
        $this->view->tenlsp=$this->model->getRow('loai_san_pham','MALSP='.$this->_request->getParam('id'),'TEN_LOAI')['TEN_LOAI'];
        if(isset($flag)){
            $this->model->insertLichSu('Thêm hãng sản xuất '.$arr['TEN_LOAI'].' vào loại sản phẩm '.$this->view->tenlsp,$this->view->iduser);
        }
    }

     /**
    * Chức năng: hiển thị danh sách hóa đơn
    */
    public function hoadonAction(){
        if($this->view->quyen["XEMDSDH"]==0){
            $this->_redirect("admin/error");
        }

        if($this->_request->isPost()){
            if($this->_request->getPost('mahd')!=null){
                if($this->model->getRow('don_hang','MADH='.$this->_request->getPost('mahd'),'HOANTHANH')['HOANTHANH']==0){
                    $count=0;
                    foreach ($this->_request->getPost("arrxoact") as $item) {
                        $sum= $this->model->Tong('ctdh','SOLUONG*GIA','MADH='.$this->_request->getPost('mahd').' AND MASP='.$item);
                        if($this->model->Xoa('ctdh','MADH='.$this->_request->getPost('mahd')." AND MASP=".$item)>0){
                            $count++;
                            $tiennoa=$this->model->getRow('don_hang','MADH='.$this->_request->getPost('mahd'),'SOTIENNO')["SOTIENNO"];
                            $tiennoa=$tiennoa-$sum;
                            if($tiennoa<0)
                                $tiennoa=0;
                            $this->model->Sua(array("SOTIENNO"=>$tiennoa),'don_hang','MADH='.$this->_request->getPost('mahd'));
                        }
                    }
                    $this->model->insertLichSu('Xóa '.$count.' CTĐH thuộc đơn hàng '.$this->_request->getPost("mahd"),$this->view->iduser);
                    $this->view->message="Xóa thành công ".$count." chi tiết đơn hàng thuộc đơn hàng ".$this->_request->getPost("mahd");
                }else{
                    $this->view->message="Đơn hàng đã hoàn thành giao dịch. Bạn không thể xóa.";
                }
            }else{
                $this->view->message="";
                foreach (explode(" ", $this->_request->getPost('arrxoa')) as $item) {
                    if($this->model->Xoa('don_hang','MADH='.$item)>0){
                        $this->model->insertLichSu('Xóa đơn đặt hàng '.$item,$this->view->iduser);
                        $this->view->message.="Xóa thành công đơn hàng ".$item."<br>";
                    }else{
                       $this->view->message.="Xóa thất bại đơn hàng ".$item." có thể đơn hàng này có CTĐH<br>"; 
                    }
                }
            }
        }

        $this->view->headTitle("Danh sách đơn đặt hàng - Admin Control Panel");
        $sortby='MADH DESC';
        if($this->_request->getParam('sort')!=null){
            $sortby=str_replace("-", " ", $this->_request->getParam('sort'));
        }
        $query="";

        $this->view->vt=2;

        if($this->_request->getParam("nt")!=null){
            $query="SOTIENNO>0";
            $this->view->vt=1;
        }

        if($this->_request->getParam("cg")!=null){
            $query="HOANTHANH=0";
            $this->view->vt=0;
        }

        // if($this->_request->getParam("new")!=null){
        //     $query="DAXEM=0";
        //     $this->view->vt=2;
        // }

        if($this->_request->getParam('q')!=null){
            if($query!=""){
                $query.=" AND ";
            }
            $arrcut=explode("-", $this->_request->getParam('q'));
            $d=$this->model->isDate($arrcut[0]);
            if(isset($arrcut[1])){
                $d1=$this->model->isDate($arrcut[1]);
            }

            if((gettype($d)=="array" && count($d)>1) || (isset($d1) && count($d1)>0)){
                if(!isset($d1)){
                    if(isset($d["day"])){
                        $query.="day(NGAYLAP)=".$d["day"];
                    }
                    if(isset($d["month"])){
                        if(isset($d["day"]))
                            $query.=" and ";
                        $query.="month(NGAYLAP)=".$d["month"];
                        $query.=" and year(NGAYLAP)=".$d["year"];
                    }
                }else{
                    $dbegin="";
                    if(isset($d["year"])){
                        $dbegin.=$d["year"];
                    }
                    if(isset($d["month"])){
                        if(isset($d["year"]))
                            $dbegin.="/";
                        $dbegin.=$d["month"];
                    }else{
                        $dbegin.="/1";
                    }
                    if(isset($d["day"])){
                        if(isset($d["year"]) || isset($d["month"]))
                            $dbegin.="/";
                        $dbegin.=$d["day"];
                    }else{
                        $dbegin.="/1";
                    }
                    
                    

                    $dend="";
                    if(isset($d["year"])){
                        $dend.=$d1["year"];
                    }
                    if(isset($d1["month"])){
                        if(isset($d1["year"]))
                            $dend.="/";
                        $dend.=$d1["month"];
                    }
                    else{
                        $dend.="/1";
                    }
                    if(isset($d1["day"])){
                         if(isset($d1["year"]) || isset($d1["month"]))
                            $dend.="/";
                        $dend.=$d1["day"];
                    }else{
                        $dend.="/1";
                    }
                    
                    $query.="NGAYLAP between '".$dbegin."' and '".$dend."'";
                }
            }else{
                if($this->model->isPhoneNumber($this->_request->getParam('q'))){
                    $query.="khach_hang.DIENTHOAI='".$this->_request->getParam('q')."'";
                }else{
                    if((int)$this->_request->getParam('q')!=0){
                       $query.="MADH=".$this->_request->getParam('q');
                    }else{
                        if(strrpos($this->_request->getParam('q'), "@"))
                            $query.="khach_hang.EMAIL='".$this->_request->getParam('q')."'";
                        else{
                            $query.="khach_hang.TENKH like '%".$this->_request->getParam('q')."%'";
                        }
                    }
                }
            }
        }

        $start=0;
        $count=10;

        if($this->_request->getParam('page')!=null){
            $start=$this->_request->getParam('page');
        }

        $this->view->hoadon=$this->model->selectAll('don_hang',$query,'MADH,NGAYLAP,GHICHU,SOTIENNO,HOANTHANH,GIAOHANG,THANHTOAN,DAXEM,(select sum(SOLUONG) from ctdh where ctdh.MADH=don_hang.MADH) as sl,(select sum(GIA*SOLUONG) from ctdh where ctdh.MADH=don_hang.MADH) as sum,khach_hang.EMAIL,khach_hang.TENKH,khach_hang.DIENTHOAI,khach_hang.DIACHI,khach_hang.TENNN,khach_hang.DIENTHOAINN,khach_hang.DIACHINN',$sortby,$start*$count,$count,'khach_hang','don_hang.MAKH=khach_hang.MAKH');
        $sum=$this->model->dem('don_hang',$query,'MADH');


        $where="";
        foreach ($this->view->hoadon as $item) {
            $where.=$item["MADH"].",";
        }
        $where=mb_substr($where, 0,strlen($where)-1);

        $this->view->cthd=$this->model->selectAll('ctdh','MADH in ('.$where.')','MADH,TENSP,HINHANH,ctdh.GIA,ctdh.SOLUONG,san_pham.URL,san_pham.MASP',null,null,null,'san_pham','ctdh.MASP=san_pham.MASP');
        $this->view->hthd=$this->model->selectAll('hoan_thanh','MADH in('.$where.')','hoan_thanh.MADH,nhan_vien.HOTEN,NGAY_HT',null,null,null,'nhan_vien','hoan_thanh.MANV=nhan_vien.MANV');

        $this->view->arr_page=array(
            'sum'=>$sum,
            'current'=>$start,
            'count'=>$count,
        );

        $this->model->Sua(array('DAXEM'=>true),'don_hang',null);
    }

    /**
    * Chức năng: in hóa đơn
    */
    public function inhoadonAction(){
        if($this->_request->getParam("id")==null){
            $this->_redirect("admin/hoadon");
        }
        $this->getHelper("layout")->disableLayout();
        $this->view->idkh=$this->model->getRow('don_hang','MADH='.$this->_request->getParam('id'),'MAKH,NGAYLAP');

        $this->view->kh=$this->model->getRow('khach_hang','MAKH='.$this->view->idkh['MAKH']);


        $this->view->lct=$this->model->selectAll('ctdh','MADH='.$this->_request->getParam('id'),'ctdh.*,san_pham.TENSP,san_pham.HINHANH',null,null,null,'san_pham','ctdh.MASP=san_pham.MASP');
    }

    /**
    * Chức năng: hoàn thành hóa đơn
    */
    public function hthoadonAction(){
        if($this->_request->getParam("id")==null){
            $this->_redirect("admin/hoadon");
        }

        if($this->_request->isPost()){
            if($this->_request->getPost("tra")==2){
                $tientra=(double)(preg_replace("/(\.|-| |\,)*/", "", trim($this->_request->getPost('tientra'))));
                $tienconno=(double)$this->_request->getPost("tienno");

                if($tienconno<$tientra){
                    $this->view->message="Số tiền trả phải nhỏ hơn tiền còn nợ. ";
                }else{
                    if(  $this->model->Sua(array("SOTIENNO"=>($tienconno-$tientra).'',"HOANTHANH"=>true),'don_hang','MADH='.$this->_request->getPost('id'))>0){
                        $this->view->message="Cập nhật hóa đơn thành công";
                    }
                }
            }else{
                if($this->model->Sua(array("HOANTHANH"=>true,"SOTIENNO"=>0),'don_hang','MADH='.$this->_request->getPost('id'))>0){
                    $this->model->insertLichSu('Hoàn thành đơn hàng '.$this->_request->getPost('id'),$this->view->iduser);
                    $this->view->message="Cập nhật đơn hàng thành công";
                }
            }

            if($this->model->dem('hoan_thanh','MADH='.$this->_request->getPost('id'),'MADH')==0){
                $this->model->Them(array('MAHT'=>'NULL','MADH'=>$this->_request->getPost('id'),'MANV'=>$this->view->iduser,'NGAY_HT'=>array()),'hoan_thanh');
            }

            if($this->_request->getPost('ht')==0){
                $lcthd=$this->model->selectAll("ctdh",'MADH='.$this->_request->getPost('id'),'MASP,SOLUONG');
                foreach ($lcthd as $item) {
                    $this->model->Sua(array('SOLUONGBANDUOC'=>array('SOLUONGBANDUOC+'.$item['SOLUONG'])),'san_pham','MASP='.$item["MASP"]);
                }
            }
        }

        $this->view->headTitle("Hoàn thành đơn đặt hàng - Admin Control Panel");
        $this->view->info=$this->model->getRow('don_hang','MADH='.$this->_request->getParam("id"),'MADH,HOANTHANH,SOTIENNO,(select sum(SOLUONG*GIA) from ctdh where ctdh.MADH=don_hang.MADH) as sum');
        if(count($this->view->info)==0){
            $this->_redirect("admin/hoadon");
        }
        if($this->view->info["HOANTHANH"]==1 && $this->view->info["SOTIENNO"]==0){
            $this->_redirect("admin/hoadon");
        }

    }

    /**
    * Chức năng: hiển thị danh sách khách hàng
    */
    public function khachhangAction(){
        if($this->view->quyen["XEMDSKH"]==0){
            $this->_redirect("admin/error");
        }

        if($this->_request->isPost()){
            if($this->_request->getPost("name")==null){
                if($this->model->Sua(array('KHOA'=>($this->_request->getPost("khoa")==1)?true:false),'khach_hang','MAKH='.$this->_request->getPost('makh'))>0){
                    if($this->_request->getPost("khoa")==1){
                        $this->model->insertLichSu('Khóa khách hàng '.$this->_request->getPost("tenkh"),$this->view->iduser);
                        $this->view->message="Khóa thành công khách hàng ".$this->_request->getPost("tenkh");
                    }else{
                        $this->model->insertLichSu('Mở khóa khách hàng '.$this->_request->getPost("tenkh"),$this->view->iduser);
                        $this->view->message="Mở khóa thành công khách hàng ".$this->_request->getPost("tenkh");
                    }
                }else{
                    $this->view->message="Có lỗi xảy ra.";
                }
            }else{
                if($this->model->Xoa('khach_hang','MAKH='.$this->_request->getPost("id"))>0){
                    $this->view->message="Xóa thành công khách hàng ".$this->_request->getPost('name');
                }else{
                    $this->view->message="Xóa thất bại có thể khách hàng ".$this->_request->getPost('name')." đã lập đơn đặt hàng.";
                }
            }
        }

        $order="MAKH DESC";

        if($this->_request->getParam('sort')!=null){
            $order=str_replace("-", " ", $this->_request->getParam('sort'));
        }

        $query=null;

        if($this->_request->getParam('q')!=null){
            $query="TENKH like '%".$this->_request->getParam('q')."%' or khach_hang.EMAIL='".$this->_request->getParam('q')."' or DIENTHOAI='".$this->_request->getParam('q')."'";
        }

        $this->view->headTitle("Danh sách khách hàng - Admin Control Panel");

        
        $start=0;
        $count=10;

        if($this->_request->getParam('page')!=null){
            $start=$this->_request->getParam('page');
        }

        $this->view->kh=$this->model->selectAll('khach_hang',$query,null,$order,$start*$count,$count);

        $this->view->arr_page=array(
            'sum'=>$this->model->dem('khach_hang',$query,'MAKH'),
            'current'=>$start,
            'count'=>$count,
        );
    }

    /**
    * Chức năng: cài đặt website
    */
    public function websiteAction(){
        if($this->view->quyen["XOASP"]==0){
            $this->_redirect("admin/error");
        }

        if($this->_request->isPost()){
            if($this->_request->getParam('a')==null){
                $this->model->EditXmlFile(array('title','hotline','metadesc','metakeys'),array($this->_request->getPost('title'),$this->_request->getPost('hotro'),$this->_request->getPost('metadescription'),$this->_request->getPost('metakeyworks')));
            }else{
                switch ($this->_request->getParam('a')) {
                    case 'chung':
                        $this->model->EditXmlFile(array('title','hotline','metadesc','metakeys'),array($this->_request->getPost('title'),$this->_request->getPost('hotro'),$this->_request->getPost('metadescription'),$this->_request->getPost('metakeyworks')));
                        break;
                    case 'logo':
                        $this->model->upload("logo","public/Contents/Images/","logo.png");
                        break;
                    case 'menutop':
                        $this->model->EditMeutop($this->_request->getParam('id'),array($this->_request->getPost('text'),$this->_request->getPost('desc'),$this->_request->getPost('keys'),$this->_request->getPost('t')));
                        break;
                    case 'css':
                        switch ($this->_request->getPost("l")) {
                            case '1':
                                $this->view->filename='url image: <input type="text" class="input" value="Images/'.$this->model->upload("hinhanh","public/Contents/Images/").'" />';
                                break;
                            case '2':
                                file_put_contents("public/Contents/css.css",$this->_request->getPost('event'));
                                break;
                        }
                        break;
                    case 'tuyendung': case 'baohanh': case 'huongdan':
                        file_put_contents($_SERVER['DOCUMENT_ROOT'].$this->view->baseUrl()."/data/".$this->_request->getParam('a').".dat","\xEF\xBB\xBF".$this->_request->getPost('data'));
                        break;
                    case 'baotri':
                        $this->model->EditXmlFile('bt',$this->_request->getPost("l"));
                        break;
                }
            }
        }

        $xml=new Zend_Config_Xml('website.xml');


        $this->view->title=array("t"=>$xml->info->title,"h"=>$xml->info->hotline,"d"=>$xml->info->metadesc,"k"=>$xml->info->metakeys);
        $this->view->menutop=$xml->info->menutop->item;
        $this->view->baotri=$xml->info->bt;
        $this->view->data=file_get_contents($_SERVER['DOCUMENT_ROOT'].$this->view->baseUrl()."/data/".$this->_request->getParam('a').".dat");

    }

    /**
    * Chức năng: danh sách nhóm
    */
    public function nhomAction(){
        if($this->view->quyen["XEMDSN"]==0){
            $this->_redirect("admin/error");
        }
        $this->view->headTitle("Danh Sách Nhóm");
        if($this->_request->isPost()){
            if($this->model->Xoa('nhom','MANHOM='.$this->_request->getPost('id'))>0){
                $this->model->insertLichSu('Xóa nhóm '.$this->_request->getPost('name'),$this->view->iduser);
                $this->view->message="Xóa thành công";
            }else{
                 $this->view->message="Xóa thất bại";
            }
        }
        $this->view->data=$this->model->selectAll('nhom');
        $this->view->nv=$this->model->selectAll('nhan_vien',null,'MANV,HOTEN,MANHOM');
    }

    /**
    * Chức năng: thêm nhóm mới
    */
    public function themnhomAction(){
        if($this->view->quyen["THEMNHOM"]==0){
            $this->_redirect("admin/error");
        }
        $this->view->headTitle("Thêm Nhóm");
        if($this->_request->isPost()){
            if($this->model->Them($this->model->post('MANHOM'),'nhom')>0){
                $this->model->insertLichSu('Thêm nhóm '.$this->_request->getPost('TENNHOM'),$this->view->iduser);
                $this->view->message="Thêm thành công";
            }else{
                 $this->view->message="Thêm thất bại";
            }
        }
    }

    /**
    * Chức năng: sửa nhóm
    */
    public function suanhomAction(){
        if($this->view->quyen["SUANHOM"]==0){
            $this->_redirect("admin/error");
        }
        $this->view->headTitle("Sửa Nhóm");
        if($this->_request->isPost()){
            if($this->model->Sua($this->model->post(),'nhom','MANHOM='.$this->_request->getParam('id'))>0){
                $this->model->insertLichSu('Sửa nhóm '.$this->_request->getPost('TENNHOM'),$this->view->iduser);
                $this->view->message="Sửa thành công";
            }else{
                 $this->view->message="Sửa thất bại";
            }
        }
        $this->view->data=$this->model->getRow('nhom','MANHOM='.$this->_request->getParam('id'));
        if(count($this->view->data)==0){
            $this->_redirect('admin/nhom');
        }
    }

    /**
    * Chức năng: nhân viên
    */
    public function nhanvienAction(){
        if($this->view->quyen["XEMDSNV"]==0){
            $this->_redirect("admin/error");
        }
        $this->view->headTitle("Danh Sách Nhân Viên - Admin Control Panel");

        if($this->_request->isPost()){
            switch ($this->_request->getPost("action")) {
                case 'trangthai':
                    if($this->_request->getPost("trangthai")==1){
                        $tt=0;
                    }else{
                        $tt=1;
                    }
                    $this->model->Sua(array('KHOA'=>(boolean)$tt),'nhan_vien','MANV='.$this->_request->getPost("manv"));
                   break;
                case 'resetmk':
                    $this->model->Sua(array('MATKHAU'=>md5('123456')),'nhan_vien','MANV='.$this->_request->getPost("id"));
                    $this->view->message="Mật khẩu nhân viên ".$this->_request->getPost("name")." đã được reset thành 123456";
                    break;
                case 'resetmk2':
                    $this->model->Sua(array('MATKHAU2'=>md5('123456')),'nhan_vien','MANV='.$this->_request->getPost("id"));
                    $this->view->message="Mật khẩu cấp 2 của nhân viên ".$this->_request->getPost("name")." đã được reset thành 123456";
                    break;
                case 'removenv':
                    if($this->model->Xoa('nhan_vien','MANV='.$this->_request->getPost('id'))>0){
                        $this->view->message="Xóa thành công nhân viên ".$this->_request->getPost('name');
                    }else{
                        $this->view->message="Xóa thất bại. Có thể nhân viên ".$this->_request->getPost('name')." đã gửi sản phẩm hoặc tin tức hoặc đã hoàn thành hóa đơn";
                    }
                break;
            }
        }

        $start=0;
        $count=10;

        if($this->_request->getParam('page')!=null){
            $start=$this->_request->getParam('page');
        }

        $sortby='MANV DESC';
        if($this->_request->getParam('sort')!=null){
            $sortby=str_replace("-", " ", $this->_request->getParam('sort'));
        }

        if($this->_request->getParam('q')==null){
            $this->view->data=$this->model->selectAll('nhan_vien',null,null,$sortby,$start*$count,$count);
            $sum=$this->model->dem('nhan_vien',null,'MANV');
        }else{
            $this->view->data=$this->model->selectAll('nhan_vien','HOTEN like "%'.$this->_request->getParam('q').'%" OR TAIKHOAN like "%'.$this->_request->getParam('q').'%"',null,$sortby,$start*$count,$count);
            $sum=$this->model->dem('nhan_vien','HOTEN like "%'.$this->_request->getParam('q').'%" OR TAIKHOAN like "%'.$this->_request->getParam('q').'%"','MANV');
        }
        $this->view->arr_page=array(
            'sum'=>$sum,
            'current'=>$start,
            'count'=>$count,
        );
        $this->view->nhom=$this->model->selectAll('nhom',null,'MANHOM,TENNHOM');
    }

    


    /**
    * Chức năng: thêm nhân viên
    */
    public function themnhanvienAction(){
        if($this->view->quyen["THEMNV"]==0){
            $this->_redirect("admin/error");
        }
        $this->view->headTitle("Thêm Nhân Viên");
        if($this->_request->isPost()){
            if($this->model->Them($this->model->post('MANV'),'nhan_vien')>0){
                $this->model->insertLichSu('Thêm nhân viên '.$this->_request->getPost('HOTEN'),$this->view->iduser);
                $this->view->message="Thêm thành công";
                unset($_POST);
            }else{
                 $this->view->message="Thêm thất bại. Có thể tài khoản đã tồn tại";
            }
        }
        $this->view->nhom=$this->model->selectAll('nhom',null,'MANHOM,TENNHOM');
    }

    /**
    * Chức năng: sửa nhân viên
    */
    public function suanvAction(){
        $id=0;
       
        if($this->_request->getParam("id")!=null){
            if($this->view->quyen["SUANV"]==1){
                $id=$this->_request->getParam("id");
            }else{
                 $this->_redirect("admin/error");
            }
        }else{
            $session=new Zend_Session_Namespace();
            $id=$session->loginadmin["ID"];
        }


        if($this->_request->isPost()){
            $this->model->Sua($this->model->post(),'nhan_vien','MANV='.$id);
            $this->model->insertLichSu('Sửa nhân viên '.$this->_request->getPost('HOTEN'),$this->view->iduser);
            $this->view->message="Sửa thành công";
            if($this->_request->getParam("id")==null){
                $session->loginadmin["HOTEN"]=$this->_request->getPost("HOTEN");
            }
        }

        $this->view->info=$this->model->getRow('nhan_vien','MANV='.$id,'MANV,HOTEN,GIOITINH,DIENTHOAI,EMAIL');
    }

    /**
    * Chức năng: tin tức
    */
    public function tintucAction(){
        if($this->view->quyen["XEMDSTT"]==0){
            $this->_redirect("admin/error");
        }
        $this->view->headTitle("Tin Tức - Admin Control Panel");

        if($this->_request->isPost()){
            if($this->model->Xoa('tin_tuc','MATT='.$this->_request->getPost('id'))>0){
                $this->model->insertLichSu('Xóa tin tức '.$this->_request->getPost('name'),$this->view->iduser);
                $this->view->message="Xóa thành công";
                unlink(UPLOAD_PATH."/images/tintuc/".$this->_request->getPost('hinhanh'));
            }else{
                 $this->view->message="Xóa thất bại";
            }
        }

        $start=0;
        $count=10;

        if($this->_request->getParam('page')!=null){
            $start=$this->_request->getParam('page');
        }

        $sortby='MATT DESC';
        if($this->_request->getParam('sort')!=null){
            $sortby=str_replace("-", " ", $this->_request->getParam('sort'));
        }

        if($this->_request->getParam('q')==null){
            $this->view->data=$this->model->selectAll('tin_tuc',null,null,$sortby,$start*$count,$count,'nhan_vien','tin_tuc.NGUOIGUI=nhan_vien.manv');
            $sum=$this->model->dem('tin_tuc',null,'MATT');
        }else{
            $this->view->data=$this->model->selectAll('tin_tuc','TIEUDE like "%'.$this->_request->getParam('q').'%"',null,$sortby,$start*$count,$count,'nhan_vien','tin_tuc.NGUOIGUI=nhan_vien.manv');
            $sum=$this->model->dem('tin_tuc','TIEUDE like "%'.$this->_request->getParam('q').'%"','MATT');
        }
        $this->view->arr_page=array(
            'sum'=>$sum,
            'current'=>$start,
            'count'=>$count,
        );
    }



    /**
    * Chức năng: thêm tin tức
    */
    public function themttAction(){
         if($this->view->quyen["THEMTT"]==0){
            $this->_redirect("admin/error");
        }
        $this->view->headTitle("Thêm Tin Tức - Admin Control Panel");
        if($this->_request->isPost()){
            $image=$this->model->upload("hinhanh",UPLOAD_PATH."/images/tintuc/");
            $imageslide="";
            if($this->_request->getPost("SLIDESHOW")){
                $imageslide=$this->model->upload("hinhanhslide",UPLOAD_PATH."/images/slide/");
            } 
            $arr=array(
                'MATT'=>'NULL',
                'NGUOIGUI'=>$this->view->iduser,
                'TIEUDE'=>$this->_request->getPost('TIEUDE'),
                'URL'=>$this->model->formatToUrl($this->_request->getPost('TIEUDE')),
                'MOTA'=>$this->_request->getPost('MOTA'),
                'NOIDUNG'=>htmlentities($this->_request->getPost('NOIDUNG'),ENT_QUOTES,'UTF-8',false),
                'HINHANH'=>$image,
                'HINHANH_SLIDE'=>$imageslide,
                'THOIGIAN'=>getdate(),
                'SLIDESHOW'=>(boolean)$this->_request->getPost('SLIDESHOW'),
                'TUKHOA'=>$this->_request->getPost('tukhoa'),
                'XEM'=>0,
                'HIEN_THI'=>true
            );
             if($this->model->Them($arr,'tin_tuc')>0){
                $_POST['TIEUDE']=null;
                $_POST['MOTA']=null;
                $_POST['NOIDUNG']=null;
                $_POST['TUKHOA']=null;
                $this->model->insertLichSu('Thêm tin tức '.$arr['TIEUDE'],$this->view->iduser);
                $this->view->message="Thêm thành công";
            }else{
                unlink(UPLOAD_PATH."/images/tintuc/".$arr["HINHANH"]);
                if($arr['SLIDESHOW'])
                    unlink(UPLOAD_PATH."/images/slide/".$arr["HINHANH_SLIDE"]);
                 $this->view->message="Thêm thất bại. Có thể tin tức đã tồn tại";
            }  
       }
    }

    /**
    * Chức năng: thêm tin tức
    */
    public function suattAction(){
        if($this->view->quyen["SUATT"]==0){
            $this->_redirect("admin/error");
        }
        if($this->_request->getParam('id')==null){
            $this->_redirect("admin/tintuc");
        }
        $this->view->headTitle("Sửa Tin Tức - Admin Control Panel");

        if($this->_request->isPost()){
            $image=$this->model->upload("hinhanh",UPLOAD_PATH."/images/tintuc/");
            if($image==""){
                $image=$this->_request->getPost("hinhanhcu");
            }else{
                unlink(UPLOAD_PATH."/images/tintuc/".$this->_request->getPost("hinhanhcu"));
            }
            $imageslide=$this->_request->getPost('hinhanhslidecu');
            if($this->_request->getPost("SLIDESHOW")){
                $imageup=$this->model->upload("hinhanhslide",UPLOAD_PATH."/images/slide/");
                if($imageup!=""){
                    if($imageslide!=""){
                        unlink(UPLOAD_PATH."/images/slide/".$imageslide);
                    }
                    $imageslide=$imageup;
                }
            } 
            $arr=array(
                'TIEUDE'=>$this->_request->getPost('TIEUDE'),
                'URL'=>$this->model->formatToUrl($this->_request->getPost('TIEUDE')),
                'MOTA'=>$this->_request->getPost('MOTA'),
                'NOIDUNG'=>htmlentities($this->_request->getPost('NOIDUNG'),ENT_QUOTES,'UTF-8',false),
                'HINHANH'=>$image,
                'HINHANH_SLIDE'=>$imageslide,
                'SLIDESHOW'=>(boolean)$this->_request->getPost('SLIDESHOW'),
                'TUKHOA'=>$this->_request->getPost('tukhoa')
            );
             if($this->model->Sua($arr,'tin_tuc','MATT='.$this->_request->getParam('id'))>0){
                $this->model->insertLichSu('Sửa tin tức '.$arr['TIEUDE'],$this->view->iduser);
                $this->view->message="Sửa thành công";
            }else{
                 $this->view->message="Sửa thất bại. Có thể tin tức đã tồn tại";
            }  
       }

        $this->view->data=$this->model->getRow('tin_tuc','MATT='.$this->_request->getParam('id'));

        if(count($this->view->data)==0){
            $this->_redirect("admin/tintuc");
        }
    }

    public function sortAction(){

    	if($this->_request->getParam('redirect')!=null){
    		switch ($this->_request->getParam('redirect')) {
    			case 'loaisp':
                    switch ($this->_request->getParam('a')) {
                        case '1':
                            $vitrihientai=$this->model->getRow('loai_san_pham','MALSP='.$this->_request->getParam('id'),'SAP_XEP_TC')['SAP_XEP_TC'];
                            if($this->_request->getParam('l')=='down'){
                                $vitrimoi=$this->model->getRow('loai_san_pham','SAP_XEP_TC>'.$vitrihientai,'SAP_XEP_TC','SAP_XEP_TC')['SAP_XEP_TC'];
                            }
                            else{
                                $vitrimoi=$this->model->getRow('loai_san_pham','SAP_XEP_TC<'.$vitrihientai,'SAP_XEP_TC','SAP_XEP_TC DESC')['SAP_XEP_TC'];
                            }
                            $idvitrimoi=$this->model->getRow('loai_san_pham','SAP_XEP_TC='.$vitrimoi.' AND LSP_CHA=0','MALSP')['MALSP'];
                            $this->model->Sua(array('SAP_XEP_TC'=>$vitrimoi),'loai_san_pham','MALSP='.$this->_request->getParam('id').' OR LSP_CHA='.$this->_request->getParam('id'));
                            $this->model->Sua(array('SAP_XEP_TC'=>$vitrihientai),'loai_san_pham','MALSP='.$idvitrimoi.' OR LSP_CHA='.$idvitrimoi);
                            break;
                        case '2':
                            $vitrihientai=$this->model->getRow('loai_san_pham','MALSP='.$this->_request->getParam('id'),'SAP_XEP_BANNER')['SAP_XEP_BANNER'];
                            if($this->_request->getParam('l')=='down'){
                                $vitrimoi=$this->model->getRow('loai_san_pham','SAP_XEP_BANNER>'.$vitrihientai,'SAP_XEP_BANNER','SAP_XEP_BANNER')['SAP_XEP_BANNER'];
                            }
                            else{
                                $vitrimoi=$this->model->getRow('loai_san_pham','SAP_XEP_BANNER<'.$vitrihientai,'SAP_XEP_BANNER','SAP_XEP_BANNER DESC')['SAP_XEP_BANNER'];
                            }
                            $idvitrimoi=$this->model->getRow('loai_san_pham','SAP_XEP_BANNER='.$vitrimoi,'MALSP')['MALSP'];
                            $this->model->Sua(array('SAP_XEP_BANNER'=>$vitrimoi),'loai_san_pham','MALSP='.$this->_request->getParam('id'));
                            $this->model->Sua(array('SAP_XEP_BANNER'=>$vitrihientai),'loai_san_pham','MALSP='.$idvitrimoi);
                            break;
                    }
                    break;
    		}
    		$this->_redirect('admin/'.$this->_request->getParam('redirect'));
    	}else{
    		$this->_redirect('admin');
    	}
    }

    public function errorAction(){
        $this->view->headTitle("Lỗi truy cập trang");
    }

    public function doimatkhauAction(){
        $this->view->headTitle("Đổi Mật Khẩu");
        if($this->_request->isPost()){
            $session=new Zend_Session_Namespace();
            
            switch ($this->_request->getPost("l")) {
                case '1':
                    $matkhaucu=$this->model->getRow("nhan_vien",'MANV='.$session->loginadmin['ID'],'MATKHAU')['MATKHAU'];
                    if(md5($this->_request->getPost('matkhaucu'))!=$matkhaucu){
                        $this->view->message="Mật khẩu cũ sai. Nếu bạn đã quyên mật khẩu, vui lòng liên hệ admin để reset lại.";
                    }else{
                        if($this->model->Sua(array("MATKHAU"=>md5($this->_request->getPost('matkhaumoi'))),'nhan_vien','MANV='.$session->loginadmin["ID"])>0){
                            $this->view->message="Sửa thành công";
                        }else{
                             $this->view->message="Sửa thất bại.";
                        }  
                    }
                    break;
                
                case '2':
                    $matkhaucu2=$this->model->getRow("nhan_vien",'MANV='.$session->loginadmin['ID'],'MATKHAU2')['MATKHAU2'];
                    if(md5($this->_request->getPost('matkhaucu2'))!=$matkhaucu2){
                        $this->view->message2="Mật khẩu cấp 2 sai. Nếu bạn đã quyên mật khẩu, vui lòng liên hệ admin để reset lại.";
                    }else{
                        if($this->model->Sua(array("MATKHAU2"=>md5($this->_request->getPost('matkhaumoi2'))),'nhan_vien','MANV='.$session->loginadmin["ID"])>0){
                            $this->view->message2="Sửa thành công";
                            $session->loginadmin['MATKHAU2']=md5($this->_request->getPost('matkhaumoi2'));
                        }else{
                             $this->view->message2="Sửa thất bại.";
                        }  
                    }
                break;
            }
            
        }
    }

}