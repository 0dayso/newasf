$(function(){
	/*爱钻好礼推荐*/
	$('.class_list li').mouseover(function(){
				$(this).addClass('bg').siblings().removeClass('bg');
			}).mouseout(function(){
				$(this).removeClass('bg');
				});
	
	//图片轮换
	slider($("#actAd"),1000,6000,0,3,680);
	slider($("#themeAd"),1000,6000,0,3,960);
	//slider($("#trademarkAd"),1000,6000,0,3,960);
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
