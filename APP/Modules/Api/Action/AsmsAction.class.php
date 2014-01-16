<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 13-11-4
 * Time: 下午5:31
 */
class AsmsAction extends IniAction{
       function searchTicket(){
           $data=array('127.0.0.1','','CAN','2013-12-06','HKG','0','6000'
           );
           $rs=D('Asms')->searchTicket($data);

          $xml=simplexml_load_string($rs);
           $arr=$xml->DataList->Flight;

           foreach($arr as $v){
                print_R($v);
           }
       }

    function index(){
        $data=array('127.0.0.1','',I('from'),I('date'),I('to'),I('clerk'),'6000'
        );
        $rs=D('Asms')->searchTicket($data);

        $xml=simplexml_load_string($rs);
        $arr=$xml;
        $js=json_encode($xml);
        print_r(json_decode($js));
    //    echo $js;
     //   print_r($xml);



        foreach($arr as $v){
         //   print_R($v);
        }
    }

    function region(){
       $data['email']='5828759@qq.com';
        $data['identificationNumbers']="11111111111111131112";
        $data['name']='王欣2';
        $data['password']=11111;
        $data['phone']='13800000055';
        $data['username']='test_wangxin';
        $data['sex']='M';
        $data['clerk']='6000';
        $Asms=D('AsmsMember');
     //   $Asms->send_url='http://127.0.0.1/newasf/tests/shengyi/B2CMember.php?wsdl';
        $rs=$Asms->registration($data);
        print_R( $rs);
    }

    function login(){
        $data['memberNumber']='MAYANQING10701';
    //    $data['email']=11111;
     //   $data['phone']='13800000055';
        $data['password']="123456";
     //   $data['username']='test_wangxin';
        $Asms=D('AsmsMember');
           $Asms->send_url='http://127.0.0.1/newasf/tests/shengyi/B2CMember.php?wsdl';
        $rs=$Asms->login($data);
        print_R( $rs);

    }


}