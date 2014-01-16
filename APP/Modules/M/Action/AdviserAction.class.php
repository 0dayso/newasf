<?php
// 专业旅行顾问控制器
class AdviserAction extends IniAction {
	
    public function index(){
        $company=D('Company');
        $this->companylist = $company->select();

        $this->evaluat=D('Evaluat')->latestEvaluat(4);
        $company_id=I('company')?I('company'):1;
        $user=D('User');
        import('ORG.Util.Page');// 导入分页类
        $where="company_id='$company_id'  and view=1 and avatar!='' and status=1";
        if(I('search')){
            $search=I('search');
            $where="status=1 and avatar!='' and view=1 and(name like '%$search%' or public_mobile like '%$search%' or private_mobile like '%$search%' or qq like '%$search%')";
        }

        $pageRows=5;
        $pageCount      = $user->where($where)->count();// 查询满足要求的总记录数
        $Page       = new Page($pageCount,$pageRows);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询

        $this->assign('page',$show);// 赋值分页输出

        $list=$user->field('id,name,avatar,position_id,qq,email,telephone,public_mobile,status')->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();

        $whereid="";
        foreach($list as $k=>$v){
            $whereid.=$v['id'].",";
        }
        $whereid=rtrim($whereid,',');
        $evaluat=D('Evaluat');

        $rs=$evaluat->field("user_id,count(user_id) count,sum(total) total ")->where("user_id in ($whereid)")->group("user_id")->select();

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
        if(IS_AJAX){
            $totalPages= ceil($pageCount/$pageRows);
            if(I('p')>$totalPages){
                $this->error('已经是最后一页了');
            }
            if($this->userlist){
                $info['list']=$this->userlist;
                $info['status']=1;
                $info['p']=I('p')?intval(I('p')):1;
                $info['showPage']=$Page->show();
            }else{
                $info['info']='获取失败';
                $info['status']=0;
            }
            $this->AjaxReturn($info);exit;
        }
        $this->display();

    //    $this->title="专业旅行顾问";
     //   $this->display();
    }
	
	public function review(){
        $user=D('User');
        $user_id=I('id');
        if($user_id){
            $where['id']=$user_id;
            $urs=$user->where($where)->find();
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

        $where['create_time']=array('ELT',time());
        $server= $evaluat->where($where)->sum('total');
        $wheres['total']=array("EGT","4");$wheres=array_merge($where,$wheres);
        $pl['hao']= $evaluat->where($wheres)->count();
        $wheres['total']=array("EQ","3");$wheres=array_merge($where,$wheres);
        $pl['zhong']= $evaluat->where($wheres)->count();
        $wheres['total']=array("LT","3");$wheres=array_merge($where,$wheres);
        $pl['cha']= $evaluat->where($wheres)->count();

        $count      = $evaluat->where($where)->count();// 查询满足要求的总记录数
        $this->server=round(($server/$count),1);

        $this->assign('pl',$pl);
        $Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->list=$evaluat->getList($where,$Page->firstRow.','.$Page->listRows);
        $this->title="专业旅行顾问";

        //ajax 请求返回json
        if(IS_AJAX){
            if($this->list){
                $info['list']=$this->list;
                $info['status']=1;
                $info['p']=I('p')?intval(I('p')):1;
                $info['showPage']=$Page->show();
            }else{
                $info['info']='获取失败';
                $info['status']=0;
            }
            $this->AjaxReturn($info);exit;
        }
        $this->display();
    }
	
	public function form(){
        if($_POST){
            $rs=D('Evaluat')->addEvaluat();
            if($rs){
                $this->success('提交成功！');
            }else{
                $this->error('提交成功！');
            }
        }
        if(I('id')){
            $user=D('User');
            $rs=$user->find(I('id'));
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
        }else{
            $this->error('您还没有登陆，请先登陆');
        }
        $this->title="专业旅行顾问";
        $this->display();
    }
	
	
}