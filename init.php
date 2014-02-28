<?php
//非框架 初始化程序
//搜索引擎 关键字记录
$rfr = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';
//if(!$rfr) $rfr='http://'.$_SERVER['HTTP_HOST'];
if($rfr){
    $p=parse_url($rfr);
    if(isset($p['query'])){
        parse_str($p['query'],$pa);
        $p['host']=strtolower($p['host']);
        $arr_sd_key=array(
            'baidu.com'=>array('word','wd'),
            'google.com'=>'q',
            'sina.com.cn'=>'word',
            'sohu.com'=>'word',
            'msn.com'=>'q',
            'bing.com'=>'q',
            '360.com'=>'q',
            'so.com'=>'q',
            '163.com'=>'q',
            'yahoo.com'=>'p'
        );
        $keyword='';
        $sengine=$p['host'];
        foreach($arr_sd_key as $se=>$kwd){
            if(strpos($p['host'],$se)!==false){
                if(is_array($kwd)){
                    foreach($kwd as $v){
                        if(isset($pa[$v])){
                            $keyword=$pa[$v];
                            break;
                        }
                    }
                }else{
                    $keyword=isset($pa[$kwd])?$pa[$kwd]:'';
                }
                $sengine=$se;
                break;
            }
        }
        if($keyword){
            session_start();
            $_SESSION['s_sengine']=$sengine;
            $_SESSION['s_keyword']=$keyword;
        }
    }
}

?>