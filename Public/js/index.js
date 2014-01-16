$(function(){
	//解决IE下左侧栏鼠标以上点击时不能打开的问题
	if($.browser.msie){
		$("#service ul li label").click(function(){
			$(this)[0].parentNode.click();
		});
	}
	$("#chaxun_form").find("li .city").each(function(i){
		$(this).click(function(){
            textInputDIs($(this));
			if(i==0){popCityList($(this)[0]);}
			if(i==1){popCityList($(this)[0],"guowai");}
		});
	});
	$("#chaxun_form").find("li .date").each(function(i){
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
	$("#radio_sel input").each(function(i){
		var ul=$(this).parent().parent();
		$(this).click(function(){
			var li=ul.find("li");
			if(i==2){
				li.eq(4).hide();
				var div=$("<ul class='clear'></ul>");
				var html="<li><span>出发城市</span><input type='text' class='text city' name='startCity1' value='城市名'/></li><li><span>到达城市</span><input type='text' class='text city' name='arriveCity1' value='城市名'/></li><li><span>出发日期</span><input type='text' class='text date' name='startTIme1' value='出发时间' /></li>";
                div.html(html);
				var lis=div.find("li");
				lis.find(".city").eq(0).click(function(){textInputDIs($(this));popCityList($(this)[0]);});
				lis.find(".city").eq(1).click(function(){textInputDIs($(this));popCityList($(this)[0],"guowai");});
				lis.find(".date").click(function(){textInputDIs($(this));WdatePicker({minDate:'%y-%M-%d',doubleCalendar:true});});
				$("#submit_sousuo").before(div);
			}else{
				if(ul.find("ul").length>0){ul.find("ul").remove();}
				if(i==0){
					li.eq(4).hide();
				}
				if(i==1){
					li.eq(4).show();
				}
			}
		});
	});
	//查询提交
	$("#chaxun_form").submit(function(e){
        var state=0;
		$(this).find("input.text").each(function(i){
            if($(this).parent().css("display")==="none") return true;
			if($(this).attr("state")!="1"||$(this).val()==""){
				if($(this).hasClass("city"))$(this).click();
				else{alert("请输入日期");}
				state=1;
				return false;   
			}
		});
        if(state==1) return false;
        searChAagin();
	});
	//订单查询
	$("#order_query input:text").click(function(){$(this).val("");});
	
	//图片轮换
	slider($("#slideDiv"),1000,6000,0,4,679);
	slider($("#actAd"),1000,6000,0,3,642);
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
