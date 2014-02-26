<?php
// Asms 胜意系统 curl 操作
class AsmsAction extends CommonAction {
    //Asms业务员列表
	function user(){
        if(I('so')){
            if(strstr(I('so'),':')){
                $so=explode(':',I('so'));
                $map[$so[0]]=$so[1];
            }else{
                $where['name'] = array('like',"%".I('so')."%");
                $where['ywyid']  = array('like',"%".I('so')."%");
                $where['phone']  = array('like',"%".I('so')."%");
                $where['_logic'] = 'or';
                $map['_complex'] = $where;
            }
        }

        $this->map = $map;
        $this->relation=true;
        parent::index(D('AsmsUser'));
     //   print_r($this->list);
        $this->display();

	}



    //更新 全部 清表重新获取
    function updateAllUser(){
        $rs=D('AsmsUser')->insert_all();
        if($rs){
            $this->success('更新完成');
        }else{
            $this->error('更新失败');
        }
    }

    //更新 指定的用户
    function updateUser(){
        if(!isset($_GET['ywyid'])){$this->error('非法操作');}
        D('AsmsUser')->update($_GET['ywyid']);
        $this->success('操作完成');
    }

    //更新自动匹配
    function updateAutoMatch(){
        if(!isset($_GET['ywyid'])){$this->error('非法操作');}
        $where['ywyid']=$_GET['ywyid'];
        $this->updateAutoMatchAll($where);
    }

    //更新全部自动匹配
    function updateAutoMatchAll($where=array()){
        $rs=D('AsmsUser')->where($where)->select();
        $i=0;
        foreach($rs as $val){
            $map['name']=$val['name'];
            $data['asms_user_id']=$val['ywyid'];
            $data['update_time']=time();
            $rs=D('User')->where($map)->save($data);
            $rs && $i++;
        }
        $this->success('操作成功 更新：'.$i."条");

    }



/*************************************/
    /*
     * 会员用户列表
     */
    function member(){
        if(I('so')){
            $where['hyid'] = array('like',"%".I('so')."%");
            $where['hyzcm']  = array('like',"%".I('so')."%");
            $where['hykh']  = array('like',"%".I('so')."%");
            $where['xm']  = array('like',"%".I('so')."%");
            $where['ywyid']  = array('like',"%".I('so')."%");
            $where['ywy']  = array('like',"%".I('so')."%");
            $where['sj']  = array('like',"%".I('so')."%");
            $where['_logic'] = 'or';
            $map['_complex'] = $where;
        }
        if(I('is_ss')){
            $_GET['page_r']=100;
            $this->list=D('AsmsMember')->memberFindAll(array("hyzcm"=>I('so')));
        }else{
            $this->map = $map;
            $this->relation=true;
            parent::index(D('AsmsMember'));
         //   print_r($this->list);
        }
        $this->display();

    }

    /*
     * 查看用户信息
     */
    function  memberInfo(){
        if(!I('hyid')) $this->error("参数有误");
        $AsmsMemberDB=D('AsmsMember');
        $this->info=$AsmsMemberDB->memberInfo(I('hyid'));
    //    echo $AsmsMemberDB->getError();
    //    dump($this->info);
        $this->display();
    }

    //会员更新
    function memberUpdate(){
        $hyid = I('hyid');
       $rs= D('AsmsMember')->memberInfo($hyid);
    //    print_r($rs);
        $this->success("成功");
         $this->display();
     }

    //会员全部更新
    function memberUpdateALL(){
        echo "正在更新...";
        $page_r=isset($_GET['page_r'])?"?page_r=".$_GET['page_r']."":'?page_r=50';
        $page_p=isset($_GET['page_p'])?"&page_p=".($_GET['page_p']+1)."":'&page_p=1';
        $url=$page_r.$page_p;
        if(!isset($_GET['page_p'])) echo "<script>location.href='$url';</script>";
        $AsmsMember=D('AsmsMember');
        $rs= $AsmsMember->memberFindAll(array());

        if(!$rs){
            echo   $AsmsMember->getError();
            echo "<script type='text/javascript'>window.location.reload();</script>";
        }

        if(!isset($_GET['page_tr']) || $_GET['page_tr']==$_GET['page_p']){
            echo "更新完成";exit;
        }

        echo "<script type='text/javascript'>location.href='$url';</script>";
        //    print_r($rs);
        $this->display();
    }

    //会员同步更新
    function memberUpdateNew(){
        echo "正在更新...";


        $AsmsMember=D('AsmsMember');
        $page_r=isset($_GET['page_r'])?"?page_r=".$_GET['page_r']."":'?page_r=50';
        $page_p=isset($_GET['page_p'])?"&page_p=".($_GET['page_p']+1)."":'&page_p=1';


        $cjrq=$AsmsMember->order('cjrq desc')->getField('cjrq');
        $cjrq=date("Y-m-d",strtotime($cjrq)-(3600*24));
        $_GET['createdate1']=isset($_GET['createdate1'])?$_GET['createdate1']:$cjrq;
        $url=$page_r.$page_p."&createdate1=".$_GET['createdate1'];
        if(!isset($_GET['page_p'])) echo "<script>location.href='$url';</script>";

        $cjrq=$AsmsMember->order('cjrq desc')->getField('cjrq');
        $_GET['createdate1']=isset($_GET['createdate1'])?$_GET['createdate1']:$cjrq;

        $rs= $AsmsMember->memberFindAll(array());

        if($rs==-1){
            echo "asms 网络错误";
            echo "<script type='text/javascript'>window.location.reload();</script>";
            exit;
        }
        if(!$rs){
            echo   $AsmsMember->getError();
            exit;
        }
        if(!isset($_GET['page_tr']) || $_GET['page_tr']==$_GET['page_p']){
            echo "更新完成";exit;
        }

        echo "<script type='text/javascript'>location.href='$url';</script>";
        //    print_r($rs);
        $this->display();
    }

    function register(){
        $AsmsMember= D('AsmsMember');
      //  $AsmsMember->checkMember($_GET);
        $data['hyzcm']='test123455';
        $data['xm']='test123456';
        $data['hykh']='test123455';
        $data['ywyid']='6000';
        $data['sj']='18673800257';
        $rs= $AsmsMember->register($data);
        if($rs===false){
            echo $AsmsMember->getError();
        }
        echo $rs;
    }

    /*
     * 编辑asms会员信息
     */
    function memberEdit(){
        $AsmsMember= D('AsmsMember');
        $data=$_POST;
        if(!isset($data['hyid'])) $this->error("参数有误");
        $rs=$AsmsMember->memberEdit($data);
        if($rs){

        }
        return ;
    }

    /*****************************************************/

    /**
     * 订单列表
     */
    function orderList(){

        if(I('so')){
            $where['ddbh'] = array('like',"%".I('so')."%");
            $where['hyid']  = array('like',"%".I('so')."%");
            $where['pnr']  = array('like',"%".I('so')."%");
            $where['xm']  = array('like',"%".I('so')."%");
            $where['zjhm']  = array('like',"%".I('so')."%");
            $where['lxdh']  = array('like',"%".I('so')."%");
            $where['hykh']  = array('like',"%".I('so')."%");
            $where['pnr']  = array('like',"%".I('so')."%");
            $where['_logic'] = 'or';
            $map['_complex'] = $where;
        }
        if(I('ksrq')&& I('jsrq')){
            $map['dprq']=array(array('egt',I('ksrq')),array('elt',I('jsrq')));
        }
        $AsmsOrder=D('AsmsOrder');

        if(I('gj_so')){ //高级搜索
            I('hyzcm')&& $_POST['khid']=D('AsmsMember')->checkMember(I('hyzcm'));
            $this->list=$AsmsOrder->orderFindAll($_POST);
            $this->numPerPage=isset($_GET['numPerPage'])?$_GET['numPerPage']:100;
            //    $this->pageNum=isset($_GET['pageNum'])?$_GET['pageNum']:1;
            //   dump($_GET);
            $this->totalCount=$_GET['totalCount']; //
            $this->totalPages=$_GET['totalPages'];
            $this->currentPage=isset($_GET['pageNum'])?$_GET['pageNum']:1;
        }elseif(I('is_ss')){
            I('so') && $rs=D('AsmsMember')->checkMember(I('so'));
            $where['ksrq']=I('ksrq');
            $where['jsrq']=I('jsrq');
            if(is_numeric($rs)){
                $this->list=$AsmsOrder->orderFindAll(array("khid"=>$rs));
            }else{
                $this->list=$AsmsOrder->orderFindAll(array("khid"=>I('so')));
            }
            $this->numPerPage=isset($_GET['numPerPage'])?$_GET['numPerPage']:100;
        //    $this->pageNum=isset($_GET['pageNum'])?$_GET['pageNum']:1;
         //   dump($_GET);
            $this->totalCount=$_GET['totalCount']; //
            $this->totalPages=$_GET['totalPages'];
            $this->currentPage=isset($_GET['pageNum'])?$_GET['pageNum']:1;
        }else{
            $this->map = $map;
            $this->relation=true;
            parent::index($AsmsOrder);
        }

        $this->list==-1 && $this->error($AsmsOrder->getError());;
        $this->display();
    }

    /*
     * 订单详细
     */
    function orderInfo(){
        $AsmsOrder=D("AsmsOrder");
        $rs=$AsmsOrder->getOrderInfo(I('id'));
        $this->info=$rs;
        $this->display();
    }

    /*
     * 更新订单
     */
    function updateOrder(){
        $pnr=I('pnr');
        $AsmsOrder=D("AsmsOrder");
        $where['pnr']=$pnr;
        if($AsmsOrder->orderFind($where)){
            $this->success("成功");
        }else{
            $this->error("更新失败".$AsmsOrder->getError());
        }
    }

    function orderDel(){
        $AsmsOrder=D("AsmsOrder");
    //    echo $_GET['ddbh'];
        $rs=$AsmsOrder->orderDel($_GET['ddbh']);
        if($rs){
            $this->success("成功");
        }
        $this->error("失败");
    }
}
?>