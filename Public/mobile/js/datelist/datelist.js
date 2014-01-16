//调用日历
dateList();

function dateList(){
	//当天的日期和时间
	var newDate = new Date();
	//今天的日期
	var toDay = newDate.getDate();
	//用于存储月份名
	var monthNames = new Array("1月","2月","3月","4月","5月","6月","7月","8月","9月","10月","11月","12月");
	//定义节日
	var festivals = new Array();
	    festivals['11']='元旦';festivals['214']='情人节';festivals['38']='妇女节';festivals['312']='植树节';festivals['51']='劳动节';festivals['54']='青年节';festivals['61']='儿童节';festivals['81']='建军节';festivals['910']='教师节';festivals['101']='国庆';festivals['1224']='平安夜';festivals['1225']='圣诞节';
	
	for(i=0; i<5; i++){
		//累加(accumulation)月数
		var accMonth = newDate.getMonth()+i,year = newDate.getFullYear(),thisYear = new Date(),month,thisStartDay,html;
		html += '<div class="month">';
		if(accMonth > 11){
			year += 1;month = accMonth-12;
			
			//当前月的天数
			dayNumFun(year,month);
			//计算当前月份1号在星期几;
			thisYear.setFullYear(year,month,1);
			thisStartDay = thisYear.getDay();
			
			html += '<h1 class="month_name">'+year+'年'+monthNames[month]+'</h1>';
			html += '<ul class="day_list">';
			
			//输出空白
			for(k=0; k<thisStartDay; k++){
				html += '<li class="day_future"></li>';
				}//for
				
			for(j=1; j<=dayNum; j++){
				//输出节日
				var fesAttr = String(month+1)+String(j);
				if(festivals[fesAttr]){
				   html += '<li date="'+year+'-'+(month+1)+'-'+j+'" class="day_future valid have_txt festival"><em>'+j+'</em><i>'+festivals[fesAttr]+'</i></li>';
				   }else{
				   html += '<li date="'+year+'-'+(month+1)+'-'+j+'" class="day_future valid"><em>'+j+'</em></li>';
				   }//if
			   }//for
			
			}else{
				month = accMonth;
				//当前月的天数
				dayNumFun(year,month);
				//计算当前月份1号在星期几;
				thisYear.setFullYear(year,month,1);
				thisStartDay = thisYear.getDay();
				
				
				html += '<h1 class="month_name">'+year+'年'+monthNames[month]+'</h1>';
				html += '<ul class="day_list">';
				
				//输出空白
				for(k=0; k<thisStartDay; k++){
					html += '<li class="day_future"></li>';
					}//for
				
				if(i == 0){
					for(j=1; j<=dayNum; j++){
						if(j<toDay){
							html += '<li date="'+year+'-'+(month+1)+'-'+j+'" class="day_pass"><em>'+j+'</em></li>';
							}else{
								switch(j)
								{
									case toDay:
		                            html += '<li date="'+year+'-'+(month+1)+'-'+j+'" class="day_future have_txt valid"><em>'+j+'</em><i>今天</i></li>';
		                            break;
		                            case (toDay+1):
		                            html += '<li date="'+year+'-'+(month+1)+'-'+j+'" class="day_future have_txt valid"><em>'+j+'</em><i>明天</i></li>';
		                            break;
		                            case (toDay+2):
		                            html += '<li date="'+year+'-'+(month+1)+'-'+j+'" class="day_future have_txt valid"><em>'+j+'</em><i>后天</i></li>';
		                            break;
		                            default:
		                            //输出节日
									var fesAttr = String(month+1)+String(j);
									if(festivals[fesAttr]){
									   html += '<li date="'+year+'-'+(month+1)+'-'+j+'" class="day_future valid have_txt festival"><em>'+j+'</em><i>'+festivals[fesAttr]+'</i></li>';
									  }else{
										  html += '<li date="'+year+'-'+(month+1)+'-'+j+'" class="day_future valid"><em>'+j+'</em></li>';
									  }//if
							    }//switch
	                        }//if
                       }//for
					}else{
						for(j=1; j<=dayNum; j++){
							//输出节日
							var fesAttr = String(month+1)+String(j);
							if(festivals[fesAttr]){
							   html += '<li date="'+year+'-'+(month+1)+'-'+j+'" class="day_future valid have_txt festival"><em>'+j+'</em><i>'+festivals[fesAttr]+'</i></li>';
							  }else{
							  html += '<li date="'+year+'-'+(month+1)+'-'+j+'" class="day_future valid"><em>'+j+'</em></li>';
							  }//if
				           }//for
						}//if

				}//if
			
		html += '</ul>'
		html += '</div>'
		}//for
	
	//在页面输出日历
	$('#date_list').html(html);
	
	}
	
//计算每个月的天数
function dayNumFun(year,month){
	//js返回月份0代表1月，1代表2月
	if(month==0 || month==2 || month==4 || month==6 || month==7 || month==9 || month==11){
		  dayNum = 31;
	  }else if(month==3 || month==5 || month==8 || month==10){
		  dayNum = 30;
	  }else if(month==1 && isLeapYear(year)){
		  dayNum = 29;
	  }else{
		  dayNum = 28;
	  }
	return dayNum;
	}

//计算是否闰年
function isLeapYear(year){
	if((year%4==0 && year%100!=0)||(year%400==0)){
		return true;
	  }else{
		  return false;
      }
   }


//选择出发日期或返程日期
$(function(){
	
	//出发日期
	$('#js_departDate').click(function(){
		var type = $('#set_date').attr('type'),returnDate = "#js_departDate";
		var departDate = $('#js_departDate em').attr('date'),backDate = $('#js_backDate em').attr('date');
		setDateList(type,departDate,backDate,returnDate);
		});
	
	//返程日期
	$('#js_backDate').click(function(){
		var type = $('#set_date').attr('type'),returnDate = "#js_backDate";
		var departDate = $('#js_departDate em').attr('date'),backDate = $('#js_backDate em').attr('date');
		setDateList(type,departDate,backDate,returnDate);
		});
		
	//关闭日历
	$('#close_date_list').click(function(){
		$('#date_list_box').hide();
		dateList();
		});
		
});
	


	
//显示并选择日期
//type等于one为单程，two为往返
function setDateList(type,departDate,backDate,returnDate){
	$('#date_list_box').show();
	if(type=='one'){
		eachDateList(type,departDate,'depart');
	  }else if(type=='two'){
		eachDateList(type,departDate,'depart');
		eachDateList(type,backDate,'back'); 
	  }
	 
	 //计算如果选择返程日期，那出发日期前的日期都没法选择
	 if(returnDate=='#js_backDate'){
		 $('#date_list .valid').each(function(index,element){
			 var thisDate = $(this).attr('date');
			 if(thisDate==departDate){return false;}
			 $(this).removeClass().addClass('day_pass');
			 });
	   }
	  
	 //选择日期
	 $(document).on('click','.valid',function(){
		 //解除动作
		 $(document).off('click','.valid');
		 var clickReturnDate = $(this).attr('date');
		 
		 //如果出发日期大于等于返程日期，返程日期自动加5天
		 if(returnDate=='#js_departDate' && getTimeFun(clickReturnDate) >= getTimeFun(backDate)){
			 upDate(formattingDate(clickReturnDate,5,1),'#js_backDate');
		   }

		 //调用更新日期函数
		 upDate(clickReturnDate,returnDate);
		 $('#date_list_box').hide();
		 dateList();
		 });
	
}

//更新日期
function upDate(DateStr,returnDate){
	var html='<em date="'+DateStr+'">'+monthDayFun(DateStr)+'</em><strong>'+weekFun(DateStr)+'</strong>';
	$(returnDate).html(html);
	}

//毫秒数格式为yyyy-mm-ss
function formattingDate(dateStr,day,type){
	//dateStr为传入日期，day为要增加的天数
	if(type==1){
		var date = new Date(getTimeFun(dateStr)+day*86400000),m = parseInt(date.getMonth())+1;
	  }else if(type==2){
		var date = new Date(dateStr+day*86400000),m = parseInt(date.getMonth())+1;
	  }	
	var str = date.getFullYear()+'-'+m.toString()+'-'+date.getDate();
	return str;
	}

//yyyy-mm-ss格式转化为毫秒数
function getTimeFun(dateStr){
	var dateArr = dateStr.split("-"),m = parseInt(dateArr[1])-1,onDate = new Date(dateArr[0],m.toString(),dateArr[2]),t = onDate.getTime();
	return t;
	}

//yyyy-mm-ss格式转化为mm月ss号
function monthDayFun(dateStr){
	var dateArr = dateStr.split("-");
	return dateArr[1]+'月'+dateArr[2]+'号';
}
//yyyy-mm-ss格式转化为星期几
function weekFun(dateStr){
	var weekArr = ['周日','周一','周二','周三','周四','周五','周六'];
	var dateArr = dateStr.split("-"),m = parseInt(dateArr[1])-1,clickDate = new Date(dateArr[0],m.toString(),dateArr[2]),n = clickDate.getDay();
	return weekArr[n];
	}
	
//遍历日历并选择传值日期
function eachDateList(type,date,departBack){
	$('#date_list .valid').each(function(index,element){
		//thisDate为当前对象的日期，thisDateNum为当前对象的日期数
		var thisDate = $(this).attr('date'),thisDateNum=$(this).children('em').text(),html;
		//判断是否出发日期或返程日期
		if(thisDate == date && departBack == 'depart'){
			html= '<i>出发日期</i><em>'+thisDateNum+'</em>';
			$(this).addClass('selected_start').html(html);
		  }else if(thisDate == date && departBack == 'back'){
			html = '<i>返程日期</i><em>'+thisDateNum+'</em>';
			$(this).addClass('selected_back').html(html);
		  }//if
        
    });
}