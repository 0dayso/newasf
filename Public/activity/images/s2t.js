var Default_isFT = 0
var StranIt_Delay = 100
var StranLink_Obj=document.getElementById("StranLink")
function SetCookie(name,value)//两个参数，一个是cookie的名子，一个是值
{
    var Days = 30; //此 cookie 将被保存 30 天
    var exp = new Date();    //new Date("December 31, 9998");
    exp.setTime(exp.getTime() + Days*24*60*60*1000);
    document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
}
function getCookie(name)//取cookies函数        
{
    var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
     if(arr != null) return unescape(arr[2]); return null;

}
function delCookie(name)//删除cookie
{
    var exp = new Date();
    exp.setTime(exp.getTime() - 1);
    var cval=getCookie(name);
    if(cval!=null){
        document.cookie= name + "="+cval+";expires="+exp.toGMTString();
    }
}
//加载页面
function STSession(){
    var s=getCookie("lag");
    StranBody();
//    if(s!=null && s!=""){
//        document.getElementById("StranLink").value="简体版";
//        StranBody();
//    }
}
function StranText(txt,toFT,chgTxt)
{
if(txt==""||txt==null)return ""
toFT=toFT==null?BodyIsFt:toFT
if(chgTxt)txt=txt.replace((toFT?"简":"繁"),(toFT?"繁":"简"))
if(toFT){return Traditionalized(txt)}
else {return Simplized(txt)}
}
function StranByCookie(){
     var s=getCookie("lag");
    if(s==null || s==""){
        document.getElementById("StranLink").value="简体版";
        SetCookie("lag","ft");
    }
    else{
        delCookie("lag");
    }
    StranBody();
}
function StranBody(fobj)
{
    if(typeof(fobj)=="object"){var obj=fobj.childNodes}
    else  
    {
    StranLink_Obj=document.getElementById("StranLink")
    var tmptxt=StranLink_Obj.innerHTML.toString()
    if(tmptxt.indexOf("简")<0)
    {
    BodyIsFt=1
    StranLink_Obj.innerHTML=StranText(tmptxt,0,1)
    document.getElementById("StranLink").title=StranText(document.getElementById("StranLink").title,0,1)
    }
    else
    {
    BodyIsFt=0
    StranLink_Obj.innerHTML=StranText(tmptxt,1,1)
    document.getElementById("StranLink").title=StranText(document.getElementById("StranLink").title,1,1)
    }
    setCookie(JF_cn,BodyIsFt,7)
    var obj=document.body.childNodes
    }
    for(var i=0;i<obj.length;i++)
    {
    var OO=obj.item(i)
    if("||BR|HR|TEXTAREA|OBJECT|".indexOf("|"+OO.tagName+"|")>0||OO==StranLink_Obj)continue;
    if(OO.title!=""&&OO.title!=null)OO.title=StranText(OO.title);
    if(OO.alt!=""&&OO.alt!=null)OO.alt=StranText(OO.alt);
    if(OO.tagName=="INPUT"&&OO.value!=""&&OO.type!="text"&&OO.type!="hidden")OO.value=StranText(OO.value);
    if(OO.nodeType==3){OO.data=StranText(OO.data)}
    else StranBody(OO)
    }
    }


    function Traditionalized(cc){
    var str='',ss=JTPYStr(),tt=FTPYStr();
    for(var i=0;i<cc.length;i++)
    {
    if(cc.charCodeAt(i)>10000&&ss.indexOf(cc.charAt(i))!=-1)str+=tt.charAt(ss.indexOf(cc.charAt(i)));
      else str+=cc.charAt(i);
    }
    return str;
}
function Simplized(cc){
var str='',ss=JTPYStr(),tt=FTPYStr();
for(var i=0;i<cc.length;i++)
{
if(cc.charCodeAt(i)>10000&&tt.indexOf(cc.charAt(i))!=-1)str+=ss.charAt(tt.indexOf(cc.charAt(i)));
  else str+=cc.charAt(i);
}
return str;
}

function setCookie(name, value)
{
var argv = setCookie.arguments;
var argc = setCookie.arguments.length;
var expires = (argc > 2) ? argv[2] : null;
if(expires!=null)
{
var LargeExpDate = new Date ();
LargeExpDate.setTime(LargeExpDate.getTime() + (expires*1000*3600*24));
}
document.cookie = name + "=" + escape (value)+((expires == null) ? "" : ("; expires=" +LargeExpDate.toGMTString()));
}

function getCookie(Name)
{
var search = Name + "="
if(document.cookie.length > 0)  
{
offset = document.cookie.indexOf(search)
if(offset != -1)  
{
offset += search.length
end = document.cookie.indexOf(";", offset)
if(end == -1) end = document.cookie.length
return unescape(document.cookie.substring(offset, end))
}
else return ""
}
}


if (StranLink_Obj)
{
var JF_cn="ft"+self.location.hostname.toString().replace(/\./g,"")
var BodyIsFt=getCookie(JF_cn)
if(BodyIsFt!="1")BodyIsFt=Default_isFT
with(StranLink_Obj)
{
if(typeof(document.all)!="object")
{
href="javascript:StranBody()"
}
else
{
href="#";
onclick= new Function("StranBody();return false")
}
title=StranText("切换到简体中文版本",1,1)
innerHTML=StranText(innerHTML,1,1)
}
if(BodyIsFt=="1"){setTimeout("StranBody()",StranIt_Delay)}
}