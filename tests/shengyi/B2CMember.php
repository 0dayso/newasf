<?php
require 'common/common.php';
header("Pragma: no-cache");
//soap
/*$server = new soap_server();
$server->configureWSDL('WebService','urn:wsdl');

/**********注册函数***************/
/*
$input_arr=array('in0' => 'xsd:string','in1' => 'xsd:string','in2' => 'xsd:string');// input parameters
$output=array('return' => 'xsd:string'); // output parameters

$server->register('registration',$input_arr,$output);// method name
$server->register('login',$input_arr,$output);// method name

$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : ''; 
$server->service($HTTP_RAW_POST_DATA);
*/

//login();

/**
 * repeatUsername()  会员注册名重复验证
 *
 *
 *
 */

/**
登陆
*/
function login(){
    $data=member_find('','','1');
    if(1){
        $str=<<<xml
<?xml version="1.0" encoding="UTF-8"?>
    <response>
    <abbreviation>$data[abbreviation]</abbreviation>
    <address>$data[abbreviation]</address>
    <answer>$data[abbreviation]</answer>
    <areOpen>1</areOpen>
    <availablePoints>$data[abbreviation]</availablePoints>
    <companyAddress>$data[abbreviation]</companyAddress>
    <companyFax>$data[abbreviation]</companyFax>
    <companyPhone>$data[abbreviation]</companyPhone>
    <dateOfBirth>$data[abbreviation]</dateOfBirth>
    <documentType>$data[abbreviation]</documentType>
    <earnPoints>$data[abbreviation]</earnPoints>
    <email>$data[abbreviation]</email>
    <fax>$data[abbreviation]</fax>
    <gender>$data[abbreviation]</gender>
    <homePhone>$data[abbreviation]</homePhone>
    <identificationNumbers>$data[abbreviation]</identificationNumbers>
    <interests>$data[abbreviation]</interests>
    <memberId>$data[abbreviation]</memberId>
    <memberLevels>$data[abbreviation]</memberLevels>
    <memberNumber>$data[abbreviation]</memberNumber>
    <memberSource>$data[abbreviation]</memberSource>
    <memberType>$data[abbreviation]</memberType>
    <name>$data[abbreviation]</name>
    <password>$data[abbreviation]</password>
    <phone>$data[abbreviation]</phone>
    <post>$data[abbreviation]</post>
    <question>$data[abbreviation]</question>
    <registrationName>$data[abbreviation]</registrationName>
    <respectiveDistricts>$data[abbreviation]</respectiveDistricts>
    <respectiveProvinces>$data[abbreviation]</respectiveProvinces>
    <resultCode>1</resultCode>
    <totalSpendPoints>$data[abbreviation]</totalSpendPoints>
    <userSignature>$data[abbreviation]</userSignature>
    <website></website>
    <workUnit></workUnit>
    <zipCode></zipCode>
</response>
xml;
    }else{
    $str=<<<xml
<?xml version="1.0" encoding="UTF-8"?>
<response>
    <message>帐号或密码输入有误，请检查</message>
    <resultCode>301</resultCode>
</response>
xml;
    }
    return $str;
}

function get_user_info($memberId=''){
    if(!$memberId)
        $memberId=isset($_GET['uid'])?$_GET['uid']:'131108095311405654';
    if(!$memberId)
        return false;

        $url=C('ASMS_HOST')."/asms/member/t_member_jfcx_info.shtml?action=edit&hyid=$memberId&hylx=P";
    $data=curl_post($url,'',COOKIE_FILE,0);
    echo $data;

    //正则匹配
    $preg="/<td width=\"32%\"><input name=\"textfield\" type=\"text\" class=\"hy_input\" style=\"width:120px;\" value=\"(.*?)\"><\/td>
            <td width=\"18%\">会员卡号:<\/td>
            <td width=\"32%\"><input name=\"textfield2\" type=\"text\" class=\"hy_input\" style=\"width:120px;\" value=\"(.*?)\"><\/td>
          	.*?<input name=\"textfield3\" type=\"text\" class=\"hy_input\" style=\"width:120px;\" value=\"(.*?)\">
	            <\/td>
            <td>会员来源:<\/td>
            <td>(.*?)
			  <\/td>
          <\/tr>
          <tr>
            <td>会员姓名:<\/td>
            <td><input name=\"textfield4\" type=\"text\" class=\"hy_input\" style=\"width:120px;\" value=\"(.*?)\"><\/td>
            <td>性　　别:<\/td>
            <td>
              <input name=\"textfield11\" type=\"text\" class=\"hy_input\" style=\"width:120px;\" value=\"(.*?)\"><\/td>
          <\/tr>
          <tr>
            <td>出生日期:<\/td>
            <td><input name=\"textfield5\" type=\"text\" class=\"hy_input\" style=\"width:120px;\" value=\"(.*?)\"><\/td>
            <td>国家\/地区:<\/td>
            <td><input name=\"textfield10\" type=\"text\" class=\"hy_input\" style=\"width:120px;\" value=\"(.*?)\"><\/td>
          <\/tr>
          <tr>
            <td>证件类型:<\/td>
            <td>
			 .*? value=\"(.*?)\".*?<\/td>
            <td>证件号码:<\/td>
            <td><input name=\"textfield9\" .*? value=\"(.*?)\"><\/td>
          .*?
            <td colspan=\"3\">([\d\D]*)?<\/td>[\d\D]*<td>创建人:<\/td>[\d\D]*<td colspan=\"3\">([\d\D]*?)?<\/td>[\d\D]*<\/table>/sui";


    preg_match($preg,$data,$info);
 //   print_R($info);
    if(count($info)>1){
        $arr['registrationName']=$info[1];
        $arr['memberNumber']=$info[2];
        $arr['memberLevels']=$info[3];
        $arr['memberSource']=$info[4];
        $arr['name']=$info[5];
        $arr['gender']=$info[6];
        $arr['dateOfBirth']=$info[7];
        $arr['documentType']=$info[8];
        $arr['identificationNumbers']=$info[9];
        $arr['dateOfBirth']=$info[10];
        $arr['dateOfBirth']=$info[11];
        $arr['dateOfBirth']=$info[12];
    }
   // print_r($arr);
 //   print_r($info);

}
//get_user_info('131211092837338132');

function mm($data){
    $xml=<<<xml
<?xml version="1.0" encoding="UTF-8"?>
<response>
    <abbreviation>$data[mx]</abbreviation>
    <address>$data[lxdz]</address>
    <answer>223344(问题答案)</answer>
    <areOpen>1(是否公开 1公开 0不公开 默认为1)</areOpen>
    <availablePoints>1250(可用积分)</availablePoints>
    <companyAddress>2(公司地址)</companyAddress>
    <companyFax>2(公司传真)</companyFax>
    <companyPhone>2(公司电话)</companyPhone>
    <dateOfBirth>1989-09-12(出生日期)</dateOfBirth>
    <documentType>NI(证件类型)</documentType>
    <earnPoints>1590(累积积分)</earnPoints>
    <email>yymusical@qq.com(EMAIL)</email>
    <fax>(传真)</fax>
    <gender>M(性别 M男 F女)</gender>
    <homePhone>0710-2222222(家庭电话)</homePhone>
    <identificationNumbers>420683198909120337(证件号码)</identificationNumbers>
    <interests>(兴趣爱好)</interests>
    <memberId>110507150326161026(会员ID)</memberId>
    <memberLevels>1007203(会员等级)</memberLevels>
    <memberNumber>MUSIC(会员卡号)</memberNumber>
    <memberSource>1(会员来源 1网上注册 2后台管理人员创建 3呼叫中心创建 4自动导入 5其他方式)</memberSource>
    <memberType>P(会员类型 P代表个人会员 E代表企业会员)</memberType>
    <name>zhangyue(姓名)</name>
    <phone>18607175122(手机)</phone>
    <post>2(职务 会员手工录入)</post>
    <question>您小学班主任的名字是(密码问题)</question>
<registrationName>music(会员注册名)</registrationName>
<password></password>
    <respectiveDistricts>273(所属地区 会员手工录)</respectiveDistricts>
    <respectiveProvinces>02014(所属省份、国家 会员手工录)</respectiveProvinces>
    <resultCode>1(反馈结果，1成功，其它见反馈状态说明)</resultCode>
    <totalSpendPoints>0(累计消费额)</totalSpendPoints>
    <userSignature>相信自己一切皆有可能(用户签名)</userSignature>
    <website>(网址)</website>
    <workUnit>2(工作单位)</workUnit>
    <zipCode>441200(邮政编码)</zipCode>
</response>
xml;

}


//获取用户信息
function get_member_info($memberId=''){
    if(!$memberId)
        $memberId=isset($_GET['uid'])?$_GET['uid']:'';
    if(!$memberId)
        return false;
   $url= C('ASMS_HOST')."/asms/member/t_member_add.shtml?action=edit&compid=GZML&hylx=P&hyid=$memberId";
    $data=curl_post($url,'',COOKIE_FILE,0);
    $preg="/<input .*?name=\"(.*?)\" .*?value=\"(.*?)\".*?>/i";
 //   preg_match_all($preg,$data,$info);
 //   $preg="/<select .*?name=\"(.*?)\".*? >.*?<option value=\"(.*?)\" selected >(.*?)<\/option>/si";
     preg_match_all($preg,$data,$info);
    foreach($info as $key=>$val){
        foreach($val as $k=>$v){
            if($key==0) continue;
            if($key==1)
               $kk[$k]=$v;
            if($key==2)
                $arr[$kk[$k]]=$v;
        }
    }
    return($arr);
}

/**
 * @param $shyzcm //会员注册名
 * @param string $shykh //会员卡号
 * @param string $ssj //会员手机
 * @param string $sjc //会员姓名
 */
function member_find($shyzcm,$shykh='',$ssj='',$sjc=''){
    $url=C('ASMS_HOST')."/asms/member/t_member_find_list.shtml";

    if(is_array($shyzcm)){
        $arr_post=$shyzcm;
    }else{
        $arr_post['shyzcm']=$shyzcm;//会员注册名
        $arr_post['shykh']=$shykh;//会员卡号
        $arr_post['ssj']=$ssj;//会员手机
        $arr_post['sjc']=$sjc;//会员姓名
    }
    $data=curl_post($url,$arr_post,COOKIE_FILE);
   // echo $data;

    //正则匹配数据
    $preg="/<tr class=.*?>\s<td.*?>.*?<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?><a href=\"#\" onclick=\".*?(\d+).*?\" .*?>(.*?)<\/a>.*?<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?><a href=.*? onclick=.*? title=.*?>(.*?)<\/a>.*?<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?><span.*?>(.*?)<\/span><\/td><\/tr>/si";

    preg_match_all($preg,$data,$info);
    //  print_r($info);
 //   $keyName=array(1=>'会员姓名',2=>'会员卡号',3=>'会员类型',4=>'会员等级',5=>'联系电话',6=>'返点类型',7=>'会员全称',8=>'持卡人姓名',9=>'性别',10=>'出生日期',11=>'证件号码',12=>'id',13=>'电子邮箱',14=>'业务员',15=>'创建人员',16=>'注册时间',17=>'职务',18=>'接听责任组',19=>'会员来源',20=>'累积积分',21=>'可用积分',22=>'集团号',23=>'客户经理',24=>'联系地址',25=>'所在国家或省份',26=>'所在地区',27=>'消费类型',28=>'消费积分',29=>'协议到期日',30=>'最后消费日期',31=>'最后回访日期',32=>'最后回访人',33=>'默认付款科目',34=>'备注');
    $keyName=array(1=>'会员姓名',2=>'会员卡号',3=>'会员类型',4=>'会员等级',5=>'联系电话',6=>'返点类型',7=>'会员全称',8=>'持卡人姓名',9=>'性别',10=>'出生日期',11=>'证件号码',12=>'id',13=>'电子邮箱',14=>'业务员',15=>'创建人员',16=>'注册时间',17=>'职务',18=>'接听责任组',19=>'会员来源',20=>'累积积分',21=>'可用积分',22=>'集团号',23=>'客户经理',24=>'联系地址',25=>'所在国家或省份',26=>'所在地区',27=>'消费类型',28=>'消费积分',29=>'协议到期日',30=>'最后消费日期',31=>'最后回访日期',32=>'最后回访人',33=>'默认付款科目',34=>'备注');
    if(empty($info[0])) return 0;
    foreach($info as $key=>$val){
        foreach($val as $k=>$v){
            if($key==0) continue;
                $arr[$k][$key]=$v;
        }
    }
    return $arr;
}

insert_db();
function insert_db(){
    $rs=member_find('');
    foreach($rs as $key=>$val){
        $rs[$key]['info']=get_member_info($val[8]);
    }
    print_r($rs);
//  $sql="insert into member (`name`,`registrationName`,`memberLevels`,`memberType`,`phone`,`memberId`,`gender`,`user_id`)values ('')";
}
/**
 * @param $name 会员注册名重复验证
 * @return bool|string
 */
function repeatUsername($name){
    if(member_find($name)){
        return "会员名已存在";//会员注册名
    }elseif( member_find('',$name)){
        return "会员号已存在";//会员会员卡号
    }elseif(member_find('','',$name)){
        return "手机号已存在";//手机号已存在
    }else{
        return true;
    }
}
//echo repeatUsername('test11145');
//会员注册
function registration($in1,$in2,$in3){
    $xml=simplexml_load_string($in3);
    $post_arr['action']='create';
    $post_arr['hyid']='';
    $post_arr['checksj']='1';
    $post_arr['hyzcm']='test1111';
    $post_arr['mm']='123456';
    $post_arr['xm']='123dsfs';
    $post_arr['wt']='';
    $post_arr['da']='';
    $post_arr['hyzk']='';
    $post_arr['csrq']='';
    $post_arr['xb']='M';
    $post_arr['sshy']='NI';
    $post_arr['zjhm']='.';
    $post_arr['hykh']='18673800250';
    $post_arr['zjlx2']='';
    $post_arr['zjyxq2']='';
    $post_arr['hyly']='';
    $post_arr['ywymc']='6000';
    $post_arr['ywyid']='6000';
    $post_arr['by01']='';
    $rs=curl_post(C('ASMS_HOST').'/asms/member/t_member_save.shtml',$post_arr,COOKIE_FILE);
    return ($rs);
$str=<<<xml
<?xml version="1.0" encoding="UTF-8"?>
<response>
    <message>1</message>
    <resultCode>1</resultCode>
</response>
xml;
return  $str;
}


//registration('','','');

?>