<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-9-6
 * Time: 下午4:38
 * To change this template use File | Settings | File Templates.
 */
class ClaimPaymentViewModel extends ViewModel{

    public $viewFields = array(
        'ClaimPayment'=>array('*','_type'=>'LEFT'),//travelerinfos,booking_references
        'ClaimBooks'=>array('_as'=>'book','name'=>'book_name','status'=>'book_status','_on'=>'book.id=ClaimPayment.book_id','_type'=>'LEFT'),

        'User'=>array('name'=>'create_username','username'=>'create_username2', '_on'=>'ClaimPayment.create_uid=User.id','_type'=>'LEFT'),

        'User1'=>array('name'=>'payment_name','username'=>'payment_username', '_on'=>'ClaimPayment.payment_uid=User1.id','_table'=>"asf_User",'_type'=>'LEFT'),

        'User3'=>array('name'=>'review_name','username'=>'review_username', '_on'=>'ClaimPayment.review_uid=User3.id','_table'=>"asf_User",'_type'=>'LEFT'),


        'Department'=>array('name'=>'department_name','_on'=>'ClaimPayment.department_id=Department.id','_type'=>'LEFT'),

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