<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-9-6
 * Time: 下午4:38
 * To change this template use File | Settings | File Templates.
 */
class ClaimPaymentModel extends RelationModel{
    protected $_link = array(
        'user'=> array(  //关联用户表
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'user',
            'foreign_key'=>'create_uid',
            'mapping_fields'=>'id,username,name,public_mobile,private_mobile,status',
            'as_fields'=>'name:create_username',
            // 定义更多的关联属性 relation(true)
        ),
        'payment'=> array(  //关联用户表
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'user',
            'foreign_key'=>'payment_uid',
            'mapping_fields'=>'id,username,name',
            'as_fields'=>'name:payment_name',
        ),
        'review'=> array(  //关联用户表
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'user',
            'foreign_key'=>'review_uid',
            'mapping_fields'=>'id,username,name',
            'as_fields'=>'name:review_username',
        ),
        'department'=> array(//关联公司表
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'department',
            'foreign_key'=>'department_id',
            //    'parent_key'=>'book_id',
            'mapping_fields'=>'id,name',
            'as_fields'=>'name:department_name',
        ),
    );

    protected $_auto = array(
        array('create_time','time',1,'function'),
        array('update_time','time',2,'function'),
        array('create_uid','getUid',1,'function'),
    );

    //格式化数据
    function format($info){
        $statusArr=array('未处理','处理中','待复核','已完成','复核未通过');
        $typeArr=array('','转帐汇款','网上支付','现金支付');
        foreach($info as $k=>$v){
            $info[$k]['status_name']=$statusArr[$v['status']];
            $info[$k]['type_name']=$typeArr[$v['type']];
        }
        return $info;
    }
}