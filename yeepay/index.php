<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>YeePay 易宝支付 - 爱尚飞</title>
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
            <a href="http://www.aishangfei.net" class="top-link">爱尚飞首页</a>  |  
            <a href="http://www.aishangfei.net/pay" class="top-link">财付通支付</a>
        </td>
    </tr>
</table>

<form name="yeepay" action='req.php' method='post'>
  <div>
    <span class="required">*</span> 
    为必填项。
  </div>
  
  <input type='hidden' name='p2_Order' value='<?php echo date("YmdHis",time());?>'><!--订单号-->
  <input name='p8_Url' type='hidden' value='http://www.aishangfei.net/epos/callback.php' size="50" maxlength="400"><!--用于易宝服务器通知返回数据-->
  <input type="hidden" name="pr_NeedResponse"  value="0"><!-- 应答机制 为“1”: 需要应答机制;为“0”: 不需要应答机制 -->

  <table>
    <tr>
      <td class="label">消费金额<span class="required">*</span></td>
      <td>
        <input type='text' name='p3_Amt' value=''>
      </td>
    </tr>
    <tr>
      <td class="label">商品名称</td>
      <td><input type='text' name='p5_Pid' value=''>
      </td>
    </tr>
    <tr>
      <td class="label">证件类型<span class="required">*</span></td>
      <td><select   name='pa_CredType' >
          <option value="IDCARD">身份证</option>
          <option value="OFFICERPASS">军官证</option>
          <option value="HM_VISITORPASS">澳居民往来内地通行证</option>
          <option value="T_VISITORPASS">台湾居民来往大陆通行证</option>
        </select>
        </td>
    </tr>
    <tr>
      <td class="label">证件号码<span class="required">*</span></td>
      <td><input type='text' name='pb_CredCode' value=''>
      </td>
    </tr>
    <tr>
      <td class="label">发卡行<span class="required">*</span></td>
      <td><!--<input type='text' name='pd_FrpId' value='ICBCCREDIT'>(必填)其他发卡行通道编码请见帮助文档-->
        <select name="pd_FrpId">
          <option value="BOCOCREDIT">交通银行</option>
          <option value="ECITICCREDIT">中信银行</option>
          <option value="ICBCCREDIT">工商银行</option>
          <option value="BOCCREDIT">中国银行</option>
          <option value="CIBCREDIT">兴业银行</option>
          <option value="CCBCREDIT">建设银行</option>
          <option value="PINGANCREDIT">平安银行</option>
          <option value="CMBCHINACREDIT">招商银行</option>
          <option value="ABCCREDIT">中国农业银行</option>
          <option value="CMBCCREDIT">中国民生银行</option>
          <option value="GDBCREDIT">广发银行</option>
          <option value="BCCBCREDIT">北京银行</option>
          <option value="BOSHCREDIT">上海银行</option>
        </select>
        </td>
    </tr>
    <tr>
      <td class="label">手机号<span class="required">*</span></td>
      <td><input type='text' name='pe_BuyerTel' value=''>
      </td>
    </tr>
    <tr>
      <td class="label">消费者姓名<span class="required">*</span></td>
      <td><input type='text' name='pf_BuyerName' value=''>
      </td>
    </tr>
    <tr>
      <td class="label">支付卡号<span class="required">*</span></td>
      <td><input type='text' name='pt_ActId' value=''>
      </td>
    </tr>
    <tr>
      <td class="label">信用卡有效期(年)<span class="required">*</span></td>
      <td><input type='text' name='pa2_ExpireYear' value=''>
      有效期截止年份，必须在2007-2099年之间，比如2015</td>
    </tr>
    <tr>
      <td class="label">信用卡有效期(月)<span class="required">*</span></td>
      <td><input type='text' name='pa3_ExpireMonth' value=''>
      有效期截止月份，必须为1-12之间，比如9</td>
    </tr>
    <tr>
      <td class="label">CVV<span class="required">*</span></td>
      <td><input type='text' name='pa4_CVV' value=''>
        信用卡背面的3或4位CVV码</td>
    </tr>
    <tr>
      <td></td>
      <td style="height:50px;"><input type='submit' value='提交表单'></td>
    </tr>
  </table>
</form>
</body>
</html>
