/**
*通用客服弹窗
*作者：吴海发
*邮件：wuhaifa@163.com
*调用方法
*<script type="text/javascript" src="URL/serviceWin.js" id="swjs" tel="400-869-6888"></script>
*URL为JS的地址
*tel=""在这里修改电话号码
*/
document.writeln("<div id=\"serviceWin\">");
document.writeln("<div id=\"sw\">");
document.writeln("<div id=\"sw_close\"><img src=\"http://www.aishangfei.com/Public/images/sw/sw_close.gif\" /></div>");
document.writeln("<div id=\"sw_open\"><img src=\"http://www.aishangfei.com/Public/images/sw/sw_open.gif\" /></div>");
document.writeln("<div id=\"sw_tel\">400-608-5188</div>");
document.writeln("</div>");
document.writeln("</div>");
document.writeln("<style type=\"text/css\">");
document.writeln("#kfivtwin{display:none!important;}");
document.writeln("#serviceWin{width:400px;height:228px;position:absolute;top:240px;z-index:10000;}");
document.writeln("#serviceWin #sw{width:400px;height:228px;background:url(http://www.aishangfei.com/Public/images/sw/sw_bg.gif) no-repeat;position:relative;}");
document.writeln("#serviceWin #sw_close{width:19px;height:19px;position:absolute;top:10px;right:9px;cursor:pointer;}");
document.writeln("#serviceWin #sw_open{width:113px;height:29px;position:absolute;top:142px;right:58px;cursor:pointer;}");
document.writeln("#serviceWin #sw_tel{display:block;width:180px;height:24px;line-height:24px;font-size:21px;font-weight:bold;font-family:\"黑体\";color:#ff7800;position:absolute;bottom:10px;left:175px;}");
document.writeln("</style>");

//判断是否已加载jQuery库，如果没自行加载
if(typeof jQuery == 'undefined'){ 
document.write ('<script type="text/javascript" src="http://www.aishangfei.com/Public/js/jquery-1.8.3.min.js"></script>');
}

document.write ('<script type="text/javascript">');
document.writeln("var left=($(window).width()-400)/2 +\'px\'");
document.writeln("$(\'#serviceWin\').css({\'left\':left});");
document.writeln("var tel=$(\'#swjs\').attr(\'tel\');");
document.writeln("$(\'#sw_tel\').text(tel);");
document.writeln("");
document.writeln("$(function(){");
document.writeln("	$(window).scroll(function(){");
document.writeln("		var top=$(this).scrollTop()+240+\'px\';");
document.writeln("		$(\'#serviceWin\').css(\'top\',top);");
document.writeln("	});");
document.writeln("	$(\'#sw_open\').click(function(){");
document.writeln("		hz6d_cus_web_msg_open();");
document.writeln("		$(\'#serviceWin\').hide();");
document.writeln("	});");
document.writeln("	$(\'#sw_close\').click(function(){");
document.writeln("		$(\'#serviceWin\').hide();");
document.writeln("	});");
document.writeln("});");
document.write ('</script>');
