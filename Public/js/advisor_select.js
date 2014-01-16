/*
@ 注册完成-选择顾问功能的JS
@ advisor_select.js
*/


//获取BODY的宽度
var w = $('body').width();
//设置遮罩层的不透明度
$('#mask_layer').css('opacity','.5');
//设置选择顾问的DIV居中
$('#advisor_select').css('left',(w-650)/2 + 'px');

//选择系统分配的顾问或认识的顾问
$('#radio input').click(function(){
	if($(this).val() == 0){
		//收缩顾问列表
		$('#as_list').slideUp(500);
		}else{
	    //展开顾问列表
		$('#as_list').slideDown(500);
		//调用查找顾问的AJAX
		var send = encodeURI('company=1&department=9');
		as_ajax(send);
		}
	});
	

//查找顾问的AJAX
function as_ajax(send){
	//下载等待
	$('#as_loadWaitImg').show();
	//AJAX
	$.getJSON(getURL,send,function(data){
		$('#as_loadWaitImg').hide();
		var html='';
		$.each(data,function(index,as){
			html+='<li>';
			html+='<img src="'+ getImgURL + as.avatar+ '"></img>';
			html+='<span>';
			html+='<input name="list_box" type="radio" value="' + as.id + '" ';
			if(index==0){html+='checked="checked"';}
			html+='/>';
			html+=as.name;
			html+='</span></li>';
			});
		//更新DOC的内容
		$('#list_box ul').html(html);
		//选择顾问
		$('#list_box li').click(function(){$(this).find('input').attr('checked','checked');});
		
		});
	}

  
$(function(){
//遮罩层设置
$(window).scroll(function(){
	//获取DOC的高度
	var l = $('body').height();
	//设置遮罩层的高度
	$('#mask_layer').css('height',l + 'px');
	//定位选择顾问的位置
//	$('#advisor_select').css('top',($(window).scrollTop()+100) + 'px');
	});


//点击确定认动作
$('#as_submit').click(function(){
	if($('#radio input').eq(0).attr('checked') == 'checked'){
		//关闭选择顾问和遮罩层
		$('#advisor_select').hide();$('#mask_layer').hide();
            $.post(getPostURL,{id:''},function(data){
                $('#advisor_select').hide();$('#mask_layer').hide();
                location.href = getIndexURL;
            });
		}else{
		//提交数据并关闭选择顾问和遮罩层
		$.post(getPostURL,{id:$('#list_box input:checked').val()},function(data){
			$('#advisor_select').hide();$('#mask_layer').hide();
			    location.href = getIndexURL;
			});
		}
	});
	  

//地区查找顾问
$('#as_list .left a').click(function(){
	//选取当前地区
	$(this).addClass('active').siblings().removeClass('active');
	//调用地区顾问的AJAX
	var send = encodeURI('company='+$(this).attr('company'));
	as_ajax(send);
	});
		  
	
//检查查找输入是否正确
$("#ksval").focus(function(){
	//检查输入是否不变
	if($(this).val() == "请输入顾问姓名"){$(this).val("");}
	});
$("#ksval").blur(function(){
	//检查输入是否为空
	if($(this).val() == ""){$(this).val("请输入顾问姓名");}
	});
		  

//点击查找顾问
$('#sousuo').click(function(){
	if($('#ksval').val() == "请输入顾问姓名"){
		//检查输入是否不变
		alert('请输入查找顾问的姓名！');
		}else{
		//调用查找顾问的AJAX
		var send = encodeURI('search='+$('#ksval').val());
		as_ajax(send);
		};
	});
$("#ksval").keydown(function(e){
	//回车查找顾问
	if(e.keyCode == 13){
		//调用查找顾问的AJAX
		var send = encodeURI('search='+$('#ksval').val());
		as_ajax(send);
		}
	});

	  
});                 