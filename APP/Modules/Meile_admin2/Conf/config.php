<?php
return array(
    'SESSION_PREFIX'        => 'admin', // session 前缀
    'COOKIE_PREFIX'         => 'admin',      // Cookie前缀 避免冲突
    'URL_MODEL'             => 2,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式
    'ADMIN_AUTH_KEY' =>'admin',

    'APP_SUB_DOMAIN_DEPLOY' => false,

    'admin_big_menu' => array(
        'Index' => '首页',
        'Member' => '用户管理',
        'News' => '内容管理',
        'Jifen'=>'积分商城',
     //   'SysData' => '数据管理',
        'Order'=>'订单管理',
        'Modules' => '模块',
        'Other'=>'其他',
        'Setting'=>'系统设置',
    ),
    'admin_sub_menu' => array(
        'Common' => array(
            'Index/myInfo' => '修改密码',
            'Index/cache' => '缓存清理',
            'News/add' => '新闻发布'
        ),
        'Setting' => array(
            'index' => '站点配置',
            'emailConfig' => '邮箱配置',
            'smsConfig' => '短信接口配置',
            'safeConfig' => '安全配置'
        ),
        'Member' => array(
            'index' => '注册用户列表',
        ),
        'News' => array(
            'index' => '管理内容',
            'category' => '分类管理',
            'add' => '发布内容',
        ),

        'Jifen' => array(
            'index' => '商品列表',
            'category' => '商品分类管理',
            'add' => '发布商品',
        ),

        'Modules' =>array(
            'cheap'=>'特价机票',
            'pl'=>'评论管理',
        ),

        'SysData' => array(
            'index' => '数据库备份',
            'restore' => '数据库导入',
            'zipList' => '数据库压缩包',
            'repair' => '数据库优化修复'
        ),

        'Other'=>array(
            'complaint'=>'投诉建议',
        ),
        'Access' => array(
            'index' => '后台用户',
            'nodeList' => '节点管理',
            'roleList' => '角色管理',
            'addAdmin' => '添加管理员',
            'addNode' => '添加节点',
            'addRole' => '添加角色',
        ),
    ),
);
?>