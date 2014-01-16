// JavaScript Document
$(function(){
	
		//全部礼品分类
		$('#allproduct li').mouseover(function(){
				var i=$(this).index();
				$(this).addClass('on').siblings().removeClass('on');
				$('#allproduct .ap_list').eq(i).addClass('on').siblings().removeClass('on');
			});
		$('#allproduct').mouseleave(function(){
				if($('#allproduct .ap_list').hasClass('on')){
					$('#allproduct').find('.on').removeClass('on');
					}
			});
	
		//积分兑换排行榜
		$('#jf_list li').mouseover(function(){
				$(this).siblings().find('.exch_pro').removeClass('on');
				$(this).find('.exch_pro').addClass('on');
			});
			
		//爱钻兑换排行榜
		$('#aj_list li').mouseover(function(){
				$(this).siblings().find('.exch_pro').removeClass('on');
				$(this).find('.exch_pro').addClass('on');
			});
			
		//好礼推荐
		$('#rec_nav a').click(function(){
				var i=$(this).index();
				$(this).addClass('active').siblings().removeClass('active');
				$('#recommend ul').eq(i).addClass('active').siblings().removeClass('active');
			});
		$('#recommend li').mouseover(function(){
				$(this).addClass('b2').siblings().removeClass('b2');
				$(this).find('.shopping').show();
			}).mouseout(function(){
				$(this).removeClass('b2');
				$(this).find('.shopping').hide();
				});
		$('.hot_exchange dd').mouseover(function(){
				$(this).addClass('bg').siblings().removeClass('bg');
				$(this).find('.shopping').show();
			}).mouseout(function(){
				$(this).removeClass('bg');
				$(this).find('.shopping').hide();
				});
				
		$('.class_list li').mouseover(function(){
				$(this).addClass('bg').siblings().removeClass('bg');
			}).mouseout(function(){
				$(this).removeClass('bg');
				});
			
		//图片轮换
		slider($("#mall_ad"),1000,6000,0,4,720);
		slider($("#f1_ad"),1000,6000,0,2,236);
		slider($("#f2_ad"),1000,6000,0,2,236);
	});
	
//动画制作
(function(){
var timer = null;var timer1 = null;
//t轮换时间，s等待时间,n当前图片位置，m总共张数，w图片宽度
function slider(div,t, s, n, m, w) {
	sliderChange(div,t, s, n, m, w);
	sliderClick(div,t, s, m, w);
}
function sliderChange(div,t, s, n, m, w) {
	div.find("ul").animate({ "margin-left": -(n * w) + "px" }, t, function () {
			timer = setTimeout(function () {
			var j = n + 1;
			if (j > m - 1) { j = 0; }
			div.find("ol li a").eq(n).removeClass("active");
			div.find("ol li a").eq(j).addClass("active");
			sliderChange(div,t, s, j, m, w);
		}, s);
	});
}
function sliderClick(div,t, s, m, w) {
	div.find("ol li").each(function (i) {
		$(this).find("a").click(function () {
			clearTimeout(timer); clearTimeout(timer1);
			div.find("ul").stop(true);
			div.find("ol li a").removeClass("active");
			div.find("ol li a").eq(i).addClass("active");
			div.find("ul").animate({ "margin-left": -(i * w) + "px" }, t);
			timer1 = setTimeout(function () {
				sliderChange(div,t, s, i, m, w) 
			},s);
		});
	});
}
window.slider=slider;
})()
