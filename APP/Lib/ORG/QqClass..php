<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-9-2
 * Time: 下午3:45
 * To change this template use File | Settings | File Templates.
 */
class qq{
    public  $sid;
    public  $qq;
    public  $gqq;

  //  sendmsg($sid,'447058782','123 ');

   function __construct($qq,$password){
       if($qq && $password)
          $this->sid=qqlogin($qq,$password);
   }

    function getqqMsg($gqq){
        $data=$this->getmsg($gqq);
        foreach($data as $v){
           echo $v['title'].'<br />';
         echo $v['msg'].'<br />';
          echo '------------------------<br />';
        }
    }

    function sendmsg($to_num,$msg){
        $sid=$this->sid;
        $params = array();
        $params["msg"] = $msg;
        $params["u"] = $to_num;
        $params["saveURL"] = 0;
        $params["do"] = "send";
        $params["on"] = 1;
        $params["aid"] = "发送";
        $url = "http://q16.3g.qq.com/g/s?sid=" . $sid;
        $data = http_post($url, $params);
        if(preg_match('/消息发送成功/',$data)) echo '发送成功<br />';
        else  echo '发送失败';
    }

    function qqlogin($qq_num,$qq_pwd){
        $data = $this->http_get('http://pt.3g.qq.com/');
        $action = preg_match("/action=\"(.+)?\"/", $data, $matches);
        $action = $matches[1];
        $params = array();
        $params["login_url"] = 'http://pt.3g.qq.com/s?aid=nLogin';
        $params["sidtype"] = 1;
        $params["loginTitle"] = '手机腾讯网';
        $params["bid"] = 0;
        $params["qq"] = $qq_num;
        $params["pwd"] = $qq_pwd;
        $params["loginType"] =1;
        $data = http_post($action, $params,1);
        $action = preg_match("/sid=(.+?)&/", $data, $matches);
        $sid = $matches[1];
        if($sid){
            $this->sid=$sid;
            return $sid;
        }else echo '登录失败';
    }
    function getMsg($qq_num){
        $url = "http://q16.3g.qq.com/g/s?sid=" . $this->sid . "&u=" . $qq_num . "&saveURL=0&aid=nqqChat";
        $data = $this->http_get($url);
        preg_match_all('/<div class=\"main-module bm-gray\">\s+<p class=\"(?:ft-s-gray|ft-s ft-cl2)\">(.+?)<\/p>\s+<p>(.+?)<\/p><\/div>/s',str_replace("\r\n","",$data),$m);
        foreach($m[1] as $k=>$v){
            $tmp['title']=$v;
            $tmp['msg']=$m[2][$k];
            $r[]=$tmp;
        }
        return $r;
    }

    function http_get($url,$header=0){
        $opt = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => $header,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_USERAGENT=>'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.2.149.29 Safari/525.13'
        );
        return $this->curl_run($opt);
    }
    function http_post($url,$data,$header=0){
        $opt = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => $header,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_USERAGENT=>'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.2.149.29 Safari/525.13'
        );
        return $this->curl_run($opt);
    }
    function curl_run($opt){
        $ch=curl_init();
        curl_setopt_array($ch,$opt);
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }

}