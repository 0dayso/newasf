<?php
/**
 * This is the model class for table "{{booking}}".
 *
 * The followings are the available columns in table '{{booking}':
 * @property string $id
 * @property string $order_id
 * @property string $order_datetime
 * @property string $price_detail
 * @property string $account
 * @property string $email
 * @property string $cell
 * @property string $travelerinfos
 * @property string $booking_references
 * @property integer $user_id
 * @property string $update_time
 * @property integer $order_status
 *
 * The followings are the available model relations:
 * @property User $user
 */

//订单视图模型
class ClaimDataViewModel extends ViewModel{
    public $viewFields = array(
        'ClaimData'=>array('*','_type'=>'LEFT'),//travelerinfos,booking_references
        'claimBooks'=>array('_as'=>'book','name'=>'book_name','status'=>'book_status','_on'=>'book.id=ClaimData.book_id','_type'=>'LEFT'),
        'User'=>array('name'=>'create_username','username'=>'create_username2', '_on'=>'ClaimData.create_uid=User.id','_type'=>'LEFT'),
        'User1'=>array('name'=>'claim_username','username'=>'claim_username2', '_on'=>'ClaimData.claim_uid=User1.id','_table'=>"asf_User",'_type'=>'LEFT'),
        'User3'=>array('name'=>'update_username','username'=>'update_username2', '_on'=>'ClaimData.update_uid=User3.id','_table'=>"asf_User",'_type'=>'LEFT'),
        'User4'=>array('name'=>'audit_username','username'=>'audit_username2', '_on'=>'ClaimData.audit_uid=User4.id','_table'=>"asf_User",'_type'=>'LEFT'),
        'Department'=>array('name'=>'department','_on'=>'ClaimData.department_id=department.id','_type'=>'LEFT'),
        'Department2'=>array('name'=>'user_department','_on'=>'User1.department_id=Department2.id','_table'=>"asf_Department",'_type'=>'LEFT'),

    );



}