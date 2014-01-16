$(function(){
	kfurl="http://www17.53kf.com/webCompany.php?"+$("#ivt_script").attr("src").match(/arg=.*/gim);
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
		
	$('#cons_select input').click(function(){
		if($(this).val()=='1'){
			$('#fquery_form').hide();
			}else if($(this).val()=='2'){
				$('#fquery_form').show();
				$('#cons_select span').remove();
                clearInterval(finterval);
				}
		});
	
	var time=4;
	var finterval=setInterval(function(){
            time--;
            if(time <= 0) {
                $('#cons_select span').remove();
                clearInterval(finterval);
				$.layer({
            type : 2,
            title : '欢迎咨询',
            iframe : {src : kfurl},
            area : ['750px' , '466px'],
            offset : ['100px','']
        });
            };
			$('#cons_select b').html(time);
        }, 1000);
	
	$("#flightquery_form").find("li .city").each(function(i){
		$(this).click(function(){
            textInputDIs($(this));
			if(i==0){popCityList($(this)[0]);}
			if(i==1){popCityList($(this)[0],"guowai");}
		});
	});
	$("#flightquery_form").find("li .date").each(function(i){
		$(this).click(function(){
            textInputDIs($(this));
			if(i==0){WdatePicker({minDate:'%y-%M-%d',doubleCalendar:true});}
			if(i==1){WdatePicker({minDate:'%y-%M-{%d+1}',doubleCalendar:true});}
		});
	});
        function textInputDIs(dom){
            if(dom.attr("state")!="1"){
                dom.css("color","#333");
                dom.val("");
                dom.attr("state","1");
            }
        }
	$("#radio_sel2 input").each(function(i){
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
		$(this).find("input.text").each(function(i){
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
    hint('#qq','您的QQ号');
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