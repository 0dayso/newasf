<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-7-25
 * Time: 上午9:44
 * To change this template use File | Settings | File Templates.
 */
//收藏模型
class MallExchangeModel extends RelationModel{
    protected $_link = array(
        'member'=> array(
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'member',
            'mall_id'=>'member_id',
            'mapping_fields'=>'id,name,username,mobile',
            // 定义更多的关联属性 relation(true)
        ),
    );
    protected $_auto = array (
        array('create_time','time',1,'function'),
    );

    //状态
    Public  $statusArr=array('未发货','已发货','已完成');

    /*
    * 格式化
    */
    function format($data){
        if(!$data || !is_array($data)) return false;
        if(isset($data[0])){
            foreach($data as $key=>$val){
                $data[$key]['status_name']=$this->statusArr[$val['status']];
                $data[$key]['create_date']=date('Y-m-d H:i:s',$val['create_time']);
                $data[$key]['update_date']=date('Y-m-d H:i:s',$val['update_time']);
            }
            return $data;
        }else{
                $data['status_name']=$this->statusArr[$data['status']];
                $data['create_date']=date('Y-m-d H:i:s',$data['create_time']);
                $data['update_date']=date('Y-m-d H:i:s',$data['update_time']);
            return $data;
        }

    }



}