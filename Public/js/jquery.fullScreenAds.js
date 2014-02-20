/*!
 全屏轮换广告插件
*/

(function($){
 $.fn.extend({
	 //定义插件的名称
	 fullScreenAds:function(options){
		 //为插件参数设定默认值
		 var defaults={
			 fadeTime:1000,              //广告图淡入的时间
			 showTime:6000,              //广告图显示的时间
			 mainWidth:980               //主体的宽度
			 };
		 //使用$.extend()覆盖插件中的默认值
		 var options=$.extend(defaults,options);
		 
		 //取得右边距
	     var sanr=($(window).width()-options.mainWidth)/2 + 'px';
		
		 return this.each(function(){
			 var obj=$(this).find('li');
			 var nav=obj.parent().siblings().find('a');
			 //设置#slide_ad_nav的右边距
			 obj.parent().siblings().css({"right":sanr,"opacity":0.9}).show(500);
			 //初始化变量index为0，用于保存索引
             var index = 0;
	         //初始化变量len为为图片列表的数量
	         var len = obj.size();
			 
			 //setInterval()方法
			 var int=setInterval(function(){
				 obj.parent().parent().css({'background':'none'});
				 showPics(index);
				 index++;
				 if(index == len){index = 0;};
				 },options.showTime);
				 
			 //showPics()设置图片和滑块的显示和隐藏
			 function showPics(index){
				 var bg=obj.eq(index).attr('img-data');
				 nav.eq(index).addClass('active').siblings().removeClass('active');
				 obj.eq(index).css({'background-image':'url('+bg+')'}).fadeIn(options.fadeTime).siblings('li').hide();
				 }
			 //当鼠标进入滑块,调用showPics()函数
			 nav.mouseenter(function(){
				 index = $(this).index();
				 showPics(index);
				 });
			
             });
	 }
 });
   
})(jQuery);