<?php
class ZtAction extends IniAction {
    public function index(){
         $where['name']=I('name');
         $rs=D('Zt')->where($where)->find();
         $tpl=$rs['tpl']?$rs['tpl']:I('name');
        foreach($rs as $key=>$val){
            $this->$key=$val;
        }

        $this->display($tpl);
    }

    function zqgq(){
        $this->title='中秋.国庆 出国游 大特惠';
        $this->display();
    }
	
	function gqcf(){
        $this->title='国庆机票金秋惠专题';
        $this->display();
    }

    function lxs(){
        $this->display();
    }
	
	function njcg(){
        $this->title='年假出国, 特价机票精选';
        $this->display();
    }
	
	function sdyd2013(){
        $this->title='圣诞元旦双节狂欢';
        $this->display();
    }
	function chaoditejia(){
        $this->title='二线城市出发超低特价大放送';
        $this->display();
    }
	function dijialaixi(){
        $this->title='一线城市往返欧洲低价来袭';
        $this->display();
    }
	function thtjbjbm(){
        $this->title='特惠推荐-北京往返北美';
        $this->display();
    }
	
	
	
	/*活动频道-品牌专区*/
	function united_sales(){
        $this->title='美联航限时特惠';
        $this->display();
    }
	function united_sales2(){
        $this->title='美联航-穗港赴北美畅游超值精选';
        $this->display();
    }
	function srilankan_sales(){
        $this->title='斯里兰卡航空公司';
        $this->display();
    }	
	function cathaypacific_sales(){
        $this->title='国泰航空公司';
        $this->display();
    }
	function cathaypacific_sales2(){
        $this->title='国泰航空广州出行北美超值推介';
        $this->display();
    }
	function flysas_sales(){
        $this->title='北欧航空';
        $this->display();
    }
	function ethiopian_sales(){
        $this->title='埃塞俄比亚航空';
        $this->display();
    }	
	function airchina_sales(){
        $this->title='中国国际航空';
        $this->display();
    }
	function virgin_atlantic_sales(){
        $this->title='维珍航空';
        $this->display();
    }
	function singaporeair_sales(){
        $this->title='新加坡航空';
        $this->display();
    }
	function hnair_sales(){
        $this->title='海南航空';
        $this->display();
    }

    function asianaAirlines_sales(){
        $this->title='韩亚航空';
        $this->display();
    }
	
	
	
	/*特别专题*/
	function firstClass(){
        $this->title='头等舱 / 商务舱预订专区';
        $this->display();
    }
	

	function weidianying(){
        $this->title='爱尚飞-微电影大赛预告';
        $this->display();
    }
	function wdy(){
        $this->display();
    }

    //微电影首页
	function wdyindex(){

        $list=D('Video')->select();

        if($_GET['zs']){  //查看真实投票
            if($_GET['add']){
                for($i=1;$i<=18;$i++){
                 $num=rand(10,99);
                 $sql="update asf_video set vote_num=vote_num+{$num},view_num=view_num+({$num}*2),keywords=concat(keywords,'+{$num}') where groups={$i}";
                 print_r(M('Video')->query($sql));
                }
            }
            $rs=D('vote_log')->field("count('action') count,vid")->where("action='vote'")->group('vid')->order("count('action') desc")->select();

              foreach($list as $k=>$v){
                  foreach($rs as $kk=>$vv){
                    if($v['id']==$vv['vid']){
                        $list[$k]['real']=$vv['count'];
                  }
                }
            }

            foreach ($list as $key => $value) {
                $name[$key] = $value['real'];
            }
            array_multisort($name,SORT_DESC, $list);
        //    print_r($list);

        }
        $this->list=$list;
        $this->display();
    }

    //评论
    function wdyPl(){
        if(IS_AJAX){
            $video_pl=D('VideoPl');
            if(IS_POST){
                if(!getUid()){
                    $this->error('您还未登陆，请先登陆',U('/member/login'));
                }
                if(strlen(I('val'))<5){
                    $this->error('评论不能少于五个字符');
                }
                $where['member']=getUid();
                $last_time=$video_pl->where($where)->order('create_time desc')->getField('create_time');

                if(($last_time+8)>time()){
                    $this->error('您发的太快了，休息一下吧！');
                }
                $data['vid']=I('id');
                $data['member_id']=getUid();
                $data['content']=I('val');
                $data['create_time']=time();
                $data['ip']=get_client_ip();
                $rs=$video_pl->add($data);
                if(!$rs){
                    $this->error('发布失败了');
                }

            }

            import('ORG.Util.Page');// 导入分页类
            $map['vid']=I('id');
            $pageNum=20;
            $count      = $video_pl->where($map)->count();// 查询满足要求的总记录数
            $Page       = new Page($count,$pageNum,'','',1);// 实例化分页类 传入总记录数和每页显示的记录数
            $show       = $Page->show();// 分页显示输出
            $list=$video_pl->where($map)->relation(true)->order('create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
            foreach($list as $k=>$v){
                $list[$k]['time']=date("Y-m-d H:i:s",$v['create_time']);
                $list[$k]['member_name']=$v['member_name']?$v['member_name']:$v['member_username'];
            }
            $list['info']=$list;
            $list['status']=1;
            $list['showPage']=$show;
            $list['page']=isset($_GET['p'])?intval($_GET['p']):1;
            $list['sum']=$count;
            $list['firstRow']=$Page->firstRow==0?1:$Page->firstRow;
            $list['listRows']=$Page->listRows;
            $list['pagesum'] = ceil($count/$pageNum);
            $list['number']=$pageNum;
            $this->ajaxReturn($list);exit;
        }
    }

    //投票
    function wdyVote(){
        if(IS_AJAX){
            $Video=D('Video');
            $where['id']=I('id');
            if(!I('id')){
                return false;
            }
            if(I('down')){ //下载次数统计
                $Video->where($where)->setInc("down_num");exit;
            }

            if(!getUid()){
                $this->error('请先登陆',U('/member/login'));
            }
            $vote_log=M('vote_log');
            //赞同踏下
            if(I('updown')=='down' or I('updown')=='up'){
                if(getUid()){
                    $map['member_id']=getUid();
                }
                $map['ip']=get_client_ip();
                $map['vid']=I('id');
                $map['action']=array(array('eq','down'),array('eq','up'),'or');
                $rs= $vote_log->where($map)->select();
                if($rs){
                    $this->error('不能重复操作');
                }
                $data['vid']=I('id');
                $data['ip']=get_client_ip();
                $data['create_time']=time();
                $data['member_id']=getUid();
                $data['action']=I('updown');
                $vote_log->add($data);

                $updown=I('updown');
                $rs=$Video->where($where)->setInc("is_".$updown);
                $num=$Video->where($where)->getField('is_'.$updown);
            }else{
                //投票
                $this->error('投票已结束');//
                if(getUid()){
                    $map['member_id']=getUid();
                }
                $map['ip']=get_client_ip();
                $map['action']='vote';
                $rs= $vote_log->where($map)->select();
                if($rs){
                    $this->error('你已投票，不能重复操作');
                }

                $data['vid']=I('id');
                $data['ip']=get_client_ip();
                $data['create_time']=time();
                $data['member_id']=getUid();
                $data['action']='vote';
                $vote_log->add($data);

                $rs=$Video->where($where)->setInc('vote_num');
                $num=$Video->where($where)->getField('vote_num');
            }
            if($rs){
               $this->success($num);
            }else{
                $this->error('失败');
            }

        }

    }

	function wdyrule(){
        $this->display();
    }

	function wdyreview(){
        $this->display();
    }
	function wdyresult(){
        $this->display();
    }
    
    //微电影详情页
	function wdyvcr(){
        $Video=D('Video');
        $this->vo=$Video->find(I('id'));
        $where['id']=I('id');
        $Video->where($where)->setInc('view_num');
        $this->top_list=$Video->order('view_num desc')->limit(10)->select();

        $video_pl=D('VideoPl');
        import('ORG.Util.Page');// 导入分页类
        $map['vid']=I('id');
        $pageNum=20;
        $count      = $video_pl->where($map)->count();// 查询满足要求的总记录数
        $Page       = new Page($count,$pageNum,'','',1);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        $list=$video_pl->where($map)->relation(true)->order('create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($list as $k=>$v){
            $list[$k]['time']=date("Y-m-d H:i:s",$v['create_time']);
            $list[$k]['member_name']=$v['member_name']?$v['member_name']:$v['member_username'];
        }

        $pl['list']=$list;
        $pl['showPage']=$show;
        $pl['page']=isset($_GET['p'])?intval($_GET['p']):1;
        $pl['sum']=$count;
        $pl['firstRow']=$Page->firstRow==0?1:$Page->firstRow;
        $pl['listRows']=$Page->listRows;
        $pl['pagesum'] = ceil($count/$pageNum);
        $pl['number']=$pageNum;
        $this->pl=$pl;
        $this->display();
    }

}
