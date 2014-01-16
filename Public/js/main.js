$(function(){
	$("#weixin_me").mouseover(function(evt){
        evt.stopPropagation?evt.stopPropagation():evt.cancelBubble=true;
		var offset=$(this).offset();
		var left=offset.left-140,top=offset.top+25;
		var weixin=$("#weixin_erweima");
		if(weixin.length>0){
			if(weixin.css("display")!="none"){return;}
			weixin.css({left:left,top:top})
			weixin.slideDown(500);
			$(document).mouseover(bindClickWeiXin);	
			return;
		}
		var html="<div class='tp'></div><div class='wr'><span class='sp0'><a class='a0'>查航班，找顾问，寻优惠...</a><a class='a1'><label class='lb0'>爱尚飞</label>微信帮你忙</a><a class='a2'>爱尚飞微信号：<label class='lb1'>aishangfeiweixin</label></a><a class='a3'>或直接扫描右侧二维码</a></span><span class='img'></span></div>";
		var div=$("<div id='weixin_erweima'>"+html+"</div>");
		div.css({left:left,top:top});
		div.appendTo($("body"));
		div.mouseover(function(evt){
        	evt.stopPropagation?evt.stopPropagation():evt.cancelBubble=true;	
		});
		$(document).mouseover(bindClickWeiXin);
		div.slideDown(500);
	});
	
	//移上弹出我的爱尚飞
	$("#myAisf_w").mouseover(function(evt){
		evt.stopPropagation?evt.stopPropagation():evt.cancelBubble=true;
		var offset=$(this).offset();
		var left=offset.left-20,top=offset.top+28;
		$("#myAisf_u").css({left:left,top:top});
		if($("#myAisf_u").css("display")=="none"){
			$("#myAisf_u").slideDown(300,function(){
				$("#myAisf_t").addClass("active");
			});
			$(document).mouseover(bindOverMylogin);
		}
	});
	$("#myAisf_u").mouseover(function(evt){//清除冒泡
		evt.stopPropagation?evt.stopPropagation():evt.cancelBubble=true;
	});
});
function bindClickWeiXin(){
	$("#weixin_erweima").slideUp(500);
	$(document).unbind("mouseover",bindClickWeiXin);
}
function bindOverMylogin(){
	$("#myAisf_u").slideUp(300,function(){
		$("#myAisf_t").removeClass("active");
	});
	$(document).unbind("mouseover",bindOverMylogin);
}

/**
 * 通用AJAX提交
 * @param  {string} url    表单提交地址
 * @param  {string} formObj    待提交的表单对象或ID
 */
function commonAjaxSubmit(url,formObj,callback,jump){
    if(!formObj||formObj==''){
        var formObj="form";
    }
    if(!url||url==''){
        var url=$(formObj).attr('action')?$(formObj).attr('action'):document.URL;
    }
    $(formObj).ajaxSubmit({
        url:url,
        type:"POST",
        success:function(data, st) {
            if(data && (typeof data)=='string'){
                var data = eval("(" + data + ")");
            };
            if(data.status==1){
				$.layer({
					area : ['auto','auto'],
					shadeClose : false,
					time : 2,
					dialog : {msg:data.info,type : 1},
					end : callback			
					
				});
            }else{
                $.layer({
					area : ['auto','auto'],
					shadeClose : false,
					time : 3,
					dialog : {msg:data.info,type : 4}
					
				});
            }
			if(!data.url && !jump){
                setTimeout(function(){location.reload},1000);
            }else if(data.url  && !jump){
                setTimeout(function(){top.window.location.href=data.url; },2000);
            }
            
        }
    });
    return false;
}
