var js_path=getJsPath()+"source/";
$(function(){
    $(".submit").click(function(){
        //验证表单是否正确
        $this=$("#reg_form");
        var state=0;
        var lis=$this.find("ul li");
        lis.each(function(i){if($(this).attr("val")=="1"){
            state=1;
            var pro=$(this).find(".pro");
            if(pro.html()==""){pro.html("<img src='"+js_path+"alterError.gif' /><a>输入有误</a>");}
            if(i==3){if($(this).find(".pro input").length>0){state=2;}}
            return false;
        }
        });
        if(state==1){alert("提交信息不正确，请检查！");return false;}
        if(state==2){alert("请点击认证手机号！");return false;}

        var re= /^1[3|4|5|8][0-9]\d{4,8}$/;
        var value=$('#reg_ph').val();
        var pro=$('#reg_ph').next();
        if(!re.test(value)){
            $('#reg_ph').parent().attr("val","1");
            pro.html("<img src='"+js_path+"alterError.gif' /><a>请填入正确的手机号</a>");
            return false;
        }

        var cdiv=$("<div class='cover_" +
            "pop_yzk'>");
        var div=$("<div class='phone_pop_yzk'>");
        var html='<h2 class="h1"><span>认证手机号</span><a class="close" title="关闭"></a></h2><h3>爱尚飞国际机票已向您的手机发送校验码,请在下方输入校验码,完成验证。</h3><ul><li><span class="sp0">手机号：</span><span class="phone">'+value+'</span></li><li><span class="sp0"><label class="red">*</label>校验码：</span><span><input class="text" type="text"/></span></li></ul><h4 class="hr"></h4><div class="fs"><label class="fslb">1分钟内没收到校验码？</label><a href="javascript:;" class="active">重发认证码短信（60s）</a></div><div class="sub"><a class="sub" href="javascript:;">下一步</a></div>';
        div.html(html);
        div.css({left:($(window).width()-440)/2+"px",top:"150px"});
        div.appendTo($("body"));
        cdiv.appendTo($("body"));
        div.find(".close").click(function(){div.remove();cdiv.remove();});
        phone_ajax(value,-1);
        var t=60;
        var a=div.find("div.fs a")[0];
        changeHtml(a,t);
        $(a).click(function(){
            if(a.className!="fsa"){return;}
            phone_ajax(value,-1);
            a.className="active";
            changeHtml(a,t);
            return false;
        });
        div.find("div.sub .sub").click(function(){
            phone_ajax(-1,div.find(".text").val(),function(){
                div.remove();cdiv.remove();
                $('#reg_ph').parent().attr("val",value);
                pro.html("<img src='"+js_path+"alterRight.gif' />");
                $this.submit();
            });
        });
    });

	$("#qiehuan_qb").click(function(){
		var $this=$(this);
		if($(this).hasClass("active")){
			$("#qb_ul").slideUp(300,function(){$this.removeClass("active");});
		}else{
			$("#qb_ul").slideDown(300,function(){$this.addClass("active");});
		}
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
						opt=opt+"<option value="+i+">"+name+"</option>";
					}
					prov.html(opt);
					changeProv(0);
					prov.change(function(){
						changeProv(Number($(this).val()));
					});
				},
				error:function(){prov.attr("state","1");}
			});
		}
	});
	//用户名提示消失
	$("#reg_id").click(function(){
		if($(this).attr("state")!="1"){
			$(this).val("");
			$(this).css("color","#000");
			$(this).attr("state",1);	
		}
	});
	$("#reg_ps").click(function(){
		if($(this).attr("state")!="1"){
			$(this).val("");
			$(this)[0].type="password";
			$(this).css("color","#000");
			$(this).attr("state",1);	
		}
	});
	//验证用户名
	$("#reg_id").blur(function(){
		var value=$(this).val();
		var re1 = /^[A-Za-z\d @.*&!#$(),?\/\\\-_=+%^><:;"'|{}\[\]’”：；`~，。、？》《）（……￥！~·——]+$/;//\u4E00-\u9FA5
		var re2 = /(^\s)|(\s$)/;
		var pro=$(this).next();
		var procont="";
		procont=value==""?"不能为空":re2.test(value)?"开始或结束不能含空字符":value.length<6?"不能小于6个字符":value.length>20?"不能大于20个字符":(!re1.test(value))?"不能含中文或个别符号":"";
		if(procont!=""){$(this).parent().attr("val","1");pro.html("<img src='"+js_path+"alterError.gif' /><a>"+procont+"</a>");if(value==""){$(this).attr("state",0);$(this).val("6~20位字符，数字，字母组合");$(this).css("color","#666");}}
		else{
			//验证调用
			var url=checkname;//这里是验证php文件
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
		if(procont!=""){$(this).parent().attr("val","1");pro.html("<img src='"+js_path+"alterError.gif' /><a>"+procont+"</a>");if(value==""){$(this).attr("state",0);$(this)[0].type="text";$(this).val("6~18位字符，数字，字母组合");$(this).css("color","#666");}}
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
			var $this=$(this);
			var url=checkphone;//这里是验证php文件
			if($this.parent().attr("val")==value){pro.html("<img src='"+js_path+"alterRight.gif' />");return;}
			$this.parent().attr("val","1");
			verify(pro,$(this),value,url,function(){
                pro.html("<img src='"+js_path+"alterRight.gif' />");
                $this.parent().attr("val",value);
			});
		}
	});

    $("#reg_ph2").blur(function(){
        var re= /^1[3|4|5|8][0-9]\d{4,8}$/;
        var value=$(this).val();
        var pro=$(this).next();
        if(!re.test(value)){$(this).parent().attr("val","1");pro.html("<img src='"+js_path+"alterError.gif' /><a>请填入正确的手机号</a>");}
        else{
            //验证调用
            var $this=$(this);
            var url=checkphone;//这里是验证php文件
            if($this.parent().attr("val")==value){pro.html("<img src='"+js_path+"alterRight.gif' />");return;}
            $this.parent().attr("val","1");
            verify(pro,$(this),value,url,function(){
                pro.html('<input type="button" value="验证手机号" class="but" id="yz_phone" />');
                pro.find("input").click(function(){
                    phone_ajax(value,-1);
                    var cdiv=$("<div class='cover_pop_yzk'>");
                    var div=$("<div class='phone_pop_yzk'>");
                    var html='<h2 class="h1"><span>认证手机号</span><a class="close" title="关闭"></a></h2><h3>爱尚飞国际机票已向您的手机发送校验码,请在下方输入校验码,完成验证。</h3><ul><li><span class="sp0">手机号：</span><span class="phone">'+value+'</span></li><li><span class="sp0"><label class="red">*</label>校验码：</span><span><input class="text" type="text"/></span></li></ul><h4 class="hr"></h4><div class="fs"><label class="fslb">1分钟内没收到校验码？</label><a href="javascript:;" class="active">重发认证码短信（60s）</a></div><div class="sub"><a class="sub" href="javascript:;">下一步</a></div>';
                    div.html(html);
                    div.css({left:($(window).width()-440)/2+"px",top:"150px"});
                    div.appendTo($("body"));
                    cdiv.appendTo($("body"));
                    div.find(".close").click(function(){div.remove();cdiv.remove();});
                    var t=60;
                    var a=div.find("div.fs a")[0];
                    changeHtml(a,t);
                    $(a).click(function(){
                        if(a.className!="fsa"){return;}
                        phone_ajax(value,-1);
                        a.className="active";
                        changeHtml(a,t);
                        return false;
                    });
                    div.find("div.sub .sub").click(function(){
                        phone_ajax(-1,div.find(".text").val(),function(){
                            div.remove();cdiv.remove();
                            $this.parent().attr("val",value);
                            pro.html("<img src='"+js_path+"alterRight.gif' />");
                        });
                    });
                });
            });
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
				if(i==3){if($(this).find(".pro input").length>0){state=2;}}
				return false;
			}
		});
		if(state==1){alert("提交信息不正确，请检查！");return false;}
		if(state==2){alert("请点击认证手机号！");return false;}
	});
	//弹出服务条款
	$("#readService").click(function(){
		var readDiv=$("#readSerDiv");
		if(readDiv.length>0){
			if(readDiv.css("display")=="none"){
				readDiv.slideDown(300);	
			}else{
				readDiv.slideUp(300);	
			}
			return false;	
		}
		readDiv=$("<div id='readSerDiv' class='readSerDiv'></div>");
		readDiv.appendTo($(this).parent());
		$.ajax({
			url:js_path+'service.txt',
			success:function(msg){
				readDiv.html(msg);
			},
			error:function(){readDiv.html("<<爱尚飞国际机票网服务条款>>");}
		});
		readDiv.slideDown(300);
		return false;
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
				opt=opt+"<option value="+i+">"+name+"</option>";
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
//ajax手机发送验证码
function phone_ajax(phone,num,fn){//phone是手机号,num是认证码,fn是回调函数
	var data=phone==-1?"num="+num:"phone="+phone;
	$.ajax({
		url:checkphone,//这里是发送url
		data:data,
		success:function(msg){
			if(msg==1){if(fn&&typeof fn==="function")fn();}//自己定义正确返回
			else{
				if(phone==-1){alert("验证码不正确，请检查！");}
				else{
                    //if(fn&&typeof fn==="function")alert("发送验证码失败，请重试！");
                }
			}
		},
		error:function(){alert("网络出错，请重试!");}
	});
}
//倒数60秒
var changeHtml=function(o,t){
	o.innerHTML="重发验证码短信("+(--t)+"s)";
	var timer=setTimeout(function(){
		if(t==0){o.className="fsa";o.innerHTML="重发验证码短信";clearTimeout(timer);return;}
		changeHtml(o,t);
	},1000);
}