$(function(){
	//出发城市
	$('#js_departcity').click(function(){
		var cityName=$(this).text(),returnCity1='#js_departcity';
		setCityList(cityName,returnCity1);
		});
	//到达城市
	$('#js_backcity').click(function(){
		var cityName=$(this).text(),returnCity2='#js_backcity';
		setCityList(cityName,returnCity2);
		});
	
	//关闭城市列表
	$(document).on('click','#close_city_list',function(){closeCityList();});
		
	//城市列表TAB
	$(document).on('click','#city_tab li',function(){
		var i=$(this).index();
		$(this).addClass('tab_on').siblings().removeClass('tab_on');
		$('.collapsible-set').eq(i).addClass('city_on').siblings().removeClass('city_on');
		});
		
	//选择国内城市
	$(document).on("click",'.collapsible h3',function(){
		var i=$(this).parent().index();
		$('#inland_city .city_list').each(function(index,element){
			if($(this).siblings().find('span').attr('class')=='icon-up'){
               $(this).hide().siblings().find('span').removeClass('icon-up').addClass('icon-down');
			}else if($(this).siblings().find('span').attr('class')=='icon-down'&&index==i){
			   $(this).show().siblings().find('span').removeClass('icon-down').addClass('icon-up');
			}
			});
		});
		
	//选择国际城市
	$(document).on("click",'.collapsible h3',function(){
		var i=$(this).parent().index();
		$('#inte_city .city_list').each(function(index,element){
			if($(this).siblings().find('span').attr('class')=='icon-up'){
               $(this).hide().siblings().find('span').removeClass('icon-up').addClass('icon-down');
			}else if($(this).siblings().find('span').attr('class')=='icon-down'&&index==i){
			   $(this).show().siblings().find('span').removeClass('icon-down').addClass('icon-up');
			}
			});
		});
		
	//搜索相关结果
	$(document).on('keyup','#city_search input',function(){
		var searchVal=$(this).val();
		if(searchVal.length>0){
			//显示清空键
			$('#empty_search').css('display','block');
			//AJAX搜索城市，citySearchUrl的值从页面传进来
			$.getJSON(citySearchUrl,{k:searchVal},function(data){
				if(data.r.length){
				var html='';
				$.each(data.r,function(i,item){
					//也可以使用$.isArray(item)判断是否为数组
					if(!item.length){
						html +='<li><a code="'+item.city+'('+item.code+'),'+item.country+',城市" title="'+item.city+'">'+item.city+'</a></li>';
						}else{
							html +='<li><a code="'+item[0].city+'('+item[0].code+'),'+item[0].country+',城市" title="'+item[0].city+'">'+item[0].city+'</a></li>';
							}
					});}else{
						html='<div>没有结果</div>';
						}
				$('#associate').html(html);
				});
				$('.collapsible-set').removeClass('city_on');
			    $('#associate').show();
			}else{
				$('#empty_search').hide();
				$('#associate').hide();
				$('#inland_city').addClass('city_on');
				}
		});
		
	//点击清空搜索内容
	$(document).on('click','#empty_search',function(){
		//关闭搜索相关结果窗口
	    $('#associate').hide();
	    //清空搜索内容
	    $('#city_search input').val('');
	    //显示国内城市
	    $('#inland_city').addClass('city_on');
	    //隐藏国际城市
	    $('#inte_city').removeClass('city_on');
		//隐藏自身
		$(this).hide();
		});
			
			
});
	
	
	
//加载城市列表并选择城市
function setCityList(cityName,returnCity){
	//判断是否已加载了城市列表“city_list_box”
	if(!$('body').children().is('#city_list_box')){
		//cityListUrl在主页面传值过来,发送请求，返回成功后加载
		$.post(cityListUrl,function(data){
			$('body').append(data);
			//判断是否为选择到达城市，是默认为国际城市
			if(returnCity=='#js_backcity'){
				$('#city_tab li').eq(1).addClass('tab_on').siblings().removeClass('tab_on');
				$('#city_list_box .collapsible-set').eq(1).addClass('city_on').siblings().removeClass('city_on');
			}else if(returnCity=='#js_departcity'){
				$('#city_tab li').eq(0).addClass('tab_on').siblings().removeClass('tab_on');
				$('#city_list_box .collapsible-set').eq(0).addClass('city_on').siblings().removeClass('city_on');
				}
			eachCityList(cityName);
			});
		}else{
			//判断是否为选择到达城市，是默认为国际城市
			if(returnCity=='#js_backcity'){
				$('#city_tab li').eq(1).addClass('tab_on').siblings().removeClass('tab_on');
				$('#city_list_box .collapsible-set').eq(1).addClass('city_on').siblings().removeClass('city_on');
			}else if(returnCity=='#js_departcity'){
				$('#city_tab li').eq(0).addClass('tab_on').siblings().removeClass('tab_on');
				$('#city_list_box .collapsible-set').eq(0).addClass('city_on').siblings().removeClass('city_on');
				}
			eachCityList(cityName);
			}
			
	
	
	//点击选择城市	
	$(document).on('click','.city_list li',function(){
		$(document).off('click','.city_list li');
		$(document).off('click','#associate li');
		var returnCityName=$(this).children().attr('title');
		$(returnCity).text(returnCityName);
		$('#city_list_box').hide();
		});	
	$(document).on('click','#associate li',function(){
		$(document).off('click','#associate li');
		$(document).off('click','.city_list li');
		var returnCityName=$(this).children().attr('title');
		$(returnCity).text(returnCityName);
		//$('#city_list_box').hide();
		//$('#city_search input').val('');
		closeCityList();
		});		
}
	

//遍历城市在对应的城市后面打勾
function eachCityList(cityName){
	//获取当前window的高度
	var H=$(window).height()+'px';
	//显示城市列表并设置其高度
	$('#city_list_box').show().css("height",H);
	$('.city_list li').each(function(index,element){
		$(this).removeClass('designate');
		if($(this).children().attr('title')==cityName){
			$(this).addClass('designate');
			}
	});
}


//还原城市选择窗口并关闭
function closeCityList(){
	//关闭搜索相关结果窗口
	$('#associate').hide();
	//清空搜索内容
	$('#city_search input').val('');
	//显示国内城市
	$('#inland_city').addClass('city_on');
	//隐藏国际城市
	$('#inte_city').removeClass('city_on');
	//关闭城市选择窗口
	$('#city_list_box').hide();
}