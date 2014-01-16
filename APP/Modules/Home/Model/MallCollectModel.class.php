<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-7-25
 * Time: 上午9:44
 * To change this template use File | Settings | File Templates.
 */
//购物车模型
class MallCollectModel extends RelationModel{
    protected $_link = array(
        'mall'=> array(
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'mall',
            'mall_id'=>'id',
            'mapping_fields'=>'id,title,jifen,img',
            'as_fields'=>"title,jifen,img",
            // 定义更多的关联属性 relation(true)
        ),
    );
    protected $_auto = array (
        array('create_time','time',1,'function'),
    );
    function addCollect(){
        $mall=M('mall');
        if($mall->field('id')->find(I('id'))){
            $where['mall_id']=I('id');
            $where['member_id']=getUid();
            $rs=$this->where($where)->find();
            if($rs){
                    return true;
             }else{
                $data['mall_id']=I('id');
                $data['member_id']=getUid();
                $data['create_time']=time();;
                if(!$this->create($data)){
                        exit($this->getError());
                }
                $rs=$this->add($data);
                if($rs){
                    return true;
                }
            }
        }
        return false;

    }

    function delCollect(){
        $where['id']=I('id');
        $where['member_id']=getUid();
        if ($this->where($where)->delete()) {
            return true;
        } else {
            return false;
        }
    }


}