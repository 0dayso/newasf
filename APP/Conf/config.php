<?php
$config=array(
    'APP_GROUP_LIST'	=>'Home,Member,M,Meile_admin,Meile_admin2,Api',//分组
    'APP_GROUP_MODE'    =>  1,
    'APP_GROUP_PATH'    =>  'Modules',

//    'SESSION_PREFIX'        => 'aishangfei', // session 前缀
//    'COOKIE_PREFIX'         => 'aishangfei',      // Cookie前缀 避免冲突

 //  'COOKIE_DOMAIN'         => ".aishangfei.com",      // Cookie有效域名

    //'LAYOUT_ON'=>true,

 //   'TOKEN_ON'=>true,  // 是否开启令牌验证 默认关闭
    'TOKEN_NAME'=>'__hash__',    // 令牌验证的表单隐藏字段名称
    'TOKEN_TYPE'=>'md5',  //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET'=>true,  //令牌验证出错后是否重置令牌 默认为true

    'SHOW_PAGE_TRACE'=>true,            //页面右下脚 显示运行的信息
//	'APP_STATUS' => 'debug',
//	'APP_STATUS' => 'test',
    'APP_SUB_DOMAIN_DEPLOY' => true,
    'APP_SUB_DOMAIN_RULES' => array(
        //     'admin'    => array('Admin/'), // 二级域名
        'mm'    => array('M/'), // 二级域名
        'm'    => array('M/'), // 二级域名
        //		'*'        => array('te','DDD=*'), // 二级泛域名
        //      'asf.host' => array('admin/admin','var=1'), // 三级域名
    ),

    'DB_TYPE'			=>	'mysql',
    'DB_HOST'			=>	'192.168.4.206',
    'DB_NAME'			=>	'newasf002',
    'DB_USER'			=>	'root',
    'DB_PWD'			=>	'77169',
    'DB_PORT'			=>	'3306',
    'DB_PREFIX'			=>	'asf_',

 //   'DATA_CACHE_TYPE' => 'Memcache',
    'DATA_CACHE_TIME' =>60,
    'MEMCACHE_HOST' => 'tcp://192.168.4.127:11223',

    'DB_SQL_BUILD_CACHE' => true,
    'DB_SQL_BUILD_QUEUE' => 'Memcache',
    'DB_SQL_BUILD_LENGTH' => 20, // SQL缓存的队列长度

    /* URL设置 */
    'URL_CASE_INSENSITIVE'  => true,   // 默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'             => 2,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
    // 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式，提供最好的用户体验和SEO支持
    'URL_HTML_SUFFIX'       => '',  // URL伪静态后缀设置
    'URL_DENY_SUFFIX'       =>  'ico|png|gif|jpg', // URL禁止访问的后缀设置
    'URL_PARAMS_BIND'       =>  true, // URL变量绑定到Action方法参数
    'URL_404_REDIRECT'      =>  '', // 404 跳转页面 部署模式有效
//    'URL_CASE_INSENSITIVE' =>true,  //U 方法生成的url 转小写

    'URL_ROUTER_ON'   => true, //开启路由
    'URL_ROUTE_RULES' => array( //定义路由规则
        'adviser/company/:company\d' => "adviser/:company",
        '/^adviser\/review_(\d+)/i' => "adviser/reviewList?id=:1", //reviewUser
        '/^adviser\/(\d+)\/review/i' => "adviser/reviewUser?id=:1", //reviewUser
        '/^news\/([a-z0-9-]+)$/i'=>'news/lists?alias=:1',
        '/^news\/(\w+)\/(\d+)$/i'=>'news/content?id=:2',
        '/^api\/top_status/i'=>'Api/Block/topStatus',
        '/^api\/footer_js/i'=>'Api/Block/footerJs',
        '/^api\/tjjp/i'=>'Api/Block/tjjp',
    ),

/*    'SHOW_RUN_TIME'    => true, // 运行时间显示
    'SHOW_ADV_TIME'    => true, // 显示详细的运行时间
    'SHOW_DB_TIMES'    => true, // 显示数据库查询和写入次数
    'SHOW_CACHE_TIMES' => true, // 显示缓存操作次数
    'SHOW_USE_MEM'     => true, // 显示内存开销
    'SHOW_LOAD_FILE'   => true, // 显示加载文件数
    'SHOW_FUN_TIMES'   => true, // 显示函数调用次数*/

    'TMPL_TEMPLATE_SUFFIX'  => '.html',     // 默认模板文件后缀
    'TMPL_ACTION_ERROR' => 'Public:success',  //提示页面
    'TMPL_ACTION_SUCCESS' => 'Public:success',  //提示页面

    'LOG_RECORD' => true, // 开启日志记录
    'LOG_LEVEL'  =>'EMERG,ALERT', // 只记录EMERG ALERT CRIT ERR 错误
);
$web_config = include_once "web_config.php";
return array_merge($config,$web_config);

?>