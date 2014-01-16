var js_path=getJsPath()+"source/";
$(function(){
	$("#qiehuan_qb").click(function(){
		var $this=$(this);
		$("#qb_ul").toggle(300,function(){
			if($this.hasClass("active")){
				$this.removeClass("active");
			}else{$this.addClass("active");}
			if($("#province_sel").attr("state")!="1"){
				var prov=$("#province_sel");
				$.ajax({
					url:js_path+"cityList.xml",
					dataType:"xml",
					success:function(msg){
						prov.attr("state","1");
						var loc=$(msg).find("Location");
						var opt="";
						var states=loc.find("CountryRegion").eq(0).find("State");
						for(var i=0;i<states.length;i++){
							var name=states.eq(i).attr("Name");
							opt=opt+"<option value="+name+" index="+i+" code="+states.eq(i).attr("Code")+">"+name+"</option>";
						}
						prov.html(opt);
						changeProv(0);
						prov.change(function(){
							changeProv(Number($(this).find("option:selected").attr("index")));
						});
					},
					error:function(){prov.attr("state","1");}
				});
			}
		});
	});
        //验证用户名
        $("#reg_id").blur(function(){
            var value=$(this).val();
            var re1 = /^[A-Za-z\d @.*&!#$(),?\/\\\-_=+%^><:;"'|{}\[\]’”：；`~，。、？》《）（……￥！~·——]+$/;//\u4E00-\u9FA5
            var re2 = /(^\s)|(\s$)/;
            var pro=$(this).next();
            var procont="";
            procont=value==""?"不能为空":re2.test(value)?"开始或结束不能含空字符":value.length<2?"不能小于2个字符":value.length>30?"不能大于30个字符":(!re1.test(value))?"不能含中文或个别符号":"";
            if(procont!=""){$(this).parent().attr("val","1");pro.html("<img src='"+js_path+"alterError.gif' /><a>"+procont+"</a>");}
            else{
				//验证调用
				var url="";//这里是验证php文件
				verify(pro,$(this),value,url);
			}
        });
        //验证密码
        $("#reg_ps").blur(function(){
            var value=$(this).val();
            var re1 = /^[A-Za-z\d@.*&!#$(),?\/\\\-_=+%^><:;"'|{}\[\]]+$/;
            var re2 = /^[\d]+$/;
            var pro=$(this).next();
            var procont="";
            procont=value==""?"不能为空":re2.test(value)?"不能全部为数字":value.length<6?"不能小于6个字符":value.length>50?"不能大于50个字符":(!re1.test(value))?"不能含中文或个别符号":"";
            if(procont!=""){$(this).parent().attr("val","1");pro.html("<img src='"+js_path+"alterError.gif' /><a>"+procont+"</a>");}
            else{
                $(this).parent().removeAttr("val");pro.html("<img src='"+js_path+"alterRight.gif' />");}
        });
        //确认密码
        $("#reg_rps").blur(function(){
            var value=$(this).val();
            var pro=$(this).next();
            if(value!=$("#reg_ps").val()||value==""){$(this).parent().attr("val","1");pro.html("<img src='"+js_path+"alterError.gif' /><a>密码确认有误</a>");}
            else{$(this).parent().removeAttr("val");pro.html("<img src='"+js_path+"alterRight.gif' />");}
        });
        //验证手机号
        $("#reg_ph").blur(function(){
            var re= /^1[3|4|5|8][0-9]\d{4,8}$/;
            var value=$(this).val();
            var pro=$(this).next();
            if(!re.test(value)){$(this).parent().attr("val","1");pro.html("<img src='"+js_path+"alterError.gif' /><a>请填入正确的手机号</a>");}
            else{
				//验证调用
				var url="";//这里是验证php文件
				verify(pro,$(this),value,url);	
			}
        });
        //验证码
        $("#reg_ver").blur(function(){
            var pro=$(this).nextAll("label").eq(0);
            //这里留空，等待ajax查询当前验证码是否准确
            if($(this).val()==""){$(this).parent().attr("val","1");pro.html("<img src='"+js_path+"alterError.gif' /><a>请填入验证码</a>");}
            else{
				//验证调用
				var url="";//这里是验证php文件
				verify(pro,$(this),$(this).val(),url);	
			}
        });
        //阅读协议
        $("#reg_check").change(function(){
            if($(this)[0].checked==false){$(this).parent().attr("val","1")}
            else{$(this).parent().attr("val","0");}
        });
        $("#reg_form").submit(function(){
            var state=0;
            var lis=$(this).find("ul li");
            lis.each(function(i){if($(this).attr("val")=="1"){
                    state=1;
                    var pro=$(this).find(".pro");
                    if(pro.html()==""){pro.html("<img src='"+js_path+"alterError.gif' /><a>输入有误</a>");}
                    return false;
                }
            });
            if(state==1){alert("提交信息不正确，请检查！");return false;}
        });
});
function changeProv(n){
	var city=$("#city_sel");
	$.ajax({
		url:js_path+"cityList.xml",
		dataType:"xml",
		success:function(msg){
			var loc=$(msg).find("Location");
			var opt="";
			var citys=loc.find("CountryRegion").eq(0).find("State").eq(n).find("City");
			for(var i=0;i<citys.length;i++){
				var name=citys.eq(i).attr("Name");
				opt=opt+"<option value="+name+" index="+i+" code="+citys.eq(i).attr("Code")+">"+name+"</option>";
			}
			city.html(opt);
		},
		error:function(){}
	});
}
function getJsPath(){
    var result="";
    var es =document.getElementsByTagName("script");
    for (var i = 0; i < es.length; i++)
    {
        var path=es[i].src;
        if (path.indexOf("register.js")!=-1)
        {
            result = path.substring(0,path.lastIndexOf("/") + 1);
        }
    }
    return result;
}

//ajax 验证
function verify(pro,$this,value,url){
	pro.html("<img src='"+js_path+"loading.gif' />");
	$.ajax({
		url:url,//这里是验证php文件
		data:"d="+value,
		success:function(msg){//正确1,错误的，最好是错误信息，比如用户名已存在，手机号已绑定等。
			if(msg==1){//这里表示正确，可以自己定义返回数据。
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
