<?php
class EvaluatModel extends RelationModel{
    protected $_link = array(
        'user'=> array(
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'user',
            'user_id'=>'id',
     //       'mapping_fields'=>'id,username,name,avatar,status',
            'as_fields'=>'name:user_name',
            // 定义更多的关联属性
        ),
        'member'=> array(
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'member',
            'member_id'=>'id',
         //   'mapping_fields'=>'id,username,name',
            'as_fields'=>'name:member_name,username:member_username',
        ),
    );

    protected $_validate = array(

	 );

    protected $_auto = array (
        array('member_id','setMemberId',1,'callback '),
        array('user_id','setUserId',1,'callback '),
        array('create_time','time',1,'function'),
    );


    function setMemberId(){
        if(session('uid')){
            return (session('uid'));
        }
    }

    function setUserId(){
        if(I('get.id')){
            return (I('get.id'));
        }
    }

    //添加评论
    function addEvaluat(){
       if($this->create()){
           $this->contents=I('post.contents');
           $rs=$this->add();
           if($rs){
               return $rs;
           }else{
               return false;
           }
       }else{
           return false;
       }
    }
        //
    function latestEvaluat($num){
        if(!S('latest_evaluat_'.$num)){
            $where['status']=1;
            $where['create_time']=array('ELT',strtotime(date("Y-m-d H:s",strtotime('-1 day'))));
            $evaluat=$this->where($where)->order("total desc,rand()")->limit($num)->select();//客户评价
            S('latest_evaluat_'.$num,$evaluat,60);
        }else{
            $evaluat=S('latest_evaluat_'.$num); //读取缓存
        }
        return $evaluat;
    }

    //获取列表
    function getList($where,$limit=10,$order='create_time desc'){
        $where['status']=1;
        $where['create_time']=array('ELT',strtotime(date("Y-m-d H:s",strtotime('-1 day'))));
        $rs= $this->cache(true)->where($where)->relation(false)->order($order)->limit($limit)->select();
        foreach($rs as $k=>$v){
            $rs[$k]['time']=date("Y-m-d",$v['create_time']);
        }
        return $rs;
    }

    function getInfo(){
        $rs=$this->relation(true)->find(I('id'));
        $rs['create_time']= date("Y-m-d",$rs['create_time']);
        return $rs;

    }
    //编辑
    function edit(){
        if(IS_POST){
            if(I('user_name')){
                $where['name']=I('user_name');
                $where['status']=1;
                $rs=D('User')->where($where)->getField('id');
                if($rs)
                    $_POST['user_id']=$rs;
                else
                    return array('status' => 0, 'info' =>I('user_name').'未找到 请检查');
            }

            $_POST['create_time']=strtotime($_POST['create_time']);
            if(!$this->create()){
                return array('status' => 0, 'info' => $this->geterror());
            }

            $wheres['id']=I('id');
            $rs = $this->where($wheres)->save();
            if($rs){
                return array('status' => 1, 'info' => "成功");
            }else{
                return array('status' => 0, 'info' => '数据未改变');
            }
        }
    }

}

?>