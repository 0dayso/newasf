<?php

class CacheAction extends CommonAction{

	function index(){
        $this->display();
    }


    /*清除缓存 */
    function cacheClear(){
        if(file_exists(RUNTIME_FILE)){
            unlink(RUNTIME_FILE);//删除RUNTIME_FILE;
        }
        // 光删除runtime_file还不够,要清空一下Cache文件夹中的文件;代码如下:

        delDirAndFile(RUNTIME_PATH."Cache/");//Cache文件的路径;
        delDirAndFile(RUNTIME_PATH."Data/");//Cache文件的路径;
        delDirAndFile(RUNTIME_PATH."Temp/");//Temp文件的路径;
        $this->success("成功");
    }

    /*
     * 清楚数据缓存
     */
    function dataCacheClear(){
        $cache  = Cache::getInstance();$cache ->clear();
        $this->success("成功");
    }

    /*
     * 清除HTML缓存
     */
    function htmlCacheClear($dir=''){
        $path=$dir?$dir:HTML_PATH;
        if(file_exists($path)){
            unlink($path);//删除RUNTIME_FILE;
        }
        delDirAndFile($path);
        $this->success("成功");
    }
}


