<?php
/*
 * @Title 易宝支付EPOS范例
 * @Description 用户支付后易宝"点对点"访问此页面，商户在本文件中加入自身业务
 * @Author  wenhua.cheng
 */
require_once 'YeePayCommon.php';	
require_once 'business.php';
	
// 支付成功时返回的参数
$p1_MerId=$_GET['p1_MerId'];
$r0_Cmd=$_GET['r0_Cmd'];
$r1_Code=$_GET['r1_Code'];
$r2_TrxId=$_GET['r2_TrxId'];
$r3_Amt=$_GET['r3_Amt'];
$r4_Cur=$_GET['r4_Cur'];
$r5_Pid=$_GET['r5_Pid'];
$r6_Order=$_GET['r6_Order'];
$r7_Uid=$_GET['r7_Uid'];
$r8_MP=$_GET['r8_MP'];
$r9_BType=$_GET['r9_BType'];
$rb_BankId=$_GET['rb_BankId'];
$ro_BankOrderId=$_GET['ro_BankOrderId'];
$rp_PayDate=$_GET['rp_PayDate'];
$ru_Trxtime=$_GET['ru_Trxtime'];
$hmac=$_REQUEST['hmac'];
// 支付失败时返回的参数
$rp_TrxDate=$_GET['rp_TrxDate'];
$rp_Msg=$_GET['rp_Msg'];
// 构造支付结果验证参数数组
$successCallBack=Array('p1_MerId'=>$p1_MerId,'r0_Cmd'=>$r0_Cmd,'r1_Code'=>$r1_Code,'r2_TrxId'=>$r2_TrxId,'r3_Amt'=>$r3_Amt,'r4_Cur'=>$r4_Cur,'r5_Pid'=>$r5_Pid,
'r6_Order'=>$r6_Order,'r7_Uid'=>$r7_Uid,'r8_MP'=>$r8_MP,'r9_BType'=>$r9_BType);

$failCallBack=Array('p1_MerId'=>$p1_MerId,'r0_Cmd'=>$r0_Cmd,'r1_Code'=>$r1_Code,'r2_TrxId'=>$r2_TrxId,'r3_Amt'=>$r3_Amt,'r6_Order'=>$r6_Order,'rp_TrxDate'=>$rp_TrxDate);

// 回写success，通知易宝支付商户已收到点对点响应
echo "success";
//在接收到支付结果通知后，判断是否进行过业务逻辑处理，不要重复进行业务逻辑处理
// 支付成功.可以调用您的业务逻辑，您无需关心易宝接口如何工作
if ($r0_Cmd=="Buy" && $r1_Code=="1") {
	if (verifyCallback($successCallBack,$hmac)){
	/*--------------------------------------------------------------------------------------*/
	 // 接入程序员关注部分
	 // 调用您的业务逻辑处理函数，比如：更新商户数据库的商品是否发货的状态，进行商品价格校验等
	 // doSuccessAfterPay(),doFailAfterPay();函数体在business.php添加
	/*--------------------------------------------------------------------------------------*/
	echo "支付成功";
	logurl("支付成功!", "订单号：".$r6_Order);
	doSuccessAfterPay();
		
}
}
// 支付失败。
elseif ($r0_Cmd=="EposFailed" && $r1_Code=="-100"){
	
	logurl("支付失败!","订单号：".$r6_Order);
	verifyCallback($failCallBack,$hmac);
	echo "支付失败";
	doFailAfterPay();

}
else {
	echo "支付失败";
	echo "未知错误。请联系技术支持！";
	logurl("支付失败!","未知错误。请联系技术支持!");
}

?> 
