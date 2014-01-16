<?php
/**
 * 上传工具 xhEditor 编辑器专用 action
 */
class UploadAction extends CommonAction{
    /*
     * 上传文章图片
     */
    public function contentImage()
    {
        header("Cache-Control: no-cache");
        import("@.ORG.XH_UploadFile");
        $XH_UploadFile              = new XH_UploadFile();                  // 实例化上传类

        $XH_UploadFile->maxAttachSize      = 1024*1024;                      // 允许上传的附件大小(1024)
        $XH_UploadFile->upExt              = 'jpg,gif,png,jpeg';            // 允许上传的附件类型
        $XH_UploadFile->thumb              = true;                          // 开启缩略图
        $XH_UploadFile->thumbPrefix        = 't_';                          // 缩略图前缀
  //      $XH_UploadFile->thumbRemoveOrigin  = true;                          // 缩略图片并删除原图
        $XH_UploadFile->thumbMaxWidth      = '400';                         // 缩略图的最大宽度
        $XH_UploadFile->thumbMaxHeight     = '300';                         // 缩略图的最大高度
        $XH_UploadFile->attachDir          = './Public/uploads/xh';  //保存目录
        $newFilePath    =  $XH_UploadFile->upload();
        if( $newFilePath )  //如果上传成功,则返回新文件路径,更新到数据库中
        {
            $file = str_replace('./Public','',$newFilePath);
            //保存到数据库
        //    $Model                  = M('ArticlesAttachs');
            $data=array();
            $data['articles_id']    = 0;
            $data['url']            = $newFilePath;
            $data['w_time']         = time();
            $data['ext']            = $XH_UploadFile->file_ext;
            $data['size']           = $XH_UploadFile->file_size;

        //    $Model->add($data);
        }
        $XH_UploadFile->ajaxReturn();
    }
}
?>