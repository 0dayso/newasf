<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-9-2
 * Time: 下午5:16
 * To change this template use File | Settings | File Templates.
 */
//需求模型 protected $_link = array(
class VideoPlModel extends RelationModel{
    protected $_link = array(
        'Member'=> array( //关联客服表
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'Member',
            'foreign_key'=>'member_id',
            'mapping_fields'=>'id,username,name',
            'as_fields'=>'name:member_name,username:member_username',
            // 定义更多的关联属性 relation(true)
        ),
    );

    protected $_auto = array (
        array('create_time','time',1,'function'),
        array('member_id','getUid',1,'function'),
    );






}