<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-9-6
 * Time: 下午4:38
 * To change this template use File | Settings | File Templates.
 */
class ClaimDataModel extends RelationModel{
    protected $_link = array(
        'user'=> array( //认领人
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'user',
            'foreign_key'=>'claim_uid',
            'mapping_fields'=>'id,username,name',
            'as_fields'=>'name:claim_username',
            // 定义更多的关联属性 relation(true)
        ),

        'user2'=> array( //创建人
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'user',
            'foreign_key'=>'create_uid',
            'mapping_fields'=>'id,username,name',
            'as_fields'=>'name:create_name',
            // 定义更多的关联属性 relation(true)
        ),
        'user3'=> array( //审核人
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'user',
            'foreign_key'=>'audit_uid',
            'mapping_fields'=>'id,username,name',
            'as_fields'=>'name:audit_username',
            // 定义更多的关联属性 relation(true)
        ),

    );


    public function attributeLabels(){
        return array(
            'id'=>'ID',
            'book_id'=>'帐本ID',
            'remitter'	=>'汇款人',
            'arrival_amount'=>'到账金额',
            'poundage'=>'手续费',
            'arrival_date'=>'到账日期',
            'bank'	=>'银行',
            'audit_uid'=>'审核人',
            'audit_remark'=>'备注',
            'create_uid'=>'创建人',
            'claim_name'=>'认领人',
            'department_id'=>'职位',
            'create_time'	=>'创建时间',
            'update_uid'=>'更新人ID',
            'update_time'=>'更新时间',
            'type'	=>'类型',
            'claim_uid'=>'认领人ID',
            'claim_time'=>'认领时间',
            'status'=>'状态',
            'order_id'	=>'订单号',
            'claim_remitter'=>'汇款人',
            'ticket_date'=>'出票日期',
            'claim_remark'=>'备注',
            'edit_order_id'=>'更正订单号',
            'edit_claim_remitter'=>'更正汇款人',
            'edit_ticket_date'=>'更正出票日期',
            'edit_claim_remark'=>'更正备注',
            'claim_username'=>'认领人',
            'create_name'=>'创建人',
            'audit_username'=>'审核人',
        );
    }
}