var js_path=getJsPath()+"source/";
$(function(){
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
	$("#fh_name").blur(function(){
		var value=$(this).val();
		var pro=$(this).next();
		var re = /(^\s)|(\s$)/;
		var procont=value==""?"不能为空":re.test(value)?"开始或结束不能含空字符":"";
		if(procont!=""){$(this).parent().attr("val","1");pro.html("<img src='"+js_path+"alterError.gif' /><a>"+procont+"</a>");}
		else{$(this).parent().removeAttr("val");pro.html("<img src='"+js_path+"alterRight.gif' />");}
	});
	
	$("#fh_phone").blur(function(){
		var re= /^1[3|4|5|8][0-9]\d{4,8}$/;
		var value=$(this).val();
		var pro=$(this).next();
		if(!re.test(value)){$(this).parent().attr("val","1");pro.html("<img src='"+js_path+"alterError.gif' /><a>请填入正确的手机号</a>");}
		else{$(this).parent().removeAttr("val");pro.html("<img src='"+js_path+"alterRight.gif' />");}
	});
	
	$("#fh_address").blur(function(){
		var value=$(this).val();
		var pro=$(this).next();
		var procont=value.length<5?"联系地址不能小于5个字符":"";
		if(procont!=""){$(this).parent().attr("val","1");pro.html("<img src='"+js_path+"alterError.gif' /><a>"+procont+"</a>");}
		else{$(this).parent().removeAttr("val");pro.html("<img src='"+js_path+"alterRight.gif' />");}
	});
	
	$("#fh_form").submit(function(){
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
        if (path.indexOf("confirmAdd.js")!=-1)
        {
            result = path.substring(0,path.lastIndexOf("/") + 1);
        }
    }
    return result;
}