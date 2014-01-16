(function(){//匿名函数包装
var locPath=getJsPath();
function popCityList(dom,s){
    var e=window.event||arguments.callee.caller.arguments[0];
    EX.stop(e);
    var obj=$("hotcity_list_d_asf");
	var tdiv=$("hotcity_list_sel_asf");

    if(obj&&typeof obj==="object"){
        disposeDom(dom,obj,s,tdiv);
        return;
    }

    jQuery.getJSON(locPath+'citylist.php','callback=?',function(data){
        if(data.code){
            loadCss(locPath+"default.css");
            var div=document.createElement("div");
            div.innerHTML=data.code;
            div.id="hotcity_list_d_asf";
            div.className="hotcity_list_d";
            document.body.appendChild(div);
            /*div事件绑定*/
            $("hotcity_hot_domestic").onclick=go_domestic;
            $("hotcity_hot_abroad").onclick=go_abroad;
            var lign1= $("paytabmenu2_guonei").getElementsByTagName("li");
            each(lign1,function(i){
                this.onclick=function(){changeCity("guonei",this,i);}
            });
            var lign2= $("paytabmenu2_guowai").getElementsByTagName("li");
            each(lign2,function(i){
                this.onclick=function(){changeCity("guowai",this,i);}
            });
            div.onclick=function(event){EX.stop(event);}
            var tdiv=createSelectDiv(dom);
            disposeDom(dom,div,s,tdiv);
        }
    })
}
var EX = {
    addEvent:function(k,v){
        var me = this;
        if (me.addEventListener)
          me.addEventListener(k, v, false);
        else if(me.attachEvent)
          me.attachEvent("on" + k, v);
        else
          me["on" + k] = v;
    },
    removeEvent:function(k,v){
        var me=this;
        if (me.removeEventListener)
          me.removeEventListener(k, v, false);
        else if (me.detachEvent)
          me.detachEvent("on" + k, v);
        else
          me["on" + k] = null;
    },
    stop:function(evt){
        evt = evt || window.event;
        evt.stopPropagation?evt.stopPropagation():evt.cancelBubble=true;//停止冒泡
    }
}
//添加静态函数，方便接口调用
popCityList.showDiv=function(){ 
    var o=document.getElementById("hotcity_list_d_asf");
    o.style.display = ""; 
    setTimeout(function(){EX.addEvent.call(document,'click',popCityList.hideDiv);},1);
} 
popCityList.hideDiv=function(){
    var o=document.getElementById("hotcity_list_d_asf");
    o.style.display = "none"; 
    EX.removeEvent.call(document,'click',popCityList.hideDiv);
}
/*处理*/
var timerKey=null;
function disposeDom(dom,div,s,tdiv){
	dom.select();
    setOffset(dom,div);
    popCityList.showDiv();
	tdiv.style.width=(dom.offsetWidth-5)+"px";
	setOffset(dom,tdiv);
	popCityList.showSel();
	tdiv.style.display="none";
    dom.onkeyup=function(e){
		if(tdiv.style.display=="none"){popCityList.hideDiv();tdiv.style.display="block";}
		clearTimeout(timerKey);
       	timerKey=setTimeout(function(){callBackKey(e,dom,tdiv);},330);
    }
	dom.onkeydown=function(e){//阻止动作
		e=e||window.event;
		var key = e.keyCode || e.which || e.charCode;
		if(key ==38 || key ==40 || key ==13){
			e.target?e.preventDefault():e.returnValue = false;
			if(key==38||key==40) return directionKey(key,tdiv,dom);
		}
	}
    if(s==="guowai"){go_abroad();}else{go_domestic();}
    cityCallBackfn(function(){
        popCityList.hideDiv();
        dom.value=this.getAttribute("code").split(",")[0];
    });
}
popCityList.showSel=function(){ 
    var o=document.getElementById("hotcity_list_sel_asf");
    o.style.display ="block";
    setTimeout(function(){EX.addEvent.call(document,'click',popCityList.hideSel);},1);
} 
popCityList.hideSel=function(){
    var o=document.getElementById("hotcity_list_sel_asf");
    o.style.display = "none"; 
    EX.removeEvent.call(document,'click',popCityList.hideSel);
}
function createSelectDiv(dom){
    var div=document.createElement("div");
    div.id="hotcity_list_sel_asf";
    div.className="hotcity_list_sel";
    document.body.appendChild(div);
    return div;
}

var gik=0;
//检测按键
function directionKey(key,tdiv,dom){
	var as=tdiv.getElementsByTagName("a");
	if(key==13){
		tdiv.style.display="none";
		dom.value=as[gik].innerHTML;
		if(dom.value=="暂未收录"){dom.value="";dom.click();}
		return;
	}
	if(as.length==1) return;
	as[gik].className="";
	gik=key==38?(gik==0?as.length-1:gik-1):(gik==as.length-1?0:gik+1);
	as[gik].className="hover";
	var dh=tdiv.clientHeight;
	if((gik+1)*30>dh){tdiv.scrollTop=gik*30;}
	else{tdiv.scrollTop=0;}
}
//绑定点击事件
function getSelCont(dom,o){
    var as=o.getElementsByTagName("a");
    each(as,function(i){
        this.onclick=function(){
            dom.value=this.innerHTML;
			if(dom.value=="暂未收录"){dom.value="";dom.click();}
        }
		this.onmouseover=function(){
			as[gik].className="";
			this.className="hover";
			gik=i;	
		}
    });
}
//http返回当前搜索数据
function callBackKey(e,dom,tdiv){
    e=e||window.event;
    var key = e.keyCode || e.which || e.charCode;
	if(key ==38 || key ==40 || key ==13){
		if(key==13){return directionKey(key,tdiv,dom);}
		return;
	}
	gik=0;
  	var xml = CreateAjax();
    if (xml) {
        xml.onreadystatechange = function () {
            if (xml.readyState === 4) {
                if (xml.status === 200){
					var arr=dataAdapter(eval("("+xml.responseText+")")["r"]);
                    var ul="",end="",hover="";
                    for(var i=0;i<arr.length;i++){
						if(i==arr.length-1){ end=" class=end";}
						if(i==0){hover=" class=hover";}else{hover="";}
                        ul=ul+"<li"+end+"><a href='javascript:;'"+hover+">"+arr[i]+"</a></li>";
                    }
                    ul="<ul>"+ul+"</ul>";
					tdiv.innerHTML=ul;
					getSelCont(dom,tdiv);
                }
                else {}
            } else return false;
        }
		var url=locPath+"getCityList.php?k="+dom.value;
        xml.open("get",url,false);
        xml.send(null);
    }
}

//解析json 数据
function dataAdapter(data)
{		
	var ret = [];
	var i = 0, len = data.length,item;
	if(len==0){
		ret.push("暂未收录");
	}
	for (; i < len; i++)
	{
		item = data[i];
		if (!item.length) 
		{
			ret.push(item.city + "(" + item.code + ")");
		}
		else 
		{
			ret.push(item[0].city + "(" + item[0].code + ")");
		}
	}
	return ret;
}

/*切换到国内*/
function go_domestic() {			 		  
    $("hotcity_hot_domestic").className="on";
    $("hotcity_hot_abroad").className="mr2";
    $("dom_city_cont").style.display='block';
    $("abr_city_cont").style.display='none';
}

/*切换到国外*/	
function go_abroad() {						 
    $("hotcity_hot_domestic").className="mr2";
    $("hotcity_hot_abroad").className="on";
    $("abr_city_cont").style.display='block';
    $("dom_city_cont").style.display='none';
} 
/*国内城市移上显示*/
function changeCity(s,e,n){
    var listLI=e.parentNode.getElementsByTagName("li");
    for(var i=0;i<listLI.length;i++) removeClass(listLI[i],"cur2");
    addClass(e,"cur2");
    var listDIV=$(s+"_select_city").getElementsByTagName("div");
    for(var i=0;i<listDIV.length;i++) listDIV[i].style.display="none";
    listDIV[n].style.display="block";
}
//回调处理
function cityCallBack(id,fn){
    var listA=$(id).getElementsByTagName("a");
    for(var i=0;i<listA.length;i++){
        listA[i].onclick=function(event){
            var e=window.event||event;
            var dom=e.target||e.srcElement;
            fn.call(dom);
        }
    }
}
function cityCallBackfn(fn){
    cityCallBack("guonei_select_city",fn);
    cityCallBack("guowai_select_city",fn);
}
/*xmlhttp*/
function CreateAjax() { if (window.XMLHttpRequest) { return new XMLHttpRequest(); } else if (window.ActiveXObject) { try { return new ActiveXObject("Msxml2.XMLHTTP"); } catch (e) { try { return new ActiveXObject("Microsoft.XMLHTTP"); } catch (e) { alert("Sorry,你的机子缺少AJAX组件。"); return false; } } } }

/*获取当前js文件路径*/
function getJsPath(){
    var result="";
    var es =document.getElementsByTagName("script");
    for (var i = 0; i < es.length; i++)
    {
        var path=es[i].src;
        if (path.indexOf("popcitylist.js")!=-1)
        {
            result = path.substring(0,path.lastIndexOf("/") + 1);
        }
    }
    return result;
}
/*加载css*/
function loadCss(s){
    var head = document.head || document.getElementsByTagName('head')[0];
    var link = document.createElement("link");
    link.setAttribute('type', 'text/css');
    link.setAttribute('rel', 'stylesheet');
    link.setAttribute('href', s);
    head.appendChild(link);
};
function setOffset(dom,div){
    var offset=getOffset(dom);
    div.style.left=offset.left+"px";
    div.style.top=(offset.top+dom.offsetHeight+1)+"px";
}
function addClass(o,s) {
    var c = o.className;
    if (c && c.indexOf(s) === -1) { o.className=c + " " + s;}
    if (!c) { o.className=s; }
}
function removeClass(o,s){
    var c = o.className;
    if (c && c.indexOf(s) !== -1) {
            if (c.split(" ").length > 1) o.className=trim(c.replace(s, ""));
            else o.className="";
    }
}
function $(s){return document.getElementById(s);}
function trim(s){return s.replace(/(^\s*)|(\s*$)/g, "");}
function getOffset(o){
	var a = o.offsetLeft, b = o.offsetTop, c = o.offsetParent;
	try {
		while (c.tagName.toUpperCase() != "BODY") { a += c.offsetLeft; b += c.offsetTop; c = c.offsetParent; }
	} catch (e) { } return { left: a, top: b };	
}
function each(obj, callback, args) {
	var name, i = 0, length = obj.length, isObj = length === undefined || isFunction(obj);
	if (args) {
		if (isObj) {
			for (name in obj) { if (callback.apply(obj[name], args) === false) { break; } }
		} else { for (; i < length; ) { if (callback.apply(obj[i++], args) === false) { break; } } }
	} else {
		if (isObj) { for (name in obj) { if (callback.call(obj[name], name, obj[name]) === false) { break; } } }
		else { for (; i < length; ) { if (callback.call(obj[i], i, obj[i++]) === false) { break; } } }
	}
	return obj;
}
function isFunction(obj){
	return Object.prototype.toString.call(obj)==="[object Function]";
}
//获取样式
var getStyle = 'defaultView' in document && 'getComputedStyle' in document.defaultView ?
    function (elem, name) {
        return document.defaultView.getComputedStyle(elem, false)[name];
	} :
    function (elem, name) {
        return elem.currentStyle[name] || '';
	};
window.popCityList=popCityList;
})();