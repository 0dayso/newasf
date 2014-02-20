<?php
require_once ("classes/RequestHandler.class.php");
require_once ("tenpay_config.php");

//4位随机数
$randNum = rand(1000, 9999);

//订单号，此处用时间加随机数生成
$out_trade_no = date("YmdHis") . $randNum;
    
if(!empty($_POST))
{
    /* 创建支付请求对象 */
    $reqHandler = new RequestHandler();
    $reqHandler->init();
    $reqHandler->setKey($key);
    $reqHandler->setGateUrl("https://gw.tenpay.com/gateway/pay.htm");
    $return_url=isset($_POST['return_url'])?$_POST['return_url']:$return_url;//支付后返回地址

    //----------------------------------------
    //设置支付参数 
    //----------------------------------------
    $reqHandler->setParameter("partner", $partner);
    $reqHandler->setParameter("out_trade_no", $_POST['order_no']);
    $reqHandler->setParameter("total_fee",$_POST['order_price']*100);  //总金额
    $reqHandler->setParameter("return_url",  $return_url);
    $reqHandler->setParameter("notify_url", $notify_url);
    $reqHandler->setParameter("body", $_POST['product_name']);
    $reqHandler->setParameter("bank_type", "DEFAULT");  	  //银行类型，默认为财付通
    //用户ip
    $reqHandler->setParameter("spbill_create_ip", $_SERVER['REMOTE_ADDR']);//客户端IP
    $reqHandler->setParameter("fee_type", "1");               //币种
    $reqHandler->setParameter("subject",$_POST['product_name']);          //商品名称，（中介交易时必填）

    //系统可选参数
    $reqHandler->setParameter("sign_type", "MD5");  	 	  //签名方式，默认为MD5，可选RSA
    $reqHandler->setParameter("service_version", "1.0"); 	  //接口版本号
    $reqHandler->setParameter("input_charset", "UTF-8");   	  //字符集
    $reqHandler->setParameter("sign_key_index", "1");    	  //密钥序号

    //业务可选参数
    $reqHandler->setParameter("attach", ""); 	  //附件数据，原样返回就可以了
    $reqHandler->setParameter("product_fee", "");        	  //商品费用
    $reqHandler->setParameter("transport_fee", "0");      	  //物流费用
    $reqHandler->setParameter("time_start",date("YmdHis"));  //订单生成时间
    $reqHandler->setParameter("time_expire", "");             //订单失效时间
    $reqHandler->setParameter("buyer_id", "");                //买方财付通帐号
    $reqHandler->setParameter("goods_tag", isset($_POST['order_id_arr'])?$_POST['order_id_arr']:"");               //商品标记
    $reqHandler->setParameter("trade_mode",$_POST['trade_mode']);              //交易模式（1.即时到帐模式，2.中介担保模式，3.后台选择（卖家进入支付中心列表选择））
    $reqHandler->setParameter("transport_desc","");              //物流说明
    $reqHandler->setParameter("trans_type","1");              //交易类型
    $reqHandler->setParameter("agentid","");                  //平台ID
    $reqHandler->setParameter("agent_type","0");               //代理模式（0.无代理，1.表示卡易售模式，2.表示网店模式）
    $reqHandler->setParameter("seller_id",$partner);                //卖家的商户号
    
    $tenpayUrl=$reqHandler->getRequestURL();
    $debugInfo=$reqHandler->getDebugInfo();
    
    //echo  $tenpayUrl;
    //转向支付页面
    header("Location:$tenpayUrl");
 }
 else
 {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>爱尚飞国际机票在线支付</title>
<meta http-equiv="Content-Type"  content="text/html;charset=utf-8" />
<style type="text/css">
<!--
a:link {
	color: #003399;
}
.px12 {
	font-size: 12px;
}
a:visited {
	color: #003399;
}
a:hover {
	color: #FF6600;
}
.px14 {
	font-size: 14px;
}
.px12hui {
	font-size: 12px;
	color: #999999;
}
.STYLE2 {
	font-size: 14px;
	font-weight: bold;
}
-->
</style>
</head>
<body>
<div align="center">
  <table width="760" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td width="381" align="left" valign="middle"><a href="http://www.aishangfei.net" target="_blank"><img src="image/logo.gif" border="0"></a> 在线支付</td>
      <td width="379" align="right" valign="middle" font style="color:#000000;font-size:12px;">
		<A  href="/" target="_blank">爱尚飞首页</A> |
        <a href="../alipay" >支付宝</a> |
          <a href="../pay" >财付通</a> |
          <a href="../yeepay" >易宝ePOS支付</a>
	  </td>
    </tr>
  </table>
  <table width="760" height="25" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td></td>
    </tr>
  </table>
  <table width="760" height="406" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td align="center" valign="top"><table width="760" border="0" cellspacing="0" cellpadding="3" align="center">
          <tr>
            <td height="30" width="5" bgcolor="#666666"></td>
            <td width="743" height="30" bgcolor="#FF6600"><font style="color:#FFFFFF;font-size:14px;"><B> 　财付通快速付款通道 方便 快捷 安全</B></font></td>
          </tr>
        </table>
        <table width="760" height="42" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td height="30" ><span class="STYLE2"><img src="image/arrow_02_01.gif"> 填写订单信息</span></td>
          </tr>
        </table>
        <table width="760" border="0" cellspacing="0" cellpadding="0" align="center" height="1">
          <tr>
            <td width="740" bgcolor="#CCCCCC"></td>
            <td width="20"></td>
          </tr>
        </table>
        <table width="760" border="0" align="center" cellpadding="0" cellspacing="0">
          <script language="javascript">
	function payFrm()
	{
		if (directFrm.order_no.value=="")
		{
			alert("提醒：请填写订单编号；如果无特定的订单编号，请采用默认编号！（刷新一下页面就可以了）");
			directFrm.order_no.focus();
			return false;
		}
		if (directFrm.product_name.value=="")
		{
			alert("提醒：请填写商品名称(付款项目)！");
			directFrm.product_name.focus();
			return false;
		}
		if (directFrm.order_price.value=="")
		{
			alert("提醒：请填写订单的交易金额！");
			directFrm.order_price.focus();
			return false;
		}
		
		if (directFrm.remarkexplain.value=="")
		{
			alert("提醒：请填写您的简要说明！");
			directFrm.remarkexplain.focus();
			return false;
		}
		if (directFrm.remarkexplain.value.length>31)
		{
			alert("提醒：超出规定的字数,请重新输入");
  			event.returnValue=false;   
  			return   false;   
		}
		
		return true;
	}
  </script>
          <form action='' method='post' name='directFrm' onSubmit="return payFrm();">
            <tr>
              <td align="left"><table width="760" height="30" border="0" align="left" cellpadding="0" cellspacing="1" bgcolor="#FFCC00">
                  <tr>
                    <td align="center" valign="top" bgcolor="#FFFFEE"><table width="760" height="351" border="0" cellpadding="6" cellspacing="0" class="px14">
                        <tr>
                          <td height="26" align="right" valign="top">&nbsp;</td>
                          <td valign="top"> 　 </td>
                          <td width="269" rowspan="8" valign="top"><table width="100%" border="0" align="left" cellpadding="0" cellspacing="5">
                              <tr>
                                <td height="10" align="center" valign="middle"></td>
                              </tr>
                              <tr>
                                <td height="24"><font style="color:#000000;font-size:12px;"></font></td>
                              </tr>
                              <tr>
                                <td height="24" font style="color:#000000;font-size:12px;"></td>
                              </tr>
                              <tr>
                                <td height="24"><font style="color:#000000;font-size:12px;"></font></td>
                              </tr>
                              <tr>
                                <td height="24" font style="color:#000000;font-size:12px;"></td>
                              </tr>
                            </table></td>
                        </tr>
                        <tr>
                          <td width="102" height="26" align="right" valign="top">收款方：</td>
                          <td width="353" valign="top" align="left"><?php echo  $spname ?></td>
                        </tr>
                        <tr>
                          <td align="right" valign="top">订单编号：</td>
                          <td valign="top" align="left"><input type="text" name="order_no" maxlength="50" size="18" readonly="readonly" value="<?php echo $out_trade_no ?>" font style="color:#000000;font-size:14px;"></td>
                        </tr>
                        <tr>
                          <td align="right" valign="top">付款项目：</td>
                          <td valign="top" align="left"><span style="color:#000000;font-size:12px;">
                            <input name="product_name" type="text" size="18" maxlength="50" font style="color:#000000;font-size:14px;">
                            </span></td>
                        </tr>
                        <tr>
                          <td align="right" valign="top">付款金额：</td>
                          <td valign="top" align="left"><input type="text" name="order_price" maxlength="50" size="18" onKeyUp="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')" font style="color:#000000;font-size:14px;">
                            元（格式：500.01）</td>
                        </tr>
                        <tr>
                          <td align="right" valign="top">支付方式：</td>
                          <td valign="top" align="left"><span style="color:#000000;font-size:12px;">
                            <input type="radio" name="trade_mode" value="1"  checked="true">
                            即时到帐&nbsp;
                            <input type="radio" name="trade_mode" value="2">
                            中介担保&nbsp;
                            <input type="radio" name="trade_mode" value="3">
                            后台选择 </span></td>
                        </tr>
                        <tr>
                          <td height="99" align="right" valign="top">简要说明：</td>
                          <td valign="top" align="left"><textarea name="remarkexplain" cols="48" rows="5" id="remark2"  font style="color:#000000;font-size:14px;"></textarea>
                            <br>
                            请填写您订单的简要说明（30字以内）</td>
                        </tr>
                        <tr>
                          <td align="right" valign="top">&nbsp;</td>
                          <td valign="top" align="left"><b>
                            <input name="submit" type="image" src="image/next.gif" alt="使用财付通安全支付" width="103" height="27">
                            </b></td>
                          <td valign="top">&nbsp;</td>
                        </tr>
                      </table></td>
                  </tr>
                </table></td>
            </tr>
          </form>
        </table>
        <table width="760" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>&nbsp;</td>
          </tr>
        </table></td>
    </tr>
  </table>
  <TABLE width=760 border=0 cellPadding=0 cellSpacing=4 class="px12">
    <TBODY>
      <TR>
        <TD width="71" rowSpan=6 align="center" noWrap bgcolor="#CCCCCC" class=box-note><FONT 
      class=note-help>支持<FONT class=note-help>银行 </FONT></FONT></TD>
        <TD width="14" rowSpan="6"></TD>
        <TD style="padding:5px">
			<IMG src="image/1.gif" border=0>
		</TD>
        <TD style="padding:5px">
			<IMG src="image/2.gif" border=0>
		</TD>
		<TD style="padding:5px">
			<IMG src="image/3.gif" border=0>
		</TD>
		<TD style="padding:5px">
			<IMG src="image/4.gif" border=0>
		</TD>
		<TD style="padding:5px">
			<IMG src="image/5.gif" border=0>
		</TD>
      </TR>
      <TR>
        <TD style="padding:5px">
			<IMG src="image/6.gif" border=0>
		</TD>
        <TD style="padding:5px">
			<IMG src="image/7.gif" border=0>
		</TD>
		<TD style="padding:5px">
			<IMG src="image/8.gif" border=0>
		</TD>
		<TD style="padding:5px">
			<IMG src="image/9.gif" border=0>
		</TD>
		<TD style="padding:5px">
			<IMG src="image/10.gif" border=0>
		</TD>
      </TR>
	  <TR>
        <TD style="padding:5px">
			<IMG src="image/11.gif" border=0>
		</TD>
        <TD style="padding:5px">
			<IMG src="image/12.gif" border=0>
		</TD>
		<TD style="padding:5px">
			<IMG src="image/13.gif" border=0>
		</TD>
		<TD style="padding:5px">
			<IMG src="image/14.gif" border=0>
		</TD>
		<TD style="padding:5px">
			<IMG src="image/15.gif" border=0>
		</TD>
      </TR>
	  <TR>
        <TD style="padding:5px">
			<IMG src="image/16.gif" border=0>
		</TD>
        <TD style="padding:5px">
			<IMG src="image/17.gif" border=0>
		</TD>
		<TD style="padding:5px">
			<IMG src="image/18.gif" border=0>
		</TD>
		<TD style="padding:5px">
			<IMG src="image/19.gif" border=0>
		</TD>
		<TD style="padding:5px">
			<IMG src="image/20.gif" border=0>
		</TD>
      </TR>
	  <TR>
        <TD style="padding:5px">
			<IMG src="image/21.gif" border=0>
		</TD>
        <TD style="padding:5px">
			<IMG src="image/22.gif" border=0>
		</TD>
		<TD style="padding:5px">
			<IMG src="image/23.gif" border=0>
		</TD>
		<TD style="padding:5px">
			<IMG src="image/24.gif" border=0>
		</TD>
		<TD style="padding:5px">
			<IMG src="image/25.gif" border=0>
		</TD>
      </TR>
	  <TR>
        <TD style="padding:5px">
			<IMG src="image/26.gif" border=0>
		</TD>
        <TD style="padding:5px">
			<IMG src="image/27.gif" border=0>
		</TD>
		<TD style="padding:5px">
			<IMG src="image/28.gif" border=0>
		</TD>
		<TD style="padding:5px">
			<IMG src="image/29.gif" border=0>
		</TD>
		<TD style="padding:5px">
			<IMG src="image/30.gif" border=0>
		</TD>
      </TR>
    </TBODY>
  </TABLE>
</div>
<HR width=760 SIZE=1>
<TABLE width=760 border=0 align="center" cellSpacing=1 class="px12hui">
  <TBODY>
    <TR>
      <TD></TD>
    </TR>
  </TBODY>
</TABLE>
</CENTER>
</CENTER>
</body>
</html>
<?php }?>
