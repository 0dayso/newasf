<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-8-15
 * Time: 下午1:16
 * To change this template use File | Settings | File Templates.
 */

class AdminMemberModel extends RelationModel{
    protected $tableName = 'member';
    protected $_link = array(
        'user'=> array(  //关联客服表
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'user',
            'user_id'=>'id',
            'mapping_fields'=>'id,username,name,profile,avatar,signature,telephone,public_mobile,private_mobile,email,qq,good_review,ordinary_review,bad_review,status',
            // 定义更多的关联属性 relation(true)
        ),

        'ranks'=> array( //关联用户组表
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'member_rank',
            'rank_id'=>'id',
            'mapping_fields'=>'id,name',
        ),
    );


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
           if(!$this->create()){
               return array('status' => 0, 'info' => $this->geterror());
           }

            $wheres['id']=I('id');
           $rs = $this->where($wheres)->relation(true)->save();
        if($rs){
            return array('status' => 1, 'info' => "成功");
        }else{
            return array('status' => 0, 'info' => '数据未改变');
        }
        }
    }


}