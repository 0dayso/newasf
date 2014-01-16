<script type="text/javascript" src="http://www.aishangfei.net/Public/js/jquery-1.8.3.min.js"></script>
<div id="data"></div>
<script>
$(function(){
	$.get("http://flights.aishangfei.net/s?flightType=1&tickType=ADT&personNum=1&originCode=BJS_1&desinationCode=NYC_1&originDate=2013-07-20&returnDate=2013-07-21&childNum=0&directFlightsOnly=2&accessId=fly4free&syn=true",function(data){
		
		$("#data").html(data);
	})
})
</script>
