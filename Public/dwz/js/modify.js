var js_path=Public+"/js/source/";
$(function(){
    if($("#province_sel").attr("state")=="1") return;
    var prov=$("#province_sel");
    $.ajax({
        url:js_path+"cityList.xml",
        dataType:"xml",
        success:function(msg){
            prov.attr("state","1");
            var loc=$(msg).find("Location");
            var opt="";
            var states=loc.find("CountryRegion").eq(0).find("State");
            for(var i=0;i<states.length;i++){
                var name=states.eq(i).attr("Name");
                opt=opt+"<option value="+i+">"+name+"</option>";
            }
            prov.html(opt);
            var val_name=$("#addcodePCT").attr("val").split(",");
            prov.val(Number(val_name[0]));
            changeProv(Number(val_name[0]),Number(val_name[1]));
            prov.change(function(){
                changeProv(Number($(this).val()));
            });
        },
        error:function(){prov.attr("state","1");}
    });
});
function changeProv(n,sel){
	var city=$("#city_sel");
	$.ajax({
		url:js_path+"cityList.xml",
		dataType:"xml",
		success:function(msg){
			var loc=$(msg).find("Location");
			var opt="";
			var citys=loc.find("CountryRegion").eq(0).find("State").eq(n).find("City");
			for(var i=0;i<citys.length;i++){
				var name=citys.eq(i).attr("Name");
				opt=opt+"<option value="+i+">"+name+"</option>";
			}
			city.html(opt);
            if(sel){city.val(sel);}
		},
		error:function(){}
	});
}
