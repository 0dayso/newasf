<?php
/*
 * @Title �ױ�֧��EPOS����
 * @Description �û�֧�����ױ�"��Ե�"���ʴ�ҳ�棬�̻��ڱ��ļ��м�������ҵ��
 * @Author  wenhua.cheng
 */
require_once 'YeePayCommon.php';	
require_once 'business.php';
	
// ֧���ɹ�ʱ���صĲ���
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
// ֧��ʧ��ʱ���صĲ���
$rp_TrxDate=$_GET['rp_TrxDate'];
$rp_Msg=$_GET['rp_Msg'];
// ����֧�������֤��������
$successCallBack=Array('p1_MerId'=>$p1_MerId,'r0_Cmd'=>$r0_Cmd,'r1_Code'=>$r1_Code,'r2_TrxId'=>$r2_TrxId,'r3_Amt'=>$r3_Amt,'r4_Cur'=>$r4_Cur,'r5_Pid'=>$r5_Pid,
'r6_Order'=>$r6_Order,'r7_Uid'=>$r7_Uid,'r8_MP'=>$r8_MP,'r9_BType'=>$r9_BType);

$failCallBack=Array('p1_MerId'=>$p1_MerId,'r0_Cmd'=>$r0_Cmd,'r1_Code'=>$r1_Code,'r2_TrxId'=>$r2_TrxId,'r3_Amt'=>$r3_Amt,'r6_Order'=>$r6_Order,'rp_TrxDate'=>$rp_TrxDate);

// ��дsuccess��֪ͨ�ױ�֧���̻����յ���Ե���Ӧ
echo "success";
//�ڽ��յ�֧�����֪ͨ���ж��Ƿ���й�ҵ���߼�������Ҫ�ظ�����ҵ���߼�����
// ֧���ɹ�.���Ե�������ҵ���߼�������������ױ��ӿ���ι���
if ($r0_Cmd=="Buy" && $r1_Code=="1") {
	if (verifyCallback($successCallBack,$hmac)){
	/*--------------------------------------------------------------------------------------*/
	 // �������Ա��ע����
	 // ��������ҵ���߼������������磺�����̻����ݿ����Ʒ�Ƿ񷢻���״̬��������Ʒ�۸�У���
	 // doSuccessAfterPay(),doFailAfterPay();��������business.php���
	/*--------------------------------------------------------------------------------------*/
	echo "֧���ɹ�";
	logurl("֧���ɹ�!", "�����ţ�".$r6_Order);
	doSuccessAfterPay();
		
}
}
// ֧��ʧ�ܡ�
elseif ($r0_Cmd=="EposFailed" && $r1_Code=="-100"){
	
	logurl("֧��ʧ��!","�����ţ�".$r6_Order);
	verifyCallback($failCallBack,$hmac);
	echo "֧��ʧ��";
	doFailAfterPay();

}
else {
	echo "֧��ʧ��";
	echo "δ֪��������ϵ����֧�֣�";
	logurl("֧��ʧ��!","δ֪��������ϵ����֧��!");
}

?> 
