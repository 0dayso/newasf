<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-9-6
 * Time: 下午4:53
 * To change this template use File | Settings | File Templates.
 */
//公共函数
function toDate($time, $format = 'Y-m-d H:i:s') {
    if (empty ( $time )) {
        return '';
    }
    $format = str_replace ( '#', ':', $format );
    return date ($format, $time );
}


function qtDate($time, $format = 'Y-m-d H:i:s') {
    if (empty ( $time )) {
        return '';
    }
    $format = str_replace ( '#', ':', $format );
    return date ($format, $time );
}


function qtDatet($time, $format = 'Y-m-d') {
    if (empty ( $time )) {
        return '';
    }
    $format = str_replace ( '#', ':', $format );
    return date ($format, $time );
}



// 缓存文件
function cmssavecache($name = '', $fields = '') {
    $Model = D ( $name );
    $list = $Model->select ();
    $data = array ();
    foreach ( $list as $key => $val ) {
        if (empty ( $fields )) {
            $data [$val [$Model->getPk ()]] = $val;
        } else {
            // 获取需要的字段
            if (is_string ( $fields )) {
                $fields = explode ( ',', $fields );
            }
            if (count ( $fields ) == 1) {
                $data [$val [$Model->getPk ()]] = $val [$fields [0]];
            } else {
                foreach ( $fields as $field ) {
                    $data [$val [$Model->getPk ()]] [] = $val [$field];
                }
            }
        }
    }
    $savefile = cmsgetcache ( $name );
    // 所有参数统一为大写
    $content = "<?php\nreturn " . var_export ( array_change_key_case ( $data, CASE_UPPER ), true ) . ";\n?>";
    file_put_contents ( $savefile, $content );
}

function cmsgetcache($name = '') {
    return DATA_PATH . '~' . strtolower ( $name ) . '.php';
}
function getStatus($status, $imageShow = true) {
    switch ($status) {
        case 0 :
            $showText = '禁用';
            $showImg = '<IMG SRC="' . __PUBLIC__ . '/dwz/Images/locked.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="禁用">';
            break;
        case 2 :
            $showText = '待审';
            $showImg = '<IMG SRC="' . __PUBLIC__ . '/dwz/Images/prected.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="待审">';
            break;
        case - 1 :
            $showText = '删除';
            $showImg = '<IMG SRC="' . __PUBLIC__ . '/dwz/Images/del.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="删除">';
            break;
        case 1 :
        default :
            $showText = '正常';
            $showImg = '<IMG SRC="' . __PUBLIC__ . '/dwz/Images/ok.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="正常">';

    }
    return ($imageShow === true) ?  $showImg  : $showText;

}
function getDefaultStyle($style) {
    if (empty ( $style )) {
        return 'blue';
    } else {
        return $style;
    }

}
function IP($ip = '', $file = 'UTFWry.dat') {
    $_ip = array ();
    if (isset ( $_ip [$ip] )) {
        return $_ip [$ip];
    } else {
        import ( "ORG.Net.IpLocation" );
        $iplocation = new IpLocation ( $file );
        $location = $iplocation->getlocation ( $ip );
        $_ip [$ip] = $location ['country'] . $location ['area'];
    }
    return $_ip [$ip];
}

function getNodeName($id) {
    if (Session::is_set ( 'nodeNameList' )) {
        $name = Session::get ( 'nodeNameList' );
        return $name [$id];
    }
    $Group = D ( "Node" );
    $list = $Group->getField ( 'id,name' );
    $name = $list [$id];
    Session::set ( 'nodeNameList', $list );
    return $name;
}

function get_pawn($pawn) {
    if ($pawn == 0)
        return "<span style='color:green'>没有</span>";
    else
        return "<span style='color:red'>有</span>";
}
function get_patent($patent) {
    if ($patent == 0)
        return "<span style='color:green'>没有</span>";
    else
        return "<span style='color:red'>有</span>";
}


function getNodeGroupName($id) {
    if (empty ( $id )) {
        return '未分组';
    }
    if (isset ( $_SESSION ['nodeGroupList'] )) {
        return $_SESSION ['nodeGroupList'] [$id];
    }
    $Group = D ( "Group" );
    $list = $Group->getField ( 'id,title' );
    $_SESSION ['nodeGroupList'] = $list;
    $name = $list [$id];
    return $name;
}

function getCardStatus($status) {
    switch ($status) {
        case 0 :
            $show = '未启用';
            break;
        case 1 :
            $show = '已启用';
            break;
        case 2 :
            $show = '使用中';
            break;
        case 3 :
            $show = '已禁用';
            break;
        case 4 :
            $show = '已作废';
            break;
    }
    return $show;

}

// zhanghuihua@msn.com
function showStatus($status, $id, $callback="") {
    switch ($status){
        case 0 :
            $info = '<a href="__URL__/resume/id/' . $id . '/navTabId/__MODULE__-'.ACTION_NAME.'" target="ajaxTodo" callback="'.$callback.'">恢复</a>';
            break;
        case 2 :
            $info = '<a href="__URL__/pass/id/' . $id . '/navTabId/__MODULE__-'.ACTION_NAME.'" target="ajaxTodo" callback="'.$callback.'">批准</a>';
            break;
        case 1 :
            $info = '<a href="__URL__/forbid/id/' . $id . '/navTabId/__MODULE__-'.ACTION_NAME.' " target="ajaxTodo" callback="'.$callback.'">禁用</a>';
            break;
        case - 1 :
            $info = '<a href="__URL__/recycle/id/' . $id . '/navTabId/__MODULE__-'.ACTION_NAME.'" target="ajaxTodo" callback="'.$callback.'">还原</a>';
            break;
    }
    return $info;
}

/**
+----------------------------------------------------------
 * 获取登录验证码 默认为4位数字
+----------------------------------------------------------
 * @param string $fmode 文件名
+----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function build_verify($length = 4, $mode = 1) {
    return rand_string ( $length, $mode );
}


function getGroupName($id) {
    if ($id == 0) {
        return '无上级组';
    }
    if ($list = F ( 'groupName' )) {
        return $list [$id];
    }
    $dao = D ( "Role" );
    $list = $dao->select ( array ('field' => 'id,name' ) );
    foreach ( $list as $vo ) {
        $nameList [$vo ['id']] = $vo ['name'];
    }
    $name = $nameList [$id];
    F ( 'groupName', $nameList );
    return $name;
}
function sort_by($array, $keyname = null, $sortby = 'asc') {
    $myarray = $inarray = array ();
    # First store the keyvalues in a seperate array
    foreach ( $array as $i => $befree ) {
        $myarray [$i] = $array [$i] [$keyname];
    }
    # Sort the new array by
    switch ($sortby) {
        case 'asc' :
            # Sort an array and maintain index association...
            asort ( $myarray );
            break;
        case 'desc' :
        case 'arsort' :
            # Sort an array in reverse order and maintain index association
            arsort ( $myarray );
            break;
        case 'natcasesor' :
            # Sort an array using a case insensitive "natural order" algorithm
            natcasesort ( $myarray );
            break;
    }
    # Rebuild the old array
    foreach ( $myarray as $key => $befree ) {
        $inarray [] = $array [$key];
    }
    return $inarray;
}

/**
+----------------------------------------------------------
 * 产生随机字串，可用来自动生成密码
 * 默认长度6位 字母和数字混合 支持中文
+----------------------------------------------------------
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
+----------------------------------------------------------
 * @return string
+----------------------------------------------------------
 */
function rand_string($len = 6, $type = '', $addChars = '') {
    $str = '';
    switch ($type) {
        case 0 :
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
            break;
        case 1 :
            $chars = str_repeat ( '0123456789', 3 );
            break;
        case 2 :
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
            break;
        case 3 :
            $chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
            break;
        default :
            // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
            $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
            break;
    }
    if ($len > 10) { //位数过长重复字符串一定次数
        $chars = $type == 1 ? str_repeat ( $chars, $len ) : str_repeat ( $chars, 5 );
    }
    if ($type != 4) {
        $chars = str_shuffle ( $chars );
        $str = substr ( $chars, 0, $len );
    } else {
        // 中文随机字
        for($i = 0; $i < $len; $i ++) {
            $str .= msubstr ( $chars, floor ( mt_rand ( 0, mb_strlen ( $chars, 'utf-8' ) - 1 ) ), 1 );
        }
    }
    return $str;
}
function pwdHash($password, $type = 'md5') {
    return hash ( $type, $password );
}

/* zhanghuihua */
function percent_format($number, $decimals=0) {
    return number_format($number*100, $decimals).'%';
}
/**
 * 动态获取数据库信息
 * @param $tname 表名
 * @param $where 搜索条件
 * @param $order 排序条件 如："id desc";
 * @param $count 取前几条数据
 */
function findList($tname,$where="", $order, $count){
    $m = M($tname);
    if(!empty($where)){
        $m->where($where);
    }
    if(!empty($order)){
        $m->order($order);
    }
    if($count>0){
        $m->limit($count);
    }
    return $m->select();
}
function findById($name,$id){
    $m = M($name);
    return $m->find($id);
}
function attrById($name, $attr, $id){
    $m = M($name);
    $a = $m->where('id='.$id)->getField($attr);
    return $a;
}


//CommonModel 自动继承
function CM($name){
    static $_model = array();
    if(isset($_model[$name])){
        return $_model[$name];
    }
    $class=$name."Model";
    import('@.Model.' . $class);
    if(class_exists($class)){
        $return=new $class();
    }else{
        $return=M("CommonModel:".$name);
    }
    $_model[$name]=$return;

    return $return;
}


function list_to_tree2($list, $pk='id',$pid = 'pid',$child = '_child',$root=0)
{
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if(isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

function showBookStatus($status){
    switch ($status) {
        case -1 :
            $info = '禁用';
            break;
        case 0 :
            $info = '审核中';
            break;
        case 1 :
            $info = '审核完成';
            break;
        default:
            $info = '禁用';
    }
    return $info;
}

function showClaimStatus($status){
    switch ($status) {
        case -1 :
            $info = '禁用';
            break;
        case 0 :
            $info = '未认账';
            break;
        case 1 :
            $info = '审核中';
            break;
        case 2 :
            $info = '已审核';
            break;
        default:
            $info = '禁用';
    }
    return $info;
}

/**
 * 手动检测权限
 * @param string $action 节点
 * @return bool
 */
function auth_check($action='admin'){
    if(getUid()==C('ADMIN_ID')) return true;

    $actionArr=explode('/',$action);
    if(class_exists('RBAC')){
         $accessList=RBAC::getAccessList(getUid());
    }else{
        return true;
    }

    $count=count($actionArr);
    if($count==1){
        $group=GROUP_NAME;
        $module=MODULE_NAME;
        $action=$actionArr[0];
    }elseif($count==2){
        $group=GROUP_NAME;
        $module=$actionArr[0];;
        $action=$actionArr[1];
    }elseif($count==3){
        $group=$actionArr[0];
        $module=$actionArr[1];
        $action=$actionArr[2];
    };

    if(isset($accessList[strtoupper($group)][strtoupper($module)][strtoupper($action)])){
        return true;
    }
    return false;
}

//curl 登陆
function curl_login($url,$fields,$cookie_file){
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
function curl_post($post_url,$post_fields,$cookie_file,$ispost=1,$HTTPHEADER=array(),$issetcookie=0){
    if(!is_file($cookie_file)){
        $cookie_file = dirname(__FILE__)."/cookie.txt";
    }
    $ch = curl_init($post_url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    // curl_setopt($ch, CURLOPT_PROXY ,'127.0.0.1:8087');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $HTTPHEADER);
    //array(
    //'Host:flights.aishangfei.net',
    //'X-Requested-With:XMLHttpRequest', // 设置为Ajax方式
    //'CLIENT-IP:125.210.188.36',
    //'CURLOPT_PROXY:127.0.0.1:8087',
    //'X-FORWARDED-FOR:125.210.188.36',
    //'Referer:http://flights.aishangfei.net/s?flightType=1&tickType=ADT&personNum=1&childNum=0&directFlightsOnly=2&originCode=CAN_1&desinationCode=CPT_1&originDate=2013-12-12&returnDate=2014-01-01&accessId=fly4free', // 冒名顶替, 嘿嘿
    //)
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

    if($ispost){
        curl_setopt($ch, CURLOPT_POST, 1);
      //  echo $post_fields;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        if($issetcookie){
            //echo file_get_contents($cookie_file);
            //echo '<br/>';
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        }
    }
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);//超时
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    $content = curl_exec($ch);
 //   $code= curl_getinfo($ch,CURLINFO_HTTP_CODE);

 //   if(!in_array($code,array('200','301','302'))){
 //       return false;
 //   }
    curl_close($ch);

    return $content;
}