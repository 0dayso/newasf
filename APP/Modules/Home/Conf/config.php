<?php
//home 分组配置
return array(
    'DEFAULT_THEME'  => 'Default',
    'TMPL_DETECT_THEME' => true, // 自动侦测模板主题
    'THEME_LIST'=>'Default,Sl',//支持的模板主题项

    'HTML_CACHE_ON'=>true, // 开启静态缓存
    'HTML_FILE_SUFFIX'  =>  '.shtml', // 设置静态缓存后缀为.shtml
    'HTML_CACHE_TIME' =>60,
    'HTML_CACHE_RULES'=> array(
        'Index:index'         => array('{$_SERVER.HTTP_HOST}_{:group}_{:module}_{:action}'),
        'Activity:'           => array('{$_SERVER.HTTP_HOST}_{:group}_{:module}_{:action}'),
        'Jifen:'            => array('{$_SERVER.HTTP_HOST}_{:group}_{:module}_{:action}_{$_SERVER.REQUEST_URI|md5}'),
        'News'        => array('{$_SERVER.HTTP_HOST}_{:group}_{:module}_{:action}_{$_SERVER.REQUEST_URI|md5}'),
        'Adviser:reviewList'            => array('{$_SERVER.HTTP_HOST}_{:group}_{:module}_{:action}_{id}_{p}',3600),
        'Airline:pl'            => array('{$_SERVER.HTTP_HOST}_{:group}_{:module}_{:action}_{from}_{to}_{p}',3600),
        'help:'            => array('{$_SERVER.HTTP_HOST}_{:group}_{:module}_{:action}'),
        'about:'            => array('{$_SERVER.HTTP_HOST}_{:group}_{:module}_{:action}'),

        // '*'=>array('{$_SERVER.REQUEST_URI|md5}'),
    ),

);