$(function(){
	var left=($(window).width()-280)/2 +'px';
	//$('#msg').css({left:left});
	$('style').append('#msg{left:'+left+';}');
	//改变窗口的大小
	$(window).resize(function() {
        var left=($(this).width()-280)/2 +'px';
		$('#msg').css({left:left});
    });
	//卷轴
	$(window).scroll(function(){
        var top=($(this).scrollTop()+($(this).height()-150)/2) +'px';
        $('#msg').css({top:top});
		});
	$(document).on('click','#confirm',function(){
		$('#msg').remove();
		});
	$(document).on('click','#cancel',function(){
		$('#msg').remove();
		});
	$(document).on('click','#call-back',function(){
		$('#msg').remove();
		});
	
	});
	var interval = setInterval(function(){
            var time = parseInt($('#wait').text());
			$('#wait').text(--time);
            if(time <= 0) {
                $('#msg').remove();
                //clearInterval(interval);
            }
        }, 1000);
        
/*
*info为表示的信息;
*type为信息的类型,0为确定，1为询问，2为自动关闭;
*icon为图标的样式,warning为警告，forbid为禁止，correct为正确，query为疑问;
*fun为询问时确定的回调函数;
*/
function msg(info,type,icon,fun){
    if(!arguments[1]) type = 0;
	var html='';
	html += '<div id="msg">';
    html += '<h2>提示信息</h2>';
    html += '<p><label class="'+ icon +'">'+ info +'</label></p>';
    html += '<div>';
	switch (type)
	{
	case 0:
	html += '<span id="confirm"><a onclick="'+fun+'">确认</a></span>';
	break;
	case 1:
	html += '<span id="cancel"><a>取消</a></span>';
	html += '<span id="call-back"><a onclick="'+fun+'">确认</a></span>';
	break;
	case 2:
	html += '信息框自动<a id="cancel"> 关闭</a>，等待时间：<b id="wait">5</b>';
	break;
	};
    html += '</div>';
    html += '</div>';
	//return html;
	$('body').append(html);
	}