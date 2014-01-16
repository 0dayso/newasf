var defaultAddress="城市名";//页面默认的地址
var defaultTime="YYYY-M月-DD,YYYY-MM-DD";//页面默认的日期
	
var fromTips="请选择出发地";
var toTips = "请选择到达地";
var fromDateTips = "请选择启程时间";
var returnDateTips = "请选择回程时间";

var flightinfostb="";//存放行程搜索条件的中文描述

//将参数格式化，根据参数要确定城市or机场，参数中包含城市就是城市，否则是机场
function paramformart(objparam)
{
		var formartparam="";
		var paramtype="";
		var objparamval=$(objparam).val();
		if(objparamval!=defaultAddress)
		{
			var rpobj=objparamval.replace(/^\s+|\s+$/g,"");//去掉头尾的空格
				formartparam=rpobj.substring(rpobj.indexOf("(")+1,rpobj.indexOf(")"));
				
				if(rpobj!="")
				{
					paramtype=rpobj.indexOf("城市")>=0 ? "1":"0";
					
					formartparam+="_"+paramtype;
				}
		}
		if(formartparam.length!=5)
		{
			tipIfo("#"+$(objparam).attr("id"),"#"+$(objparam).attr("id")+"span",false,"请输入合理的城市/机场!");
		}
		
		return formartparam;
}

//查询功能的请求
	function searChAagin()
	{
		var tempsuburl=subSearch();
		//var url="http://flights.aishangfei.net/s?"+tempsuburl+"&accessId=fly4free";
        var url=BaseInfo.path+"iflight/flightquery/?"+tempsuburl+"&accessId=fly4free";
		if(tempsuburl!="")
		{
			location.href=encodeURI(url);
		}
	}

//行程信息校验
function subSearch()
{
		lastisSub();//最后一组行程的处理
		$("span[id$='span']").each
		(
			function()
			{
				$(this).html("");
			}
		);
		
		var actionAddress="";//请求提交的地址
		var flightType=$("#radio_sel input:checked").val();//行程类别(往返:1  单程:2  联程:3)
		var directFlightsOnly="2";
		$("input[type='checkbox'][name='directFlightsOnly']").each
		(
			function()
			{
				if($(this).attr("checked")=="checked")
					directFlightsOnly=$(this).val();
			}
		);
		
		var fromcookieparam="";//去程三字码
		var tocookieparam="";//回程三字码
		var fromdatecookieparam="";//启程时间
		var todatecookieparam="";//回程时间
		
	//	var tickTypeparam=$("select[name='tickType']").val();////机票类型(普通 0,学生 1,儿童 2)
	//	var childnum=$("select[name='childnum']").val();//儿童数量
   //  var personnum=$("select[name='personnum']").val();//成人数量
         var tickTypeparam='ADT';////机票类型(普通 0,学生 1,儿童 2)
         var childnum=0;//儿童数量
		var personnum=1;//成人数量
		
		actionAddress+="flightType="+flightType+"&tickType="+tickTypeparam+"&personNum="+personnum+"&childNum="+childnum
			+"&directFlightsOnly="+directFlightsOnly;

		if((flightType==2 && !checkSingle()) ||(flightType==1 && !checkRound())
			|| (flightType==3 && !checkOpenJaw()) || (flightType<1 || flightType>3))
		//	return "";
		
		var codeisboolean=true;
		for(var index =0;index<3;index++)
		{
			if(index==2 && !isTripLegal("#from3","#to3","FromDate3"))// 第三段不合法
				break;
				
			var bltemp=isAgainaddress(document.getElementsByName("origincode")[index],document.getElementsByName("desinationcode")[index]);

			if(bltemp==false)
				return "";
				
			codeisboolean=paramformart(document.getElementsByName("origincode")[index]).length==5&&codeisboolean?true:false;
			codeisboolean=paramformart(document.getElementsByName("desinationcode")[index]).length==5&&codeisboolean?true:false;	
				
			var origincodeB=paramformart(document.getElementsByName("origincode")[index]);

            var origin_name=document.getElementsByName("origincode")[index].value;
            var desination_name=document.getElementsByName("desinationcode")[index].value;

			var desinationcodeB=paramformart(document.getElementsByName("desinationcode")[index]);// 到达城市/机场代码
			
			var originDateB=initDataFormart(document.getElementsByName("originDate")[index].value);
			fromcookieparam+="+"+origincodeB;					
			actionAddress+="&originCode="+origincodeB;
			tocookieparam+="+"+desinationcodeB;
			actionAddress+="&desinationCode="+desinationcodeB;
			fromdatecookieparam+="+"+originDateB;
			actionAddress+="&originDate="+originDateB;

            actionAddress+='&origin_name='+origin_name+'&desination_name='+desination_name;
			if(flightType!=3)
				break;
		}
		var returnDate=$("#returnDate").val();//返程日期
		if(flightType==1)
		{
			todatecookieparam=returnDate;
			actionAddress+="&returnDate="+returnDate;
		}
		var persontype=personnum+"+"+childnum;//乘客类型信息
		if(fromcookieparam!="")
			fromcookieparam = fromcookieparam.substring(1,fromcookieparam.length);
		if(tocookieparam!="")
			tocookieparam = tocookieparam.substring(1,tocookieparam.length);
		if(fromdatecookieparam!="")
			fromdatecookieparam = fromdatecookieparam.substring(1,fromdatecookieparam.length);
		var fromobjparam=fromcookieparam+"|"+tocookieparam+"|"+fromdatecookieparam;//三个参数拼在一起
		fromobjparam+="|"+todatecookieparam;	
		var cookstr=flightType+"&"+persontype+"&"+fromobjparam+"&"+tickTypeparam+"&"+directFlightsOnly;
		if(codeisboolean==true)
		{
		//	addCookie("historyparam",cookstr);
		}

		actionAddress=codeisboolean==true?actionAddress:"";
		return actionAddress;
}

//最后一组(联程)行程信息处理
function lastisSub(){
	var lastobjIssub=$("#addbutul").css("display");//值的范围:none和block
	
	if(lastobjIssub=="none")//如果删除了最后一组行程的话就清空最后一组行程信息
	{
		$("#addbutul").find("input[type='text']").each
		(
			function()
			{
				$(this).val("");
			}
		);	
	}
}

	//地址是否合法
	function isTripLegal(fromid,toid,dateid)
	{
		return !isAddressEmptyOrDefault($(fromid).val()) &&
			   !isAddressEmptyOrDefault($(toid).val())   &&
			   !isEmptyOrDefault($(dateid).val());
	}
	
	//检查单程数据
	function checkSingle()
	{
		return checkTrip("#from1","#to1","#FromDate1");
	}
	
	//检查往返数据
	function checkRound()
	{
		var ret = checkSingle();
		if(isEmptyOrDefault($("#returnDate").val()))
		{
			tipIfo("#returnDate","#returnDate"+"span",false,returnDateTips);
			ret = false;
		}
		if(ret)
		{
			var tempfromdate=$("#FromDate1").val().split("-");
			var tempreturndate=$("#returnDate").val().split("-");
			var	lvDate 	= new Date(tempfromdate[0],tempfromdate[1],tempfromdate[2]);
			var	arvDate	= new Date(tempreturndate[0],tempreturndate[1],tempreturndate[2]);
			if(lvDate>arvDate)
			{
				tipIfo("#returnDate","#returnDate"+"span",false,"回程时间应该在启程日期之后");
				ret = false;
			}
		}
		return ret;
	}
	
	//检查联程数据
	function checkOpenJaw()
	{
	   		var ret = true;
	   		ret = ret & checkTrip("#from1","#to1","#FromDate1");
	   		ret = ret & checkTrip("#from2","#to2","#FromDate2");
	   		if(ret) {
				var tempfromdate = $("#FromDate1").val().split("-");
				var tempreturndate = $("#FromDate2").val().split("-");
				var lvDate = new Date(tempfromdate[0], tempfromdate[1], tempfromdate[2]);
				var arvDate = new Date(tempreturndate[0], tempreturndate[1], tempreturndate[2]);				
				if(lvDate > arvDate) {
					tipIfo("#FromDate2", "#FromDate2" + "span", false, "第二段应该在第一段日期之后");
					ret = false;
				}				
			}
			return ret;
	}
	
	//检查页面提交的数据是否合法,合法的意思是时间不能为空且不能为默认，出发地和目的地同样不能为空和默认值
	function checkTrip(fromid,toid,dateid)
	{
		var ret = true;
		if(isAddressEmptyOrDefault($(fromid).val()))
		{
			tipIfo(fromid,fromid+"span",false,fromTips);
			ret = false;
		}
		if(isAddressEmptyOrDefault($(toid).val()))
		{
			tipIfo(toid,toid+"span",false,toTips);
			ret = false;
		}
		if(isEmptyOrDefault($(dateid).val()))
		{
			tipIfo(dateid,dateid+"span",false,fromDateTips);
			ret = false;
		}
		return ret;
	}
	
	
	//将三字码转成有意义的文字描述
	function initFindparam(paramobj,origincode,dateparam,dateparam_return)
	{
		var rshtml="";
		var temphtml="";
			$.ajax({
				url:encodeURI("http://"+host+"/AutoComplete/u.htm?k="+origincode.replace("_","")),
				type:"get",
				async:false,
				dataType:"json",
				success:function(date)
						{
							var param=date.r;
							
							if(param.length>0)
							{
								if(origincode.substring(origincode.indexOf("_")+1,origincode.length)=="1")//城市
								{
										rshtml+=param[0].city+ "(" + param[0].code+")," + param[0].country + " 城市";
										temphtml+=param[0].city+ "(" + param[0].code+")";
										paramobj.value=rshtml;
								}	
								if(origincode.substring(origincode.indexOf("_")+1,origincode.length)=="0")//机场
								{
										rshtml+=param[0].city+ "("+param[0].code+")," + param[0].airport+","+param[0].country+" 机场";
										temphtml+=param[0].city+ "(" + param[0].code+")";
										paramobj.value=rshtml;
								}	
								
								if($(paramobj).attr("name")=="origincode")
								{
									if(document.getElementById('showfindparam')!=undefined)
									{
										if(null!=dateparam)
										{
											var textHtml="<b>从</b>&nbsp; <font style='color:#f60;'>"+temphtml+"</font>&nbsp;&nbsp;";
											flightinfostb+=textHtml;
											$(textHtml).appendTo('#showfindparam');
										}
										else 
										{
											var textHtml="<b>&nbsp;到</b>&nbsp; <font style='color:#f60;'>"+temphtml+"</font>&nbsp;&nbsp;；<font  style='color:#f60;'>"+dateparam_return+"</font>；<br/>";
											flightinfostb+=textHtml;
											$(textHtml).appendTo('#showfindparam');
										}
									}
								}
								if($(paramobj).attr("name")=="desinationcode")
								{
									if(document.getElementById('showfindparam')!=undefined)
									{
										if(null!=dateparam)
										{
											var textHtml="<b>&nbsp;到</b>&nbsp; <font style='color:#f60;'>"+temphtml+"</font> ；<font  style='color:#f60;'>"+dateparam+"</font>；<br/>";
											flightinfostb+=textHtml;
											$(textHtml).appendTo('#showfindparam');
										}
										else
										{
											var textHtml="<b>从</b>&nbsp; <font style='color:#f60;'>"+temphtml+"</font>&nbsp;&nbsp;";
											flightinfostb+=textHtml;
											$(textHtml).appendTo('#showfindparam');
										}
									}
								}
								return rshtml;
							}
						}
				});
		return rshtml;
	}
	
	
/*
 * 联程新增/删除最后一程
 */
function chanageMenu(elementobj,bl)
{
	if(bl)
	{
		$("#"+$(elementobj).attr("id")+"ul").css("display","");
		$("#minusdiv").html("<input type='button' id='addbut'  class='addbut' onclick='chanageMenu(this,false)' value='- 减少一程'/>");
		$(elementobj).parent().html("");
		return;
	}
	else
	{
		$("#"+$(elementobj).attr("id")+"ul").css("display","none");
		$(elementobj).parent().html("<input type='button' id='addbut'  class='addbut' onclick='chanageMenu(this,true)' value='+ 添加一程'/>");
		return;
	}
}


