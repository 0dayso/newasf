<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>YeePay �ױ�֧�� - ���з�</title>
<style type="text/css">
    body{
        font-size:14px;
        width:960px;
        margin:10px auto;
    }
    table{
        width:100%;
    }
    td{
        height:26px;
        line-height:26px;
    }
    td.label{
        text-align:right;
        padding-right:15px;
    }
    span.required{
        color:#FF0000;
        font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
    }
    form{
        margin-top:20px;
        border:1px solid #4BBB5A;
        padding:20px;
        border-radius:5px;
    }
    .top-link{
        color:#50BD5F;
        text-decoration:none;
    }
	img{
		border:none;
	}
</style>
</head>
<body>
<table>
    <tr>
        <td style="width:740px"><a href="http://www.yeepay.com" target="_blank"><img src="logo_yeepay.gif" /></a></td>
        <td style="text-align:right;vertical-align:top">
            <a href="http://www.aishangfei.net" class="top-link">���з���ҳ</a>  |  
            <a href="http://www.aishangfei.net/pay" class="top-link">�Ƹ�֧ͨ��</a>
        </td>
    </tr>
</table>

<form name="yeepay" action='req.php' method='post'>
  <div>
    <span class="required">*</span> 
    Ϊ�����
  </div>
  
  <input type='hidden' name='p2_Order' value='<?php echo date("YmdHis",time());?>'><!--������-->
  <input name='p8_Url' type='hidden' value='http://www.aishangfei.net/epos/callback.php' size="50" maxlength="400"><!--�����ױ�������֪ͨ��������-->
  <input type="hidden" name="pr_NeedResponse"  value="0"><!-- Ӧ����� Ϊ��1��: ��ҪӦ�����;Ϊ��0��: ����ҪӦ����� -->

  <table>
    <tr>
      <td class="label">���ѽ��<span class="required">*</span></td>
      <td>
        <input type='text' name='p3_Amt' value=''>
      </td>
    </tr>
    <tr>
      <td class="label">��Ʒ����</td>
      <td><input type='text' name='p5_Pid' value=''>
      </td>
    </tr>
    <tr>
      <td class="label">֤������<span class="required">*</span></td>
      <td><select   name='pa_CredType' >
          <option value="IDCARD">���֤</option>
          <option value="OFFICERPASS">����֤</option>
          <option value="HM_VISITORPASS">�ľ��������ڵ�ͨ��֤</option>
          <option value="T_VISITORPASS">̨�����������½ͨ��֤</option>
        </select>
        </td>
    </tr>
    <tr>
      <td class="label">֤������<span class="required">*</span></td>
      <td><input type='text' name='pb_CredCode' value=''>
      </td>
    </tr>
    <tr>
      <td class="label">������<span class="required">*</span></td>
      <td><!--<input type='text' name='pd_FrpId' value='ICBCCREDIT'>(����)����������ͨ��������������ĵ�-->
        <select name="pd_FrpId">
          <option value="BOCOCREDIT">��ͨ����</option>
          <option value="ECITICCREDIT">��������</option>
          <option value="ICBCCREDIT">��������</option>
          <option value="BOCCREDIT">�й�����</option>
          <option value="CIBCREDIT">��ҵ����</option>
          <option value="CCBCREDIT">��������</option>
          <option value="PINGANCREDIT">ƽ������</option>
          <option value="CMBCHINACREDIT">��������</option>
          <option value="ABCCREDIT">�й�ũҵ����</option>
          <option value="CMBCCREDIT">�й���������</option>
          <option value="GDBCREDIT">�㷢����</option>
          <option value="BCCBCREDIT">��������</option>
          <option value="BOSHCREDIT">�Ϻ�����</option>
        </select>
        </td>
    </tr>
    <tr>
      <td class="label">�ֻ���<span class="required">*</span></td>
      <td><input type='text' name='pe_BuyerTel' value=''>
      </td>
    </tr>
    <tr>
      <td class="label">����������<span class="required">*</span></td>
      <td><input type='text' name='pf_BuyerName' value=''>
      </td>
    </tr>
    <tr>
      <td class="label">֧������<span class="required">*</span></td>
      <td><input type='text' name='pt_ActId' value=''>
      </td>
    </tr>
    <tr>
      <td class="label">���ÿ���Ч��(��)<span class="required">*</span></td>
      <td><input type='text' name='pa2_ExpireYear' value=''>
      ��Ч�ڽ�ֹ��ݣ�������2007-2099��֮�䣬����2015</td>
    </tr>
    <tr>
      <td class="label">���ÿ���Ч��(��)<span class="required">*</span></td>
      <td><input type='text' name='pa3_ExpireMonth' value=''>
      ��Ч�ڽ�ֹ�·ݣ�����Ϊ1-12֮�䣬����9</td>
    </tr>
    <tr>
      <td class="label">CVV<span class="required">*</span></td>
      <td><input type='text' name='pa4_CVV' value=''>
        ���ÿ������3��4λCVV��</td>
    </tr>
    <tr>
      <td></td>
      <td style="height:50px;"><input type='submit' value='�ύ��'></td>
    </tr>
  </table>
</form>
</body>
</html>
