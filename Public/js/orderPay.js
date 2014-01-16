$(function(){
	//隐藏订单详情
    $("#o_details .ycan").each(function(i){
		var lis=$("#o_details ul").eq(i).find("li");
		$(this).find("a").click(function(){
			var $this=$(this);
			if($(this).hasClass("active")){
				lis.eq(1).slideDown(300);
				lis.eq(2).slideDown(300,function(){
					$this.removeClass("active");
					$this.html("隐藏订单详情");
				});
			}else{
				lis.eq(1).slideUp(300);
				lis.eq(2).slideUp(300,function(){
					$this.addClass("active");
					$this.html("显示订单详情");
				});
			}
			return false;
		});
	});
	//展开航程
	$("#o_details .zk").each(function(i){
		var des=$("#o_details ul").eq(i).find(".detail");
		$(this).find("a").click(function(){
			var $this=$(this);
			if($(this).hasClass("active")){
				des.each(function(j){
					$(this).slideUp(300,function(){
						if(j==des.length-1){
							$this.removeClass("active");
							$this.html("展开航程↓");
						}
					});
				});
			}else{
				des.each(function(j){
					$(this).slideDown(300,function(){
						if(j==des.length-1){
							$this.addClass("active");
							$this.html("收起航程↑");
						}
					});
				});
			}
			return false;
		});
		
	});
    //支付方式切换
	$("#pay_list_zf h2 a").each(function(i){
		$(this).click(function(){
			if($(this).hasClass("active")) return;
			$(this).siblings().removeClass("active");
			$(this).addClass("active");
			$("#pay_list_zf form").hide();
			$("#pay_list_zf form").eq(i).show();
			return false;
		});
	});
});