<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-9-2
 * Time: 下午5:16
 * To change this template use File | Settings | File Templates.
 */
//需求模型
class RequireOrderModel extends RelationModel{
    protected $_link = array(
        'member'=> array( //关联客服表
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'member',
            'foreign_key'=>'member_id',
            'mapping_fields'=>'id,username,name',
            'as_fields'=>'name:member_name,username:member_username',
            // 定义更多的关联属性 relation(true)
        ),
        'user'=> array( //关联客服表
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'user',
            'foreign_key'=>'user_id',
            'mapping_fields'=>'id,username,name',
            'as_fields'=>'name:user_name,username:user_username',
            // 定义更多的关联属性 relation(true)
        ),
    );

    protected $_auto = array (
        array('create_time','time',1,'function'),
        array('ip','get_client_ip',1,'function'),
        //HTTP_REFERER

        array('domain','get_domain',1,'callback'),
    );

    //自动完成  返回提交域名
    function get_domain(){
        return get_http_referer(1);
    }


    function getList($where,$limit=10,$order="create_time desc"){
            $data=$this->where($where)->order($order)->limit($limit)->select();
        return $data;
    }

    function insert($data){
        $data=$data?$data:$_POST;
        $map['phone']=I('phone');
        $map['create_time']=array('gt',time()-62);
        $rs=$this->where($map)->count();
        if($rs){
            return false;
        }
        $where['phone']=I('phone');
        $where['user_id']=array('neq',0);
        $where['status']=1;
        $rs=$this->where($where)->order('create_time desc')->getField('user_id');
        if($rs){
            $data['user_id']=$rs;
            $data['update_time']=time();
        }
        if(!$this->create($data)){
            return $this->getError();
        }
        if(!$this->source){
            $referer=get_http_referer();
            $this->source=$referer['url'];
        }

        if(getUid()){
            $this->member_id=getUid();
        }
        $rs=$this->add();
        echo  $this->getDbError();
        return $rs;
    }

    function update(){
        $data=$_POST;
        $rs=$this->save($data);
        return $rs;
    }

    //格式化
    function format($info){
        $typeArr=array('普通','学生','新移民','劳务');
        $gradeArr=array('经济舱','超经济舱','公务舱','头等舱');
        $statusArr=array('未查看','已查看');
        $uid=getUid();
        foreach($info as $k=>$v){
            $info[$k]['time']=date("Y-m-d H:i:s",$v['create_time']);
            $info[$k]['update_date']=date("Y-m-d H:i:s",$v['update_time']);
            $info[$k]['type']=$typeArr[$v['type']];
            $info[$k]['grade']=$gradeArr[$v['grade']];
            $info[$k]['status_name']=$statusArr[$v['status']];
            $user_name=$v['user_name']?$v['user_name']:$v['user_username'];
            $info[$k]['user_name']=$v['user_id']?$user_name:"";
            if($v['user_id']!=$uid){
               $pattern = "/(\d{3})(\d{4})(\d{4})/";
               $replacement = "\$1****\$3";
               $v['phone']=preg_replace($pattern, $replacement, $v['phone']);
            }
            $info[$k]['phone']=$v['phone'];
            $from_city=explode(',',$v['from_city']);
        //    dump($from_city);
            $to_city=explode(',',$v['to_city']);
            $origin_date=explode(',',$v['origin_date']);
            if(is_array($from_city) && $from_city[0] ){
                foreach($from_city as $kk=>$vv){
                    $info[$k]['route'][$kk]['from_city']=$vv;
                    $info[$k]['route'][$kk]['to_city']=$to_city[$kk];
                    $info[$k]['route'][$kk]['origin_date']=$origin_date[$kk];
                }
            }

        }
        return $info;
    }

    //需求单信息
    function getInfo($where){
        $info=$this->where($where)->relation(true)->select();
        $info=$this->format($info);
        return $info[0];
     }

      //设定客服 公司 对应的网站 条件
    function getMyWhere(){
        $rs=D('User')->field('department_id,company_id')->find(getUid());
        $site=$rs['company_id'];
        if($rs['department_id']==10){ //国际商旅部
            $site=0;
        }
        $domain=array(
            array('sl.aishangfei.com'), //国际商务部
            array('www.aishangfei.com','www.aishangfei.cn','www.aishangfei.net','m.aishangfei.com','www.ttfdp.com','www.shlc.ac.cn'),//广州
            array('www.hihivisa.com','sh.aishangfei.com'),//上海
            array('www.chuguow.com','www.hljiam.ac.cn','www.idb.ac.cn','bj.aishangfei.com'),//北京
            array('sz.aishangfei.com','www.aishangfei.com.cn','www.21fei.com','www.21cc.cn','www.19188.cn'),//深圳
        );
        $arr=array();
        foreach($domain as $val){
            foreach($val as $v){
                array_push($arr,$v);
            }
        }

        if($site==1){
            $domain1=implode("','",$domain[$site]);
            $domain2= implode("','",$arr);
            $where['_string'] = "(`domain` in ('$domain1')) OR ( `domain` not in ('$domain2'))";
        //    $where['domain']=array(array('in',implode("','",$domain[$site])),array('notin',implode("','",$arr)),'or');
        }else{
            $where['domain']=array('in',$domain[$site]);
        }
        return $where;
    }

    //获取需求单
    function ajaxList($limit){
        $where=$this->getMyWhere();
        $where['user_id']=array('LT',2);
        $data=$this->getList($where,$limit,'create_time');
        return  $data[0];
    }

    //获取需求单并保存
   function getNews($phone){
       $domain=$this->where("id=".$_GET['id'])->getField('domain');
       $MyWhere=$this->getMyWhere();
       $domainArr=$MyWhere['domain'][1];

       if(!empty($domainArr) && !in_array($domain,$domainArr)){
          return false; //非法请求
       }

       $map['phone']=$phone;
       $map['user_id']=array('lt',2); //同手机未被认领的
       $idArr=$this->field('id')->where($map)->select();
       if(empty($idArr)) return;
    //   print_r($idArr);
       $rs_num=count($idArr);
       foreach($idArr as $val ){
           $arr[]= $val['id'];
       }
       $where['id']=array('in',$arr);
       $data['user_id']=getUid();
       $data['update_time']=time();

       $rs= $this->where($where)->save($data);
       return $rs?$rs_num:false;
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