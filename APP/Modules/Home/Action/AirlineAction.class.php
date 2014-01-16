<?php
class AirlineAction extends IniAction{

    Public function index(){

    }

    function pl(){
        if(isset($_GET['test'])){
            print_r($_GET);
        }
        if(!I("from") || !I("to")){
            $this->error("URL 输入有误！");
        }

        $evaluat=D('Evaluat');
        $where['from_iata']=I("from");
        $where['to_iata']=I("to");
        $where['create_time']=array('ELT',time());
     //   $where="";
        $city['from']=D("City")->getCityName(I("from"));
        $city['to']=D("City")->getCityName(I("to"));

        $_GET['origin_name']=$city['from']."(".I("from").")";
        $_GET['desination_name']=$city['to']."(".I("to").")";
        $_GET['originDate']=date('Y-m-d',strtotime('+5 day'));
        $_GET['returnDate']=date('Y-m-d',strtotime('+10 day'));

        $this->city=$city;
        import('ORG.Util.Page');// 导入分页类

        $count      = $evaluat->where($where)->count();// 查询满足要求的总记录数
        $this->count=$count;
        $Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出

        // 进行分页数据查询
        $wheres['total']=array("EGT","4");$wheres=array_merge($where,$wheres);
        $pl['hao']= $evaluat->where($wheres)->count();
        $wheres['total']=array("EQ","3");$wheres=array_merge($where,$wheres);
        $pl['zhong']= $evaluat->where($wheres)->count();
        $wheres['total']=array("LT","3");$wheres=array_merge($where,$wheres);
        $pl['cha']= $evaluat->where($wheres)->count();
        $this->pl=$pl;
        $server= $evaluat->where($where)->sum('total');
        $this->server=round(($server/$count),1);
        $speed= $evaluat->where($where)->sum('speed');
        $this->speed=round(($speed/$count),1);
        $price= $evaluat->where($where)->sum('price');
        $this->price=round(($price/$count),1);

        $this->assign('page',$show);// 赋值分页输出
        $this->list=$evaluat->getList($where,$Page->firstRow.','.$Page->listRows);

        $user=D('User');
        $this->kf_list=$user->where("department_id=5 and status=1 and public_mobile!='' and view=1 and avatar!=''")->order('rand()')->limit('8')->select();

        foreach($this->kf_list as $k=>$v){
            $id=$v['id'];
            $count  = $evaluat->where("user_id=$id")->count();// 查询满足要求的总记录数
            $servers= $evaluat->where("user_id=$id")->sum('total');
            $server[$id]=round(($servers/$count),1);
        }
        $this->assign('userver',$server);

        $this->title="$city[from]至$city[to] - 国际航线客服评价";

        $this->display();
    }



}