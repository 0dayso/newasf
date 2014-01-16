<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-9-2
 * Time: 下午5:16
 * To change this template use File | Settings | File Templates.
 */
//需求模型
class VideoModel extends RelationModel{
/*    protected $_link = array(
       'user'=> array( //关联客服表
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'user',
            'foreign_key'=>'create_id',
            'mapping_fields'=>'id,username,name',
            'as_fields'=>'name:user_name,username:user_username',
            // 定义更多的关联属性 relation(true)
        ),
        'vote_log'=> array( //关联客服表
            'mapping_type'=>HAS_MANY ,
            'class_name'=>'vote_log',
            'foreign_key'=>'vid',
            'parent_key'=>'id',
            'mapping_fields'=>'id,vid,action',
         //   'as_fields'=>'name:user_name,username:user_username',
            // 定义更多的关联属性 relation(true)
        ),
    );*/

    protected $_auto = array (
        array('create_time','time',1,'function'),
        array('create_uid','getUid',1,'function'),
    );






}