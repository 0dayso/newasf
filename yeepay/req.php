<?php
/*
 * @Title 易宝支付EPOS范例
 * @Description 请求文件，获取用户提交信息
 * @V3.0
 * @Author wenhua.cheng
 */
require_once 'yeePayCommon.php';	
require_once 'business.php';
// 获取订单号
$p2_Order=$_POST['p2_Order'];

// 获取本地提交消费金额
$p3_Amt=$_POST['p3_Amt'];

// 获取本地提交商品名称
$p5_Pid=$_POST['p5_Pid'];

// 获取本地提交接收支付结果地址
$p8_Url=$_POST['p8_Url'];

// 获取本地提交证件类型
$pa_CredType=$_POST['pa_CredType'];

// 获取本地提交证件号码
//$pb_CredCode=iconv('GBK','UTF-8',$_POST['pb_CredCode']);

// 获取本地提交银行编码
$pd_FrpId=$_POST['pd_FrpId'];

// 获取本地提交消费者手机号
$pe_BuyerTel=$_POST['pe_BuyerTel'];

// 获取本地提交消费者姓名
//$pf_BuyerName=iconv('GBK','UTF-8',$_POST['pf_BuyerName']);

// 获取本地提交信用卡卡号
$pt_ActId=$_POST['pt_ActId'];

// 获取本地提交有效期（年）
$pa2_ExpireYear=$_POST['pa2_ExpireYear'];

// 获取本地提交有效期（月）
$pa3_ExpireMonth=$_POST['pa3_ExpireMonth'];

// 获取本地提交信用卡CVV码
$pa4_CVV=$_POST['pa4_CVV'];


// 构造请求业务处理所需参数数组
$bizArray=Array(
'p0_Cmd'=>$p0_Cmd,'p1_MerId'=>$p1_MerId,'p2_Order'=>$p2_Order,'p3_Amt'=>$p3_Amt,'p4_Cur'=>$p4_Cur,'p5_Pid'=>$p5_Pid,'p8_Url'=>$p8_Url,'pa_CredType'=>$pa_CredType,
'pb_CredCode'=>$pb_CredCode,'pd_FrpId'=>$pd_FrpId,'pe_BuyerTel'=>$pe_BuyerTel,'pf_BuyerName'=>$pf_BuyerName,'pt_ActId'=>$pt_ActId,'pa2_ExpireYear'=>$pa2_ExpireYear,
'pa3_ExpireMonth'=>$pa3_ExpireMonth,'pa4_CVV'=>$pa4_CVV);



/*-------------------------------------------------------*/
// 接入程序员关注部分（此函数可选择使用）	
// 调用您的业务逻辑处理函数，比如：将商品状态改为“下单”
// doBeforPay()函数体在business.php添加
/*-------------------------------------------------------*/
doBeforPay();



// 调用支付业务函数，接入程序员无需关注
eposSale($bizArray);

?> 

