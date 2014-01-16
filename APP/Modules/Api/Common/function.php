<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 13-11-5
 * Time: 下午4:25
 */
function login($url,$fields,$cookie_file){
//bh="+sbh+"&method=checkLogin&kl="+skl+"&call="+scall+"&callnum="+scallnum+"&randtime="+ntime
    //163的用户登陆地址
    //$url = "http://www.17u.net/login/login_check.asp?PasswordGet=$password&UsernameGet=$name&gotoUrl=&pageid=&postUrl=&x=93&y=16&zfxurl=";
    //post 要提交的数据
    //$fields = "bh=$name&method=checkLogin&kl=$password&call=&callnum=&randtime";
    //启动一个CURL会话
    $ch = curl_init();
    // 要访问的地址
    curl_setopt($ch, CURLOPT_URL, $url);
    // 对认证证书来源的检查，0表示阻止对证书的合法性的检查。
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    // 从证书中检查SSL加密算法是否存在
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
    //模拟用户使用的浏览器，在HTTP请求中包含一个”user-agent”头的字符串。
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");
    //发送一个常规的POST请求，类型为：application/x-www-form-urlencoded，就像表单提交的一样。
    curl_setopt($ch, CURLOPT_POST, 1);
    //要传送的所有数据，如果要传送一个文件，需要一个@开头的文件名
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    //连接关闭以后，存放cookie信息的文件名称
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    // 包含cookie信息的文件名称，这个cookie文件可以是Netscape格式或者HTTP风格的header信息。
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    // 设置curl允许执行的最长秒数
    //curl_setopt($ch, CURLOPT_TIMEOUT, 6);
    // 获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    // 执行操作
    $result = curl_exec($ch);
    if ($result == NULL) {
        echo "Error:<br>";
        echo curl_errno($ch) . " - " . curl_error($ch) . "<br>";
    }else{
        return $result;
    }
    curl_close($ch);
}



//访问操作可以post也可以不
function post($post_url,$post_fields,$cookie_file,$ispost='1',$issetcookie=0){
    $ch = curl_init($post_url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //echo file_get_contents($cookie_file);
    //echo '<br/>';
    if($ispost){
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        if($issetcookie){
            //echo file_get_contents($cookie_file);
            //echo '<br/>';
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        }
    }
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    $content = curl_exec($ch);
    curl_close($ch);
    return $content;
}

