<?php
//home 分组配置
return array(
    'DEFAULT_THEME'  => 'Default',
    'TMPL_DETECT_THEME' => true, // 自动侦测模板主题
    'THEME_LIST'=>'Default,Sl',//支持的模板主题项

    'HTML_CACHE_ON'=>true, // 开启静态缓存
    'HTML_FILE_SUFFIX'  =>  '.shtml', // 设置静态缓存后缀为.shtml
    'HTML_CACHE_RULES'=> array(
    //    'ActionName(小写)'            => array('静态规则', '静态缓存有效期', '附加规则'),
    //    'ModuleName(小写)'            => array('静态规则', '静态缓存有效期', '附加规则'),
    //    'ModuleName(小写):ActionName(小写)' => array('静态规则', '静态缓存有效期', '附加规则'),
        '*'=>array('{$_SERVER.REQUEST_URI|md5}'),
        //…更多操作的静态规则
    )
);