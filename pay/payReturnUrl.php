<?php
$data=array();
$data['input']=file_get_contents('php://input');  # 获取trafree推送的信息
$data['get']=$_GET;
$data['post']=$_POST;
$data=json_encode($data);	
$dir='./log';
if(!is_dir($dir))mkdir($dir,'0777',true);
file_put_contents($dir.'/'.date("Y-m-d").'2.log',date("H:i:s")."\n".$data."\n",FILE_APPEND);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>爱尚飞国际机票在线支付</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
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
      <td width="379" align="right" valign="middle" font style="color:#000000;font-size:12px;">您好，请 <A 
      href="/register.php" target="_blank">注册</A> 或 <A 
      href="/login.php" target="_blank">登录</A> | <A 
      href="/" target="_blank">爱尚飞首页</A></td>
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
        
        <table width="760" border="0" cellspacing="0" cellpadding="0" align="center" height="1">
          <tr>
            <td width="740" bgcolor="#CCCCCC"></td>
            <td width="20"></td>
          </tr>
        </table>
        <table width="760" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<?php
					//---------------------------------------------------------
					//财付通即时到帐支付页面回调示例，商户按照此文档进行开发即可
					//---------------------------------------------------------
					require_once ("./classes/ResponseHandler.class.php");
					require_once ("./classes/function.php");
					require_once ("./tenpay_config.php");

					log_result("进入前台回调页面");


					/* 创建支付应答对象 */
					$resHandler = new ResponseHandler();
					$resHandler->setKey($key);

					//判断签名
					if($resHandler->isTenpaySign()) {
						//通知id
						$notify_id = $resHandler->getParameter("notify_id");
						//商户订单号
						$out_trade_no = $resHandler->getParameter("out_trade_no");
						//财付通订单号
						$transaction_id = $resHandler->getParameter("transaction_id");
						//金额,以分为单位
						$total_fee = $resHandler->getParameter("total_fee");
						//如果有使用折扣券，discount有值，total_fee+discount=原请求的total_fee
						$discount = $resHandler->getParameter("discount");
						//支付结果
						$trade_state = $resHandler->getParameter("trade_state");
						//交易模式,1即时到账
						$trade_mode = $resHandler->getParameter("trade_mode");
						
						
						if("1" == $trade_mode ) {
							if( "0" == $trade_state){ 
								//------------------------------
								//处理业务开始
								//------------------------------
								
								//注意交易单不要重复处理
								//注意判断返回金额
								
								//------------------------------
								//处理业务完毕
								//------------------------------	
								
								echo "<br/>" . "<strong style='color:green;font-size:18px;'>即时到帐支付成功</strong>" . "<br/>";
						
							} else {
								//当做不成功处理
								echo "<br/>" . "<strong style='color:red;font-size:18px;'>即时到帐支付失败</strong>" . "<br/>";
							}
						}elseif( "2" == $trade_mode  ) {
							if( "0" == $trade_state) {
							
								//------------------------------
								//处理业务开始
								//------------------------------
								
								//注意交易单不要重复处理
								//注意判断返回金额
								
								//------------------------------
								//处理业务完毕
								//------------------------------	
								
								echo "<br/>" . "<strong style='color:green;font-size:18px;'>中介担保支付成功</strong>" . "<br/>";
							
							} else {
								//当做不成功处理
								echo "<br/>" . "<strong style='color:red;font-size:18px;'>中介担保支付失败</strong>" . "<br/>";
							}
						}
						
					} else {
						echo "<br/>" . "<strong style='color:red;font-size:18px;'>认证签名失败</strong>" . "<br/>";
						echo $resHandler->getDebugInfo() . "<br>";
					}

					?>
				</td>
			</tr>
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