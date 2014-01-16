var host=window.location.host;//取工程端口
	var tickinfolist="";//这是取出来的符合查询条件的所有机票信息
	var flightscodes="";//存放所有航空公司代码
	var httpheader="http://"+document.location.host;

	var historyurl="";//上个页面的搜索条件
	
	
	var isSub=true;//是否提交(这个值根据页面选择和内容填写的结果来确定)
	var paramisSub=true;//这个在做检查是做一个中间变量使用	
	
	var flighttemptype="0,1,";//行程运行类型(0:直达     1:中转)[注意,这个变量只能用在列表页的选择上。绝不可当做用户的查询条件]
	var flightLisBytype="";
	
	function isEmptyOrDefault(objparam)
	{
		return defaultTime.indexOf(objparam)>=0 || objparam =="";
	}
	function isAddressEmptyOrDefault(address)
	{
		if(address=="") return true;
		var regExp = /^[\u4e00-\u9fa5|\s/ig|A-Z|a-z]*\([A-Z]{3}\)/;//可以有中文，空格，字母
		return !regExp.exec(address);
	}



	//将传过来的值进行转化成日历控件需要的格式
	function initDataFormart(objparam)
	{
		var datestr="";
		if(!isEmptyOrDefault(objparam))
		{
			datestr=objparam;
		}	
		return datestr;
	}

	//进入页面时城市查询条件参数的判断
	function historyParam(historyparam)
	{
		if(isEmptyOrDefault(historyparam))
		{
			historyparam="";
		}
		return historyparam;
	}
	
	//进入页面时时间参数的判断
	function historyDateParam(historyparam)
	{
		if(!isEmptyOrDefault(historyparam))
		{
			historyparam="";
		}
		else
		{
			historyparam=initDataFormart(historyparam);
		}
		return historyparam;
	}
	
	function submit_chk(source)
	{
		var targdate=source.split("-");
		var d2=new Date(targdate[0]+"/"+targdate[1]+"/"+targdate[2]);
	  	var d=new Date();
	  	
	  	if(d2<d)//小于当前日期
	  	{
	  		return "smaller";
	  	}
	  	
	  	d.setMonth(d.getMonth() + 9);//日期加九个月
	  	
		if(d2>d)//大于当前日期后的6个月
		{
			return "greater";
		}
		
		return "ok";
	}
	
	//清空信息提示层
	function flushElement(elementId)
	{
		var param=$("#"+elementId+"span").html();
		if(param!="")
		{
			$("#"+elementId+"span").css("display","none");
		}
	}
	
	//显示提示信息
	function tipIfo(inputId,spanId,pass,str)
	{
		var wrongClass = "show_warn";
		var rightClass = "rightTip";
		var divLeft =  $(inputId).width()+35;
		var divTop =  -($(inputId).height()+10);
		
		style="position:absolute; margin:0px; padding:0px; z-index:100;background:url(front/images/warn_out.gif) no-repeat;  width:200px; height:26px; color:#56CAD7;text-indent:32px; line-height:26px;margin-left:213px;*+margin-left:0px;"+"margin-top:"+divTop+"px;*+margin-top:0px;*+float:none!importent;";
		var divparam="<div style='"+style+"'>"+str+"</div>";
		$(spanId).html(divparam).show();
	}
	
	
	
	
	
	
	function isAgainaddress(fromobj,toobj)//是否有重复地址(出发地与目的地不可重复)
	{
		var str1 = $(fromobj).val();
		var str2 = $(toobj).val();
		var patt = new RegExp(/\([A-Z]{3}\)/);
		var chk1 = patt.exec(str1);
		if(chk1[0]==patt.exec(str2))
		{
			tipIfo("#"+$(fromobj).attr("id"),"#"+$(fromobj).attr("id")+"span",false,"出发地不能与目的地相同!");
			tipIfo("#"+$(toobj).attr("id"),"#"+$(toobj).attr("id")+"span",false,"目的地不能与出发地相同!");
			return false;
		}
		return true;
	}
	

	//检查页面提交的数据是否合法（联程最后一组数据）
	function checkLastflight(errorNumparam)
	{
		var param=0;
		var str=new Array();
		if($("#from3").val()==""||$("#from3").val()==defaultAddress)
		{
			str.push("#from3_请选择出发地");
		}
		if($("#to3").val()==""||$("#to3").val()==defaultAddress)
		{
			str.push("#to3_请选择目的地");
		}
		if($("#FromDate3").val()==""||$("#FromDate3").val()==defaultTime)
		{
			str.push("#FromDate3_请选择启程时间");
		}
		
		if($("#from3").val()!=defaultAddress&&$("#from3").val()!="")
		{
			param++;
		}
		if($("#to3").val()!=defaultAddress&&$("#to3").val()!=defaultAddress!="")
		{
			param++;
		}
		if($("#FromDate3").val()!=defaultTime&&$("#FromDate3").val()!="")
		{
			param++;
		}
		
		if(param>0)
		{
			for(var i=0;i<str.length;i++)
			{
				var tempstr=str[i].split("_");
				tipIfo(tempstr[0],tempstr[0]+"span",false,tempstr[1]);
			}
		}
		return param;
	}
	
	
	
	

/**************************************cookie相关方法[开始]**********************************/
	
	
	
	
	
	function delCookie(name)
	{
		var date=new Date;
		date.setTime(date.getTime()-10000);
		document.cookie=name+"=a;expires="+date.toGMTString();
	}
/**************************************cookie相关方法[结束]**********************************/
	
function ckisnonstop(flen)
{
	var param=0;
	if(flen==1)
	{
		param=0;
	}
	if(flen>1)
	{
		param=1;
	}
	if(flighttemptype.indexOf(param+",")>-1)
	{
		return true;
	}
	return false;
}


//找出所有的不重复的航空公司代码
	function findFlightCode(flightcodeparam)
	{
		if(flightscodes.indexOf(flightcodeparam)<0)
		{
			flightscodes+=flightcodeparam+",";
			
			pageflightcmpparam+=flightcodeparam+",";//用于条件过滤时使用
		}
	}

//初始化首页数据
function initJsonHtml(param)//判断航班号是否被选中
{
	var bl = false;
	if (pageflightcmpparam.indexOf(param + ",") >= 0) {
		bl = true;
	}
	return bl;
}


	
	function clearThisReSearch(index)
	{
		jsonToHtml(tickinfolist,true);
	}
	
	function hrefurl(posid)
	{
		location.href=httpheader+"/infofilefarem?"+encodeURI(historyurl)+"&posid="+posid;
	}
	
	//更新行程报价
	function updateFlightPrice(src,targ,index)
	{
		var temp = src.list;
		var len =temp.length;
		
		if (len > 0) 
		{
			temp.splice(index-1,1);
			for (var va = 0; va < len; va++)// 循环报价组
			{
				if(temp[va].singleTotalFare>targ[0].singleTotalFare)
				{
					temp.splice(va,1,targ[0]);
					break;
				}
			}
		}
		
		src.list=temp;
		return src;
	}
	
	
	//新价格
	function newPricehtml(temp)
	{
			var len =temp.length;;
			var childrenPrice="";//儿童票价信息
       		var personPrice="";//成人票价信息	
			var pricediv = "";
			
			if(len>0)
			{
				var ordermoney=0;//定单总价
				
				var fareBreakdowns=temp[0].fareBreakdowns;//报价信息
				for(var a=0;a<fareBreakdowns.length;a++)
				{
					var priceobj=fareBreakdowns[a];
								
					if(priceobj.passengerCode=="ADT")
					{
						personPrice="<label>成人票价(单人):&yen;"+priceobj.singleBaseFare+"+&yen;"+ priceobj.singleTaxFare+" = &yen;"+priceobj.singleTotalFare+"</label>";
					}
					if(priceobj.passengerCode=="STU")
					{
						personPrice+="<label>学生票价(单人):&yen;"+priceobj.singleBaseFare+"+&yen;"+ priceobj.singleTaxFare+" = &yen; "+priceobj.singleTotalFare+"</label>";
					}
					if(priceobj.passengerCode=="YOU")
					{
						personPrice+="<label>青年票价(单人):&yen;"+priceobj.singleBaseFare+"+&yen;"+ priceobj.singleTaxFare+" = &yen; "+priceobj.singleTotalFare+"</label>";
					}
										
					if(priceobj.passengerCode=="CNN")
					{
						childrenPrice+="<label>儿童票价(单人):&yen;"+priceobj.singleBaseFare+"+&yen;"+ priceobj.singleTaxFare+" = &yen; "+priceobj.singleTotalFare+"</label>";
					}
				}
				pricediv =" <li>为了确保您预订实时占位成功；我们为您二次验证此价格：<br></li>"
	                     +"  <li>您所选择的航班调整了票价及税项，</li>"
	                     +"  <li>您将以如下实时新价格进行预订；</li>"
	                     +personPrice+childrenPrice;
			}
			return pricediv;
	}
	
	//老价格
	function olPriceFThtml(temp)
	{
		var prictPTC="";//价格折分的html
        var childrenPrice="<ul></ul>";//儿童票价信息
        var personPrice="";//成人票价信息	
		var fareBreakdowns=temp[0].fareBreakdowns;
		for(var a=0;a<fareBreakdowns.length;a++)
		{
			var priceobj=fareBreakdowns[a];
								
			if(priceobj.passengerCode=="ADT")
			{
				personPrice="<ul><li><font>成人</font>";
				personPrice+="<li>单人票价:&yen;  "+priceobj.singleBaseFare+"</li>" +
						"<li>+ 税金:&yen; "+priceobj.singleTaxFare+"</li><li>" +
							"<font>单人含税总价:&yen; "+priceobj.singleTotalFare+"</font></li><li>成人人数:"+priceobj.passengerQuantity+"</li></ul>";
			}
			if(priceobj.passengerCode=="STU")
			{
				personPrice+="<ul><li><font>学生</font>";
				personPrice+="<li>单人票价:&yen; "+priceobj.singleBaseFare+"</li>" +
						"<li>+ 税金:&yen; "+priceobj.singleTaxFare+"</li>" +
						"<li><font>单人含税总价:&yen; "+priceobj.singleTotalFare+"</font></li><li>成人人数:"+priceobj.passengerQuantity+"</li></ul>";
			}
			if(priceobj.passengerCode=="YOU")
			{
				personPrice+="<ul><li><font>青年</font>";
				personPrice+="<li>单人票价:&yen; "+priceobj.singleBaseFare+"</li>" +
						"<li>+ 税金:&yen; "+priceobj.singleTaxFare+"</li>" +
						"<li><font>单人含税总价:&yen; "+priceobj.singleTotalFare+"</font></li><li>成人人数:"+priceobj.passengerQuantity+"</li></ul>";
			}
								
			if(priceobj.passengerCode=="CNN")
			{
				childrenPrice="<ul><li><font>儿童</font>" +
						"</li><li>单人票价:&yen; "+priceobj.singleBaseFare+"</li>" +
						"<li>+ 税金:&yen; "+priceobj.singleTaxFare+"</li>" +
						"<li><font>单人含税总价:&yen; "+priceobj.singleTotalFare+"</font></li><li>儿童人数:"+priceobj.passengerQuantity+"</li></ul>";
			}
		}			
		prictPTC+=personPrice+childrenPrice;
		return prictPTC;
	}
	
	
	//设置航空公司
function createFligmentCmp(data)
{
		var tickparam=data;
		var len =tickparam.length;
					
		if(len>0)
		{
			for(var va=0;va<len;va++)//循环报价组
			{
				var originDestinationOptions=tickparam[va].originDestinationOptions;//报价单位
							
				for(var a=0;a<originDestinationOptions.length;a++)//循环方向组
				{
					var originParam=originDestinationOptions[a];//方向单位
					var flightsParam=originParam.flightSegments;//航班组
							
					for(var b=0;b<flightsParam.length;b++)
					{
							var flightParamObj=flightsParam[b];
							findFlightCode(flightParamObj.marketingCode+"&"+flightParamObj.marketingCn);
					}
				}
			}
						
		}
}
	
		//6-17新加
function newhidden()
{
  if(document.getElementById("otherAiritinerys").style.display=="none")
  {
	   document.getElementById("otherAiritinerys").style.display="block";  
	   document.images.imagelp1.src=httpheader+"/front/images/blueopen_icon2.gif";
  }
  else
  {
	   document.getElementById("otherAiritinerys").style.display="none";
	   document.images.imagelp1.src=httpheader+"/front/images/blueopen_icon.gif"; 
	   userOperate("otherflight");
  }
  
}
