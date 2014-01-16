<?php
    function tjjipiao(){
        include WEB_ROOT."tj.php";
        if($_GET['dq']=='mz'){
            echo json_encode($mzarr);
        }elseif($_GET['dq']=='dny'){
            echo json_encode($dnyarr);
        }elseif($_GET['dq']=='rh'){
            echo json_encode($rharr);
        }elseif($_GET['dq']=='qt'){
            echo json_encode($qtarr);
        }
    }
?>
 function open_kf(){
                window.open('http://www12.53kf.com/webCompany.php?arg=meile10&style=1&language=cn&lytype=0&charset=gbk&kflist=off&kf=&zdkf_type=1&referer=&keyword=', '_blank', 'height=473,width=703,top=200,left=200,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=no,location=no,titlebar=no');
            }
            function gettj(dq,t){
                $.getJSON("http://192.168.4.205/newasf/common/tjjipiao",'dq='+dq+'&callback=?',function(data){
                 //   alert(data);
                    var html='';
					var data2=data.code;
                    $.each(data2, function(i,item){
                        if(i!='gz'){
                           var  st ='style="display:none"';
                        }
                        html +='<ul class="conbk" id="from'+i+'" '+st+'>';
                            $.each(item,function(i,items){
                             html+='<li>';
                             html+='<span class="span0"><a class="a0">'+items.fr+'</a><a class="spr3 rec"></a><a class="a1">'+items.too+'</a></span>';
                            html+= '<span class="span1"><a class="zz">中转</a></span>';
                            html+='<span class="span2"><a class="time">2013-07-30</a><a>截止</a></span>';
                            html+= '<span class="span3"><a class="sign"><img src="__PUBLIC__/images/df_10.gif" alt="sign" /></a><a>'+items.air+'</a></span>';
                            html+= '<span class="span4"><a>往返</a><a class="jg">'+items.price+'</a></span>';
                            html+= '<span class="end span5"><a class="spr3 yd" href="javascript:;" onclick="open_kf()">预订</a></span>';
                            html+= ' </li>';
                            })
                        html+= '</ul>';
                     })
                            $('#special_list').html(  html );

                })
            }
			$(function(){
				gettj('mz');
			})
			

            $('#special_tic .left a').bind('click',function(){
                 $(this).siblings().removeClass("active");
                $(this).addClass("active");
            })