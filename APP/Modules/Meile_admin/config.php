<?php
$config=array(
    'APP_GROUP_LIST'	=>'Home,Meile_admin,Meile_admin2,Api,M',//分组
    'APP_GROUP_MODE'    =>  1,
    'APP_GROUP_PATH'    =>  'Modules',

    'TMPL_TEMPLATE_SUFFIX'  => '.html',     // 默认模板文件后缀
    'TMPL_ACTION_ERROR' => 'Public:success',  //提示页面
    'TMPL_ACTION_SUCCESS' => 'Public:success',  //提示页面

    'COOKIE_DOMAIN'         => ".aishangfei.net",      // Cookie有效域名

    //'LAYOUT_ON'=>true,

 //   'SHOW_PAGE_TRACE'=>true,            //页面右下脚 显示运行的信息
//	'APP_STATUS' => 'debug',
//	'APP_STATUS' => 'test',
    'APP_SUB_DOMAIN_DEPLOY' => true,
    'APP_SUB_DOMAIN_RULES' => array(
        //     'admin'    => array('Admin/'), // 二级域名
        'm'    => array('M/'), // 二级域名
        //		'*'        => array('te','DDD=*'), // 二级泛域名
        //      'asf.host' => array('admin/admin','var=1'), // 三级域名
        //   '*.host'   => array('home'), // 三级泛域名
    ),

    'DB_TYPE'			=>	'mysql',
    'DB_HOST'			=>	'localhost',
    'DB_NAME'			=>	'newasf04',
    'DB_USER'			=>	'root',
    'DB_PWD'			=>	'gzyiqifei',
    'DB_PORT'			=>	'3306',
    'DB_PREFIX'			=>	'asf_',


    /* URL设置 */
    'URL_CASE_INSENSITIVE'  => true,   // 默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'             => 2,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
    // 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式，提供最好的用户体验和SEO支持
    'URL_HTML_SUFFIX'       => '',  // URL伪静态后缀设置
    //  'URL_DENY_SUFFIX'       =>  'ico|png|gif|jpg', // URL禁止访问的后缀设置
    'URL_PARAMS_BIND'       =>  true, // URL变量绑定到Action方法参数
    'URL_404_REDIRECT'      =>  '', // 404 跳转页面 部署模式有效
//    'URL_CASE_INSENSITIVE' =>true,  //U 方法生成的url 转小写
	
	'DEFAULT_FILTER'        => 'htmlspecialchars', //I方法的所有获取变量都会进行htmlspecialchars过滤

    'URL_ROUTER_ON'   => true, //开启路由
    'URL_ROUTE_RULES' => array( //定义路由规则
        'adviser/company/:company\d' => "adviser/:company",
        '/^adviser\/review_(\d+)/i' => "adviser/reviewList?id=:1", //reviewUser
        '/^adviser\/(\d+)\/review/i' => "adviser/reviewUser?id=:1", //reviewUser
        '/^news\/([a-z0-9-]+)$/i'=>'news/lists?alias=:1',
        '/^news\/(\w+)\/(\d+)/i'=>'news/content?id=:2',
        '/^api\/top_status/i'=>'Api/Block/topStatus',
        '/^api\/footer_js/i'=>'Api/Block/footerJs',
        '/^api\/tjjp/i'=>'Api/Block/tjjp',
    ),




    'LOG_RECORD' => true, // 开启日志记录
    'LOG_LEVEL'  =>'EMERG,ALERT', // 只记录EMERG ALERT CRIT ERR 错误


);
$web_config = include "web_config.php";
return array_merge($config,$web_config);

?>