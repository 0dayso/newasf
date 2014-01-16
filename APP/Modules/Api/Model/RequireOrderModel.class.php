<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-9-2
 * Time: 下午5:16
 * To change this template use File | Settings | File Templates.
 */
//需求模型
class RequireOrderModel extends Model{

    function getList($where,$limit=10){
        $data=$this->where($where)->order('create_time desc')->limit($limit)->select();
        $typeArr=array('快捷需求单','单程','往返','多程');
        foreach($data as $k=>$v){
            $data[$k]['time']=date("Y-m-d H:i:s",$v['create_time']);
            $data[$k]['type']=$typeArr[$v['type']];
        }
        return $data;
    }

    function insert(){
        if(!$this->create()){
            return $this->getError();
        }
        if(getUid()){
            $this->member_id=getUid();
        }
        $this->create_time=time();
        $rs=$this->add();
        return $rs;
    }

    function update(){
        $data=$_POST;
        $rs=$this->save($data);
        return $rs;
    }

    //需求单信息
    function getInfo($where=array()){
         $where['id']=I('id');
         $info= $this->getlist($where,1);
         return $info[0];
     }

    function ajaxList(){
        $where['is_view']=0;
        return  $this->getList($where);
    }

    //需求单信息  点击后别人不能查看
    function ajaxInfo(){
        $where['is_view']=0;
        $where['session'] = session_id();
        $where['_logic'] = 'or';
        $map['_complex'] = $where;
        $map['id']=I('id');
        $info= $this->getlist($map,1);
        if($info){
        $data['id']=I('id');
        $data['is_view']=1;
        $data['session'] =session_id();
        $this->save($data);
        }
        return $info;
    }


}