var js_path=getJsPath()+"source/";
$(function(){
	//标题提示消失
	$("#tit_id").click(function(){
		if($(this).attr("state")!="1"){
			$(this).val("");
			$(this).css("color","#000");
			$(this).attr("state",1);	
		}
	});
	//描述提示消失
	$("#describe_id").click(function(){
		if($(this).attr("state")!="1"){
			$(this).val("");
			$(this).css("color","#000");
			$(this).attr("state",1);	
		}
	});
	//验证标题
	$("#tit_id").blur(function(){
		var value=$(this).val();
		var re = /(^\s)|(\s$)/;
		var pro=$(this).next();
		var procont="";
		procont=value==""?"不能为空":re.test(value)?"开始或结束不能含空字符":"";
		if(procont!=""){$(this).parent().attr("val","1");pro.html("<img src='"+js_path+"alterError.gif' /><a>"+procont+"</a>");if(value==""){$(this).attr("state",0);$(this).val("不能为空");$(this).css("color","#666");}}
		else{
			//验证通过
			pro.html("<img src='"+js_path+"alterRight.gif' />");
		}
	});
	//验证描述
	$("#describe_id").blur(function(){
		var value=$(this).val();
		var pro=$(this).next();
		var procont="";
		procont=value==""?"不能为空":value.length<5?"不能小于5个字符":"";
		if(procont!=""){$(this).parent().attr("val","1");pro.html("<img src='"+js_path+"alterError.gif' /><a>"+procont+"</a>");if(value==""){$(this).attr("state",0);$(this).val("不能为空,不少于5位字符");$(this).css("color","#666");}}
		else{
			//验证通过
			pro.html("<img src='"+js_path+"alterRight.gif' />");
		}
	});
	//验证E-mail
	$("#email_id").blur(function(){
		var value=$(this).val();
		var re1 = /^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/;
		var re2 = /(^\s)|(\s$)/;
		var pro=$(this).next();
		var procont="";
		procont=value==""?"请填入正确的邮箱":(!re1.test(value))?"请填入正确的邮箱":re2.test(value)?"开始或结束不能含空字符":"";
		if(procont!=""){$(this).parent().attr("val","1");pro.html("<img src='"+js_path+"alterError.gif' /><a>"+procont+"</a>");if(value==""){$(this).attr("state",0);$(this).css("color","#666");}}
		else{
			//验证通过
			pro.html("<img src='"+js_path+"alterRight.gif' />");
		}
	});
	//验证码
	$("#reg_ver").blur(function(){
		var pro=$(this).nextAll("label").eq(0);
		//这里留空，等待ajax查询当前验证码是否准确
		if($(this).val()==""){$(this).parent().attr("val","1");pro.html("<img src='"+js_path+"alterError.gif' /><a>请填入验证码</a>");}
		else{
			//验证调用
			var url=checkverify;//这里是验证php文件
			verify(pro,$(this),$(this).val(),url);	
		}
	});
	$("#reg_form").submit(function(){
		var state=0;
		var spans=$(this).find("span");
		alert(spans.length)
		return false;
		spans.each(function(i){
			if($(this).attr("val")=="1"){
				state=1;
				var pro=$(this).find(".pro");
				if(pro.html()==""){pro.html("<img src='"+js_path+"alterError.gif' /><a>输入有误</a>");}
				return false;
			}
		});
		if(state==1){alert("提交信息不正确，请检查！");return false;}
	});
	
});


//ajax 验证
function verify(pro,$this,value,url,fn){
	pro.html("<img src='"+js_path+"loading.gif' />");
	$.ajax({
		url:url,//这里是验证php文件
		data:"d="+value,
		success:function(msg){//正确1,错误的，最好是错误信息，比如用户名已存在，手机号已绑定等。
			if(msg==1){//这里表示正确，可以自己定义返回数据。
                if(fn&&typeof fn=="function"){fn();return;}
				pro.html("<img src='"+js_path+"alterRight.gif' />");		
				$this.parent().removeAttr("val");
			}
			else{
				pro.html("<img src='"+js_path+"alterError.gif' /><a>"+msg+"</a>");
			}
		},
		error:function(){pro.html("<img src='"+js_path+"alterError.gif' /><a>验证出错，请重试</a>");}	
	});
}
//
function getJsPath(){
    var result="";
    var es =document.getElementsByTagName("script");
    for (var i = 0; i < es.length; i++)
    {
        var path=es[i].src;
        if (path.indexOf("complaint.js")!=-1)
        {
            result = path.substring(0,path.lastIndexOf("/") + 1);
        }
    }
    return result;
}