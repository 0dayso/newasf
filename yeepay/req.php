<?php
/*
 * @Title �ױ�֧��EPOS����
 * @Description �����ļ�����ȡ�û��ύ��Ϣ
 * @V3.0
 * @Author wenhua.cheng
 */
require_once 'yeePayCommon.php';	
require_once 'business.php';
// ��ȡ������
$p2_Order=$_POST['p2_Order'];

// ��ȡ�����ύ���ѽ��
$p3_Amt=$_POST['p3_Amt'];

// ��ȡ�����ύ��Ʒ����
$p5_Pid=$_POST['p5_Pid'];

// ��ȡ�����ύ����֧�������ַ
$p8_Url=$_POST['p8_Url'];

// ��ȡ�����ύ֤������
$pa_CredType=$_POST['pa_CredType'];

// ��ȡ�����ύ֤������
//$pb_CredCode=iconv('GBK','UTF-8',$_POST['pb_CredCode']);

// ��ȡ�����ύ���б���
$pd_FrpId=$_POST['pd_FrpId'];

// ��ȡ�����ύ�������ֻ���
$pe_BuyerTel=$_POST['pe_BuyerTel'];

// ��ȡ�����ύ����������
//$pf_BuyerName=iconv('GBK','UTF-8',$_POST['pf_BuyerName']);

// ��ȡ�����ύ���ÿ�����
$pt_ActId=$_POST['pt_ActId'];

// ��ȡ�����ύ��Ч�ڣ��꣩
$pa2_ExpireYear=$_POST['pa2_ExpireYear'];

// ��ȡ�����ύ��Ч�ڣ��£�
$pa3_ExpireMonth=$_POST['pa3_ExpireMonth'];

// ��ȡ�����ύ���ÿ�CVV��
$pa4_CVV=$_POST['pa4_CVV'];


// ��������ҵ���������������
$bizArray=Array(
'p0_Cmd'=>$p0_Cmd,'p1_MerId'=>$p1_MerId,'p2_Order'=>$p2_Order,'p3_Amt'=>$p3_Amt,'p4_Cur'=>$p4_Cur,'p5_Pid'=>$p5_Pid,'p8_Url'=>$p8_Url,'pa_CredType'=>$pa_CredType,
'pb_CredCode'=>$pb_CredCode,'pd_FrpId'=>$pd_FrpId,'pe_BuyerTel'=>$pe_BuyerTel,'pf_BuyerName'=>$pf_BuyerName,'pt_ActId'=>$pt_ActId,'pa2_ExpireYear'=>$pa2_ExpireYear,
'pa3_ExpireMonth'=>$pa3_ExpireMonth,'pa4_CVV'=>$pa4_CVV);



/*-------------------------------------------------------*/
// �������Ա��ע���֣��˺�����ѡ��ʹ�ã�	
// ��������ҵ���߼������������磺����Ʒ״̬��Ϊ���µ���
// doBeforPay()��������business.php���
/*-------------------------------------------------------*/
doBeforPay();



// ����֧��ҵ�������������Ա�����ע
eposSale($bizArray);

?> 

