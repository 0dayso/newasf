$(function(){
	
	$('#close_flight_query').click(function(){
		$('#flight_query_box').hide();
		$('#load').show();
		$('#consultancy').hide();
		$('#fquery_form').hide();
		$('#cons_select label').eq(0).html('<input type="radio" value="1" checked="checked"/>咨询专业旅行顾问<span><b>4</b>秒</span>');
		$('#cons_select label').eq(1).find('input').removeAttr('checked');
		});
	$('#flight_tab dt').click(function(){
		$('#flight_query_box').hide();
		$('#load').show();
		$('#consultancy').hide();
		$('#fquery_form').hide();
		$('#cons_select label').eq(0).html('<input type="radio" value="1" checked="checked"/>咨询专业旅行顾问<span><b>4</b>秒</span>');
		$('#cons_select label').eq(1).find('input').removeAttr('checked');
		});
	$('#flight_tab dd').click(function(){
		$('#flight_query_box').hide();
		$('#load').show();
		$('#consultancy').hide();
		$('#fquery_form').hide();
		$('#cons_select label').eq(0).html('<input type="radio" value="1" checked="checked"/>咨询专业旅行顾问<span><b>4</b>秒</span>');
		$('#cons_select label').eq(1).find('input').removeAttr('checked');
		});
	
	//调用国际机票查询弹窗
	$('#sub').click(function(){
		if($('#flight_tab dt').hasClass('tab_on')){
			flightQueryBox('单程');
			}else if($('#flight_tab dd').hasClass('tab_on')){
				flightQueryBox('往返');
				}
		});
//国际机票查询弹窗
function flightQueryBox(txt){
	//显示窗口
	$('#flight_query_box').show();
	//获得城市和时间
	var fromCity=$('#js_departcity').text(),toCity=$('#js_backcity').text();
	var originDate=$('#js_departDate em').attr('date'),returnDate=$('#js_backDate em').attr('date');
	//设置查询弹窗的城市和时间
	$('#query_city strong').eq(0).text(fromCity);
	$('#query_city strong').eq(1).text(toCity);
	$('#query_city strong').eq(2).text(txt);
	$('#load em').eq(0).text(originDate);
	$('#load em').eq(1).text(returnDate);
	$('#fromCity').val(fromCity);
	$('#toCity').val(toCity);
	$('#originDate').val(originDate);
	$('#returnDate').val(returnDate);
	
	//判断是往返或单程
	if(txt=='往返'){
		$('#load p').eq(2).show();
		$('#radio_sel input').eq(0).attr('checked',"checked");
		$('#fquery_form li').eq(4).show();
		}else if(txt=='单程'){
			$('#radio_sel input').eq(1).attr('checked',"checked");
			$('#fquery_form li').eq(4).hide();
	}
			
	//加载航空公司
	var airline_arr = ['中国国际航空','新加坡航空','法国航空','维珍航空','香港航空','印度航空','新加坡航空','大韩航空','国泰航空','马来西亚航空','印度航空','日本航空'];
	var i=0;
	var fload=setInterval(function(){
		$('#load span').html(airline_arr[i]);
		i++;
		if(i>=12){
			clearInterval(fload);
			$('#load').hide();
			$('#consultancy').show();
		}
	},100);
	
	
	//选择往返或单程
	$(document).on('click','#cons_select input',function(){
		if($(this).val()=='1'){
			$('#fquery_form').hide();
			$('#cons_select label').eq(1).find('input').removeAttr('checked');
			}else if($(this).val()=='2'){
				$('#fquery_form').show();
				$('#cons_select span').remove();
				if(typeof finterval != 'undefined'){clearInterval(finterval);}
				$('#cons_select label').eq(0).find('input').removeAttr('checked');
				}
		});
	
	//调用弹窗
	var time=3;
	var finterval=setInterval(function(){
            time--;
            if(time <= 0) {
                $('#cons_select span').remove();
				//弹出客服窗口
				if(typeof finterval != 'undefined'){open_kf();}
                clearInterval(finterval);
            };
			$('#cons_select b').html(time);
        }, 1000);
	
}


	
	$("#radio_sel input").each(function(i){
		var ul=$(this).parent().parent();
		$(this).click(function(){
			var li=ul.find("li");
				if(i==1){
					li.eq(4).hide();
				}
				if(i==0){
					li.eq(4).show();
				}

		});
	});
	//查询提交
	$("#flightquery_form").submit(function(e){
        var state=0;
		$(this).find("input.txt").each(function(i){
            if($(this).parent().css("display")==="none") return true;
			if($(this).val()==""){
				state=1;
				return false;
			}else if($(this).val()=="您的姓名"){
				alert('请输入正确的姓名');
				state=1;
				return false; 
				}else if($(this).val()=="您的手机号"){
					alert('请输入正确的手机！');
					state=1;
					return false; 
					}
		});
        if(state==1) return false;
	});
	
	hint('#name','您的姓名');
	hint('#phone','您的手机号');
	
});

function hint(id,value){
	$(id).focus(function(){
                if($(this).val()==value){
                     $(this).val('');
                }
        }).blur(function(){
                if($(this).val()==''){
                      $(this).val(value);
               }
        });
}
