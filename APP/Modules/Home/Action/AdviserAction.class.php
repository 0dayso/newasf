<?php
// 专业顾问 
class AdviserAction extends IniAction {
    public function index(){
        $this->order=D("Booking")->nearOrder(6); //最近订单

        $company=D('Company');
        $this->companylist = $company->cache(true)->select();
        $this->evaluat=D('Evaluat')->latestEvaluat(4);//客户评价

        $company_id=I('company')?I('company'):1;//公司
        $where_department=I('department')?"and department_id=".I('department'):'';//部门

        $user=D('User');
        import('ORG.Util.Page');// 导入分页类
        $wh="view=1 and avatar!='' and qq!='' and status=1 $where_department"; //前台显示条件
        $where="$wh and company_id='$company_id'";
        if(I('search')){
            $search=I('search');
            $where ="$wh and (name like '%$search%' or public_mobile like '%$search%' or private_mobile like '%$search%' or qq like '%$search%')";
        }

        $count      = $user->cache(true)->where($where)->count();// 查询满足要求的总记录数
        $Page       = new Page($count,30);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询

        $this->assign('page',$show);// 赋值分页输出

        //ajax 请求返回json
        if(IS_AJAX){
            $this->userlist=$user->cache(true)->field('id,name,avatar,position_id,qq,email,telephone,public_mobile,status')->where($where)->select();
            $this->AjaxReturn($this->userlist);
        }

        $this->userlist=$user->cache(true)->field('id,name,avatar,position_id,qq,email,telephone,public_mobile,status')->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();

        $whereid="";
        foreach($this->userlist as $k=>$v){
            $whereid.=$v['id'].",";
        }
        $whereid=rtrim($whereid,',');
        $evaluat=D('Evaluat');

        $rs=$evaluat->cache(true)->field("user_id,count(user_id) count,sum(total) total ")->where("user_id in ($whereid)")->group("user_id")->select();

        foreach($rs as $v){
            $server[$v['user_id']]=round(($v['total']/$v['count']),1);
        }
        // print_r($rs);
        $this->assign('server',$server);
        $comp=I('company')?" - ".$this->companylist[I('company')-1]['name']:'';
        $p = I('p')?' - page' .I('p'):'';
        $this->title="专业顾问".$comp.$p;
        $this->display();
    }


    //ajax请求客服列表
    public function ajaxList(){
        $company=D('Company');
        $this->companylist = $company->select();

        $plnum=isset($_GET['plnum'])?$_GET['plnum']:4; //评论数量
        $this->evaluat=D('Evaluat')->latestEvaluat($plnum);
        $company_id=I('company')?I('company'):1;
        $user=D('User');
        import('ORG.Util.Page');// 导入分页类
        $where="company_id='$company_id' and department_id=5 and view=1 and avatar!='' and status=1";
        if(I('search')){
            $search=I('search');
            $where="department_id=5 and status=1 and avatar!='' and view=1 and(name like '%$search%' or public_mobile like '%$search%' or private_mobile like '%$search%' or qq like '%$search%')";
        }

        $pageRows=isset($_GET['pageRows'])?$_GET['pageRows']:30;
        $pageCount      = $user->where($where)->count();// 查询满足要求的总记录数
        $Page       = new Page($pageCount,$pageRows);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询

        $this->assign('page',$show);// 赋值分页输出

        $list=$user->cache(true)->field('id,name,avatar,position_id,qq,email,telephone,public_mobile,status')->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();

        $whereid="";
        foreach($list as $k=>$v){
            $whereid.=$v['id'].",";
        }
        $whereid=rtrim($whereid,',');
        $evaluat=D('Evaluat');

        $rs=$evaluat->cache(true)->field("user_id,count(user_id) count,sum(total) total ")->where("user_id in ($whereid)")->group("user_id")->select();

        foreach($list as $uk=>$uv){
            foreach($rs as $v){
                if($uv['id']==$v['user_id']){
                    $list[$uk]['server']=round(($v['total']/$v['count']),1);
                    $list[$uk]['serverImg']=round(($v['total']/$v['count'])*2);
                }
            }
            if(!$list[$uk]['server']){
                $list[$uk]['server']=5;
                $list[$uk]['serverImg']=10;
            }
        }
        $this->userlist=$list;
        $comp=I('company')?" - ".$this->companylist[I('company')-1]['name']:'';
        $p = I('p')?' - page' .I('p'):'';
        $this->title="专业顾问".$comp.$p;

        //ajax 请求返回json
        if(IS_AJAX || isset($_GET['callback'])){
            $totalPages= ceil($pageCount/$pageRows);
            if(I('p')>$totalPages){
                $this->error('已经是最后一页了');
            }

            if($this->userlist){
                $info['list']=$this->userlist;
                $info['status']=1;
                $info['p']=I('p')?intval(I('p')):1;
                $info['showPage']=$Page->show();
                $info['pl']=$this->evaluat;
            }else{
                $info['info']='获取失败';
                $info['status']=0;
            }
            $cb = $_GET['callback'];
            echo $cb."({code:".json_encode( $info)."})";
        }

    }


    function review(){ //（不通过顾问直接订购的评价)
        $this->display();

    }

    //提交评价
    function reviewUser(){ /// adviser/XXX/review （XXX为顾问编号）
        if($_POST){
            $rs=D('Evaluat')->addEvaluat();
            if($rs){
                $this->success('提交成功！');
            }else{
                $this->error('提交成功！');
            }
        }
        $user=D('User');
        $userid=isset($_GET['id'])?$_GET['id']:$_POST['post.id'];
        if($userid){
            $rs=$user->where("id=$userid and position_id=14")->find();
            if(!$rs){
                header("HTTP/1.0 404 Not Found");//使HTTP返回404状态码
                $this->title="404 错误";
                $this->display("Public:404");
            }
            $this->user=$rs;
            //  dump($rs);
            $this->success("您还没有成功的订单不能评价 !",'',3);
            $this->title="我要评价";
            $this->display();
        }

    }

    //客服评价页
    function reviewList(){
        $this->order=D("Booking")->nearOrder(10); //最近订单
        $user=D('User');
        $user_id=I('id');
        if($user_id){
            $urs=$user->find($user_id);
            if(!$urs){
                header("HTTP/1.0 404 Not Found");//使HTTP返回404状态码
                $this->title="404 错误";
                $this->display("Public:404");
            }
        }
        $this->user=$urs;
        $evaluat=D('Evaluat');
        import('ORG.Util.Page');// 导入分页类

        $where=array();
        $where['user_id']=$user_id;
        $where['create_time']=array('ELT',strtotime(date("Y-m-d H:s",strtotime('-1 day'))));
        $server= $evaluat->cache(true)->where($where)->sum('total');

        $wheres['total']=array("EGT","4");$wheres=array_merge($where,$wheres);
        $pl['hao']= $evaluat->cache(true)->where($wheres)->count();
        $wheres['total']=array("EQ","3");$wheres=array_merge($where,$wheres);
        $pl['zhong']= $evaluat->cache(true)->where($wheres)->count();
        $wheres['total']=array("LT","3");$wheres=array_merge($where,$wheres);
        $pl['cha']= $evaluat->cache(true)->where($wheres)->count();

        $count      = $evaluat->cache(true)->where($where)->count();// 查询满足要求的总记录数
        $this->server=round(($server/$count),1);

        $this->assign('pl',$pl);
        $Page       = new Page($count,12);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->list=$evaluat->getList($where,$Page->firstRow.','.$Page->listRows);

        $this->title="$urs[name] - 客服评价";

        //ajax 请求返回json
        if(IS_AJAX || isset($_GET['callback'])){
            if($this->list){
                $info['list']=$this->list;
                $info['status']=1;
                $info['p']=I('p')?intval(I('p')):1;
                $info['showPage']=$Page->show();
            }else{
                $info['info']='获取失败';
                $info['status']=0;
            }
            $cb = $_GET['callback'];
            echo $cb."({code:".json_encode( $info)."})";
            exit;
        }
        $this->display();
    }

}