<?php
return array(
    'SESSION_PREFIX'        => 'admin', // session 前缀
    'COOKIE_PREFIX'         => 'admin',      // Cookie前缀 避免冲突
    'URL_MODEL'             => 3,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式

    'APP_SUB_DOMAIN_DEPLOY' => false,
    'URL_CASE_INSENSITIVE' =>false,  //U 方法生成的url 转小写

    //认证
    'APP_AUTOLOAD_PATH'=>'@.TagLib',
    'SESSION_AUTO_START'=>true,

    'VAR_PAGE'=>'pageNum',
    //lxz
    'PAGE_LISTROWS'=>30,  //分页 每页显示多少条
    'PAGE_NUM_SHOWN'=>10, //分页 页标数字多少个

    'USER_AUTH_ON'=>true,
    'ADMIN_ID'=>1,
    'USER_AUTH_TYPE'=>1,		// 默认认证类型 1 登录认证 2 实时认证
    'USER_AUTH_KEY'=>'authId',	// 用户认证SESSION标记
    'ADMIN_AUTH_KEY'=>'administrator',
    'USER_AUTH_MODEL'=>'User',	// 默认验证数据表模型
    'AUTH_PWD_ENCODER'=>'md5',	// 用户认证密码加密方式
    'USER_AUTH_GATEWAY'=>'/Meile_admin/Public/login',	// 默认认证网关
    'NOT_AUTH_MODULE'=>'Public',		// 默认无需认证模块
    'REQUIRE_AUTH_MODULE'=>'',		// 默认需要认证模块
    'NOT_AUTH_ACTION'=>'',		// 默认无需认证操作
    'REQUIRE_AUTH_ACTION'=>'',		// 默认需要认证操作
    'GUEST_AUTH_ON'=>false,    // 是否开启游客授权访问
    'GUEST_AUTH_ID'=>0,     // 游客的用户ID

    'DB_LIKE_FIELDS'=>'title|remark',
    'RBAC_ROLE_TABLE'=>'asf_role',
    'RBAC_USER_TABLE'=>'asf_role_user',
    'RBAC_ACCESS_TABLE'=>'asf_access',
    'RBAC_NODE_TABLE'=>'asf_node',



);
?>