<?php

class ActionLogModel extends RelationModel {
    protected $_link = array(
        'action'=> array( //关联会员表
            'mapping_type'=>BELONGS_TO ,
            'class_name'=>'action',
            'foreign_key'=>'action_id',
            'mapping_fields'=>'id,title,name,status',
            // 定义更多的关联属性 relation(true)
        ),
    );


}

?>
