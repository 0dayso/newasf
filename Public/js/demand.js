$(function(){
	
	cityDate();
	
	//调用城市名和出发日期
	function cityDate(){
	$("#demand_form").find("ul").each(function(i){
		$(this).find(".city").each(function(j){
		$(this).click(function(){
            textInputDIs($(this));
			if(j==0){popCityList($(this)[0]);}
			if(j==1){popCityList($(this)[0],"guowai");}
		})
		});
	});
	$("#demand_form").find("li .date").each(function(i){
		$(this).click(function(){
            textInputDIs($(this));
			WdatePicker({minDate:'%y-%M-%d',doubleCalendar:true});
		});
	});
	}
	function textInputDIs(dom){
            if(dom.attr("state")!="1"){
                dom.css("color","#333");
                dom.val("");
                dom.attr("state","1");
            }
        }
		
	//添加航程
	$("#df_add").click(function(){
		var ul = "",i = parseInt($(this).prev().attr("no"))+1;
		ul += "<ul  no='" + i + "'><li>";
        ul += "<span>出发城市" + i + "：</span>";
        ul += "<input type='text' class='text city' name='origincode[]' value='城市名'/>";
		ul += "</li><li>";
        ul += "<span>到达城市" + i + "：</span>";
        ul += "<input type='text' class='text city' name='desinationcode[]' value='城市名'/>";
        ul += "</li><li>";
        ul += "<span>出发日期" + i + "：</span>";
        ul += "<input type='text' class='text2 date' name='originDate[]' value='出发时间' />";
		ul += "</li><li>";
		ul += "<a href='javascript:;' class='df_del'><em>X</em>删除</a>";
		ul += "</li></ul>"
		$(this).before(ul);
		cityDate();
		dfDel();
		});
	
	dfDel();
	
	//删除航程
	function dfDel(){
		$("#hc_xinxi").find(".df_del").each(function(i){
			$(this).click(function(){
				$(this).parents('ul').remove();
			  });
		});
	}
	
	inp_val('#phone','11位数字');
	inp_val('#area','区号');
	inp_val('#phone_no','电话号码');
	inp_val('#ext','分机号');
	function inp_val(id,txt){
		$(id).focus(function(){
			if($(this).val()==txt){
				$(this).val("");
				}
			}).blur(function(){
				if($(this).val()==""){
				$(this).val(txt);
				}
				});
		 }
	
	//表单验证
	$("#demand_form").submit(function(){
		if(adult()==0){return false;};
		if($("input:radio[name='type']:checked").val()==null){alert("请选择乘客类型！");return false;};
		if($("input:radio[name='grade']:checked").val()==null){alert("请选择舱位等级！");return false;};
		if(linkman()==0){return false;};
		if(phone()==0){return false;};
		});
	function adult(){
		var value = $("input[name='adult']").val();
		var re=/^[\d]+$/;
		var txt=value==""?"成人乘客人数不能为空！":value==0?"成人乘客人数不能为零!":!re.test(value)?"乘客人数请填入数字！":"";
		if(txt!=""){alert(txt);return false;}
		}
    function linkman(){
		var value = $("input[name='linkman']").val();
		var re=/^[\d]+$/;
		var txt=value==""?"联系人姓名不能为空！":re.test(value)?"联系人姓名不能全为数字！":value.length<2?"联系人姓名不能2小于个字符!":"";
		if(txt!=""){alert(txt);return false;}
		}
	function phone(){
		var value = $("input[name='phone']").val();
		var re= /^1[3|4|5|8][0-9]\d{4,8}$/;
		var txt=value=="11位数字"?"手机号码不能为空！":!re.test(value)?"请填入正确的手机号！":"";
		if(txt!=""){alert(txt);return false;}
		}
	 
    });