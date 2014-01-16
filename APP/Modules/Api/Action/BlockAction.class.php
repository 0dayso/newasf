<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-7-23
 * Time: 下午5:36
 * To change this template use File | Settings | File Templates.
 */

class BlockAction extends IniAction{
    function index(){
    }
    function topStatus(){
        $userinfo=$this->userinfo;
        if($userinfo){
            $name=$userinfo['name']?$userinfo['name']:$userinfo['username'];
            $html1='<span class="left">您好,<a title="进入会员中心" href="'.U('/Member/index','',true,'',true).'">'.$name.'</a>普通会员<a href="'.U('/member/out','','','',true).'">退出</a>
				</span>';

            $html2='<a id="myAisf_w" href="'.U('/member','','','',true).'">我的爱尚飞</a>
                 <ul id="myAisf_u">
                     <li><a href="'.U('/member/booking','','','',true).'">我的订单</a></li>
                     <li><a href="'.U('/member/information','','','',true).'">个人资料</a></li>
                     <li><a  target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin='.$userinfo['user']['qq'].'&site=qq&menu=yes">咨询我的顾问</a></li>
                     <li><a href="'.U('/member/editpwd','','','',true).'">修改密码</a></li>
                 </ul>
                 <a id="myAisf_t"></a>';
        }else{
        $html1 ='<span class="left">您好，欢迎光临爱尚飞国际机票网<a href="'.U('/member/login','','','',true).'">登陆</a><a href="'.U('/member/register','','','',true).'">注册</a></span>';
html;
        }

        $html3 = '<a class="spr gz"></a><a href="http://e.weibo.com/aishangfei2012?ref=http%3A%2F%2Fweibo.com%2Fu%2F2014524587%3Fwvr%3D5%26" class="ml0" target="_blank">关注我们</a><a class="spr wx"></a><a href="javascript:;" class="ml0" id="weixin_me">微信我们</a><a href="'.U('/complaint','','','',true).'" class="ts">投诉建议</a><a href="'.U('/help','','','',true).'" class="bz">帮助中心</a><a class="spr dh"></a>';

        $str=<<< html
        document.writeln('$html1 <span class="right"> $html2 $html3</span>');
html;
        $str = str_replace("\r\n","",$str);
        $str = str_replace("\r","",$str);
        $str = str_replace("\n","",$str);
        //return trim($str);
        echo $str;
    }


    function footerJs(){
        $str=<<< html
        document.writeln('<script type="text/javascript" src="http://www.aishangfei.net/iframe/js/main.js"></script>');
html;
       echo  $str;

    }


    function tjjp(){
        $html=<<<html
        document.writeln('<h2 class=\"titbk\"><a class=\"spr3 tj\"></a><span class=\"left\">特价机票</span><span class=\"right\"><label class=\"lb0\">温馨提示：</label><label class=\"lb1\">爱尚飞服务时间：</label>9:00-21:00(周一至周五)，9:00-18:00(周六日)</span></h2>');
document.writeln('                    <h3>');
document.writeln('                        <label class=\"lb2 left\">选择出发城市：</label>');
document.writeln('                        <select class=\"left\">');
document.writeln('                            <option value=\"gz\">广州出发</option>');
document.writeln('                            <option value=\"bj\">北京出发</option>');
document.writeln('                            <option value=\"sh\">上海出发</option>');
document.writeln('                            <option value=\"sz\">深圳出发</option>');
document.writeln('                        </select>');
document.writeln('                        <label class=\"left lb3\">目的地：</label>');
document.writeln('                        <span class=\"left\">');
document.writeln('                            <a href=\"javascript:;\">美洲</a>');
document.writeln('                            <a href=\"javascript:;\" class=\"active\">东南亚</a>');
document.writeln('                            <a href=\"javascript:;\">日韩及港台</a>');
document.writeln('                            <a href=\"javascript:;\">其他</a>');
document.writeln('                        </span>');
document.writeln('                    </h3>');
document.writeln('                    <!--这里列出6个li-->');
document.writeln('                    <ul class=\"conbk\">');
document.writeln('                        <li>');
document.writeln('                        	<span class=\"span0\"><a class=\"a0\">广州</a><a class=\"spr3 rec\"></a><a class=\"a1\">罗马</a></span>');
document.writeln('                            <span class=\"span1\"><a class=\"zz\">中转</a></span>');
document.writeln('                            <span class=\"span2\"><a class=\"time\">2013-06-30</a><a>截止</a></span>');
document.writeln('                            <span class=\"span3\"><a class=\"sign\"><!--这里是航空公司图标--><img src=\"images/df_10.gif\" alt=\"sign\" /></a><a>多家航空组合</a></span>');
document.writeln('                            <span class=\"span4\"><a>往返</a><a class=\"jg\">￥2730</a></span>');
document.writeln('                            <span class=\"end span5\"><a class=\"spr3 yd\" href=\"javascript:;\">预订</a></span>');
document.writeln('                        </li>');
document.writeln('                        <li>');
document.writeln('                        	<span class=\"span0\"><a class=\"a0\">广州</a><a class=\"spr3 rec\"></a><a class=\"a1\">罗马</a></span>');
document.writeln('                            <span class=\"span1\"><a class=\"zz\">中转</a></span>');
document.writeln('                            <span class=\"span2\"><a class=\"time\">2013-06-30</a><a>截止</a></span>');
document.writeln('                            <span class=\"span3\"><a class=\"sign\"><!--这里是航空公司图标--><img src=\"images/df_10.gif\" alt=\"sign\" /></a><a>多家航空组合</a></span>');
document.writeln('                            <span class=\"span4\"><a>往返</a><a class=\"jg\">￥2730</a></span>');
document.writeln('                            <span class=\"end span5\"><a class=\"spr3 yd\" href=\"javascript:;\">预订</a></span>');
document.writeln('                        </li>');
document.writeln('                        <li>');
document.writeln('                        	<span class=\"span0\"><a class=\"a0\">广州</a><a class=\"spr3 rec\"></a><a class=\"a1\">罗马</a></span>');
document.writeln('                            <span class=\"span1\"><a class=\"zz\">中转</a></span>');
document.writeln('                            <span class=\"span2\"><a class=\"time\">2013-06-30</a><a>截止</a></span>');
document.writeln('                            <span class=\"span3\"><a class=\"sign\"><!--这里是航空公司图标--><img src=\"images/df_10.gif\" alt=\"sign\" /></a><a>多家航空组合</a></span>');
document.writeln('                            <span class=\"span4\"><a>往返</a><a class=\"jg\">￥2730</a></span>');
document.writeln('                            <span class=\"end span5\"><a class=\"spr3 yd\" href=\"javascript:;\">预订</a></span>');
document.writeln('                        </li>');
document.writeln('                        <li>');
document.writeln('                        	<span class=\"span0\"><a class=\"a0\">广州</a><a class=\"spr3 rec\"></a><a class=\"a1\">罗马</a></span>');
document.writeln('                            <span class=\"span1\"><a class=\"zz\">中转</a></span>');
document.writeln('                            <span class=\"span2\"><a class=\"time\">2013-06-30</a><a>截止</a></span>');
document.writeln('                            <span class=\"span3\"><a class=\"sign\"><!--这里是航空公司图标--><img src=\"images/df_10.gif\" alt=\"sign\" /></a><a>多家航空组合</a></span>');
document.writeln('                            <span class=\"span4\"><a>往返</a><a class=\"jg\">￥2730</a></span>');
document.writeln('                            <span class=\"end span5\"><a class=\"spr3 yd\" href=\"javascript:;\">预订</a></span>');
document.writeln('                        </li>');
document.writeln('                        <li>');
document.writeln('                        	<span class=\"span0\"><a class=\"a0\">广州</a><a class=\"spr3 rec\"></a><a class=\"a1\">罗马</a></span>');
document.writeln('                            <span class=\"span1\"><a class=\"zz\">中转</a></span>');
document.writeln('                            <span class=\"span2\"><a class=\"time\">2013-06-30</a><a>截止</a></span>');
document.writeln('                            <span class=\"span3\"><a class=\"sign\"><!--这里是航空公司图标--><img src=\"images/df_10.gif\" alt=\"sign\" /></a><a>多家航空组合</a></span>');
document.writeln('                            <span class=\"span4\"><a>往返</a><a class=\"jg\">￥2730</a></span>');
document.writeln('                            <span class=\"end span5\"><a class=\"spr3 yd\" href=\"javascript:;\">预订</a></span>');
document.writeln('                        </li>');
document.writeln('                        <li>');
document.writeln('                        	<span class=\"span0\"><a class=\"a0\">广州</a><a class=\"spr3 rec\"></a><a class=\"a1\">罗马</a></span>');
html;
        echo $html;

    }

}