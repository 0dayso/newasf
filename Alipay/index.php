<?php
/* *
* 功能：支付宝即时到账交易接口接口调试入口页面
* 版本：3.3
* 日期：2012-07-23
* 说明：
* 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
*/
//4位随机数
$spname="广州美乐商务服务有限公司";
$randNum = rand(1000, 9999);

//订单号，此处用时间加随机数生成
$out_trade_no = date("YmdHis") . $randNum;
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
                    <td width="743" height="30" bgcolor="#FF6600"><font style="color:#FFFFFF;font-size:14px;"><B> 　支付宝快速付款通道 方便 快捷 安全</B></font></td>
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

                        if (directFrm.WIDout_trade_no.value=="")
                        {
                            alert("提醒：请填写订单编号；如果无特定的订单编号，请采用默认编号！（刷新一下页面就可以了）");
                            directFrm.WIDout_trade_no.focus();
                            return false;
                        }
                        if (directFrm.WIDsubject.value=="")
                        {
                            alert("提醒：请填写商品名称(付款项目)！");
                            directFrm.WIDsubject.focus();
                            return false;
                        }
                        if (directFrm.WIDtotal_fee.value=="")
                        {
                            alert("提醒：请填写订单的交易金额！");
                            directFrm.WIDtotal_fee.focus();
                            return false;
                        }

                        if (directFrm.WIDbody.value=="")
                        {
                            alert("提醒：请填写您的简要说明！");
                            directFrm.WIDbody.focus();
                            return false;
                        }
                        if (directFrm.WIDbody.value.length>31)
                        {
                            alert("提醒：超出规定的字数,请重新输入");
                            event.returnValue=false;
                            return   false;
                        }

                        return true;
                    }
                </script>
                <form action='alipayapi.php' method='post' name='directFrm' onSubmit="return payFrm();">
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
                                                <td valign="top" align="left"><input type="text" name="WIDout_trade_no" maxlength="55" size="20" readonly="readonly" value="<?php echo $out_trade_no ?>" font style="color:#000000;font-size:14px;"></td>
                                            </tr>
                                            <tr>
                                                <td align="right" valign="top">付款项目：</td>
                                                <td valign="top" align="left"><span style="color:#000000;font-size:12px;">
                            <input name="WIDsubject" type="text" size="18" maxlength="50" font style="color:#000000;font-size:14px;">
                            </span></td>
                                            </tr>
                                            <tr>
                                                <td align="right" valign="top">付款金额：</td>
                                                <td valign="top" align="left"><input type="text" name="WIDtotal_fee" maxlength="50" size="18" onKeyUp="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')" font style="color:#000000;font-size:14px;">
                                                    元（格式：500.01）</td>
                                            </tr>

                                            <tr>
                                                <td height="99" align="right" valign="top">简要说明：</td>
                                                <td valign="top" align="left"><textarea name="WIDbody" cols="48" rows="5" id="remark2"  font style="color:#000000;font-size:14px;"></textarea>
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