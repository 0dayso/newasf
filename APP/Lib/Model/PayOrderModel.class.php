<?php
/*
 * 支付模型
 */
class PayOrderModel extends RelationModel{
    protected $_link = array(
        'member'=> array(//关联用户表
            'mapping_type'=>BELONGS_TO ,
            'class_name'=>'member',
            'foreign_key'=>'member_id',
             'mapping_fields'=>'id,username,name,mobile',
            // 定义更多的关联属性 relation(true)
        ),
    );

    protected $_auto = array (
        array('member_id','getUid','','function'),
        array('create_time','time','','function'),
        array('update_time','time',2,'function'),
    );

    public $statusArr=array('未支付','已支付');

    /*
     * 更新
     */
    function update($data){
        if(!$this->create($data)){
            return false;
        }
        $rs= $this->save();
        return $rs;
    }

    /*
     * 格式化
     */
    function format($data){
        foreach($data as $key=>$val){
            $data[$key]['status_name']=$this->statusArr[$val['status']];
            $data[$key]['create_time']=date('Y-m-d H:i:s',$val['create_time']);
            $data[$key]['update_time']=date('Y-m-d H:i:s',$val['update_time']);
        }
        return $data;
    }


}