<?php

/**
+------------------------------------------------------------------------------
 * 文件上传类 xheditor 编辑器专用
+------------------------------------------------------------------------------
+------------------------------------------------------------------------------
 */
class XH_UploadFile extends Think
{//类定义开始

    // 表单文件域name
    public $inputName = 'filedata';

    // 上传文件保存路径，结尾不要带/
    public $attachDir = 'Uploads';

    // 子目录创建方式 可以使用hash date
    public $subType   = 'hash';
    public $dateFormat = 'Ym/d';

    // 开启缩略图处理
    public $thumb   =  false;
    // 缩略图最大宽度
    public $thumbMaxWidth;
    // 缩略图最大高度
    public $thumbMaxHeight;
    // 缩略图前缀
    public $thumbPrefix   =  'thumb_';
    //缩略图后缀
    public $thumbSuffix  =  '';
    // 缩略图保存路径
    public $thumbPath = '';
    // 缩略图文件名
    public $thumbFile        =    '';
    //生成缩略图是否删除原图
    public $thumbRemoveOrigin = false;

    // 最大上传大小，默认是2M
    public $maxAttachSize = 2097152;

    public $upExt = 'txt,rar,zip,jpg,jpeg,gif,png,swf,wmv,avi,wma,mp3,mid';//上传扩展名

    public $err = "";   //错误原因
    public $msg = "''";
    public $tempPath = '';

    public $file_size =  0;  //文件名大小
    public $file_ext  = ''; //文件的扩展名

    public $localName = '';  //上传的临时文件

    /**
    +----------------------------------------------------------
     * 架构函数
    +----------------------------------------------------------
     * @access public
    +----------------------------------------------------------
     */
    public function __construct($maxSize='',$allowExts='',$allowTypes='',$savePath='',$saveRule='')
    {
        if( isset($_GET['immediate']) )
        {
            $this->immediate = 1;  //选择上传模式
        }
        $this->tempPath = $this->attachDir.'/'.date("YmdHis").mt_rand(10000,99999).'.tmp';
    }

    /**
    +----------------------------------------------------------
     * 上传文件
    +----------------------------------------------------------
     */
    public function upload()
    {
        if(isset($_SERVER['HTTP_CONTENT_DISPOSITION'])&&preg_match('/attachment;\s+name="(.+?)";\s+filename="(.+?)"/i',$_SERVER['HTTP_CONTENT_DISPOSITION'],$info))
        {//HTML5上传
            file_put_contents($this->tempPath,file_get_contents("php://input"));
            $this->localName = urldecode($info[2]);
        }
        else
        {   //标准表单式上传
            $upfile = @$_FILES[$this->inputName];
            if(!isset($upfile))
            {
                $this->err = '文件域的name错误';
            }
            elseif( !empty($upfile['error']) )
            {
                $this->checkError($upfile['error']);
            }
            elseif(empty($upfile['tmp_name']) || $upfile['tmp_name'] == 'none')
            {
                $this->err = '无文件上传';
            }
            else
            {
                move_uploaded_file($upfile['tmp_name'],$this->tempPath);
                $this->localName = $upfile['name'];
            }
        }
        $this->checkSize();
        $this->checkExt();

        if( $this->err == '')
        {
            return $this->save($this->localName);
        }
        return false;
    }


    /**
    +----------------------------------------------------------
     * 保存上传文件
    +----------------------------------------------------------
     */
    private function save()
    {
        $fileInfo = pathinfo($this->localName);
        $extension = $fileInfo['extension'];
   //
        $attachDir = $this->attachDir.'/'.$this->getSubName();

        if(!is_dir($attachDir))  //创建保存目录
        {
            @mkdir($attachDir, 0777,true);
            @fclose(fopen($attachDir.'/index.htm', 'w'));
        }
     //   PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
        $newFilename = date("His").mt_rand(1000,9999).'.'.$extension;
        $newFilePath = $targetPath = $attachDir.'/'.$newFilename;     //新文件的完整路径

        rename($this->tempPath,$targetPath);  //重命名

        @chmod($targetPath,0755);

        if($this->thumb && in_array(strtolower($extension),array('gif','jpg','jpeg','bmp','png'))) {
            $image =  getimagesize($targetPath);
            if(false !== $image){
                //是图像文件生成缩略图
                $thumbWidth            = $this->thumbMaxWidth;
                $thumbHeight        = $this->thumbMaxHeight;
                $thumbPrefix        = $this->thumbPrefix;
                $thumbSuffix        = $this->thumbSuffix;
                $thumbFile            = $this->thumbFile;
                $thumbPath            = $this->thumbPath?$this->thumbPath:$attachDir.'/';
                // 生成图像缩略图
                import("ORG.Util.Image");

                $thumbname    =    $thumbPath.$thumbPrefix.substr($newFilename,0,strrpos($newFilename, '.')).$thumbSuffix.'.'.$extension;

                Image::thumb($targetPath,$thumbname,'',$thumbWidth,$thumbHeight,true);

                if($this->thumbRemoveOrigin) {
                    // 生成缩略图之后删除原图
                    unlink($targetPath);
                }

                $this->msg = "'!".$this->jsonString(str_replace('./Public',__ROOT__.'/Public',$thumbname))."'";
                $newFilePath = $thumbname;
            }
        }
        else
        {
            $this->msg = "'!".$this->jsonString(str_replace('./Public',__ROOT__.'/Public',$targetPath))."'";
        }
        @unlink($this->tempPath); //删除临时文件
        return $newFilePath; //返回上传成功的文件名
    }


    /**
    +----------------------------------------------------------
     * 获取子目录的名称
    +----------------------------------------------------------
     * @access private
    +----------------------------------------------------------
     * @param array $file  上传的文件信息
    +----------------------------------------------------------
     * @return string
    +----------------------------------------------------------
     */
    private function getSubName()
    {
        $dir   =  date($this->dateFormat,time());
        if(!is_dir($this->attachDir.'/'.$dir)) {
            @mkdir($this->attachDir.'/'.$dir);
        }
        return $dir;
    }

    /**
    +----------------------------------------------------------
     * Ajax 返回
    +----------------------------------------------------------
     */
    public function ajaxReturn()
    {
        exit("{'err':'".$this->jsonString($this->err)."','msg':".$this->msg."}");
    }

    /**
    +----------------------------------------------------------
     * 检查大小
    +----------------------------------------------------------
     */
    private function checkSize()
    {
        $this->file_size = filesize($this->tempPath);
        if( $this->file_size > $this->maxAttachSize )
        {
            $this->err = '请不要上传大小超过'.$this->formatBytes($this->maxAttachSize).'的文件';
            return false;
        }
        return true;
    }

    /**
    +----------------------------------------------------------
     * 检查后缀名
    +----------------------------------------------------------
     */
    private function checkExt()
    {
        $fileInfo = pathinfo($this->localName);
        $this->file_ext = $fileInfo['extension'];
        if( preg_match('/'.str_replace(',','|',$this->upExt).'/i',$this->file_ext) )
        {
            return true;
        }
        $this->err = '上传文件扩展名必需为：'.$this->upExt;
        return false;
    }

    /*
    * 检查错误原因
    */
    private function checkError($error)
    {
        switch($error)
        {
            case '1':
                $this->err = '文件大小超过了php.ini定义的upload_max_filesize值';
                break;
            case '2':
                $this->err = '文件大小超过了HTML定义的MAX_FILE_SIZE值';
                break;
            case '3':
                $this->err = '文件上传不完全';
                break;
            case '4':
                $this->err = '无文件上传';
                break;
            case '6':
                $this->err = '缺少临时文件夹';
                break;
            case '7':
                $this->err = '写文件失败';
                break;
            case '8':
                $this->err = '上传被其它扩展中断';
                break;
            case '999':
            default:
                $this->err = '无有效错误代码';
        }
    }

    private function jsonString($str)
    {
        return preg_replace("/([\\\\\/'])/",'\\\$1',$str);
    }

    private function formatBytes($bytes)
    {
        if($bytes >= 1073741824) {
            $bytes = round($bytes / 1073741824 * 100) / 100 . 'GB';
        } elseif($bytes >= 1048576) {
            $bytes = round($bytes / 1048576 * 100) / 100 . 'MB';
        } elseif($bytes >= 1024) {
            $bytes = round($bytes / 1024 * 100) / 100 . 'KB';
        } else {
            $bytes = $bytes . 'Bytes';
        }
        return $bytes;
    }

}//类定义结束
?>
