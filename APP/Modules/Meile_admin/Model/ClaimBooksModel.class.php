<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-9-6
 * Time: 下午4:38
 * To change this template use File | Settings | File Templates.
 */
class ClaimBooksModel extends RelationModel{
    protected $_link = array(
        'user'=> array(  //关联客服表
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'user',
            'foreign_key'=>'user_id',
            'mapping_fields'=>'id,username,name,public_mobile,private_mobile,status',
            'as_fields'=>'name:username',
            // 定义更多的关联属性 relation(true)
        ),
        'company'=> array( //关联公司表
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'company',
            'foreign_key'=>'company_id',
            //    'parent_key'=>'book_id',
            'mapping_fields'=>'id,name',
            'mapping_order'=>'status desc,claim_time desc',
        ),
        'record'=> array(  //关联
            'mapping_type'=>HAS_MANY,
            'class_name'=>'claim_data',
            'foreign_key'=>'book_id',
        //    'parent_key'=>'book_id',
            'condition'=>'status != 2 ',
            'mapping_fields'=>'id,book_id,bank,status,claim_time',
            'mapping_order'=>'status desc,claim_time desc',
            'mapping_limit'=>1,
            // 定义更多的关联属性 relation(true)
        ),
    );
}