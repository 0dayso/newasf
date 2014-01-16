<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-9-2
 * Time: 下午5:16
 * To change this template use File | Settings | File Templates.
 */
//需求模型
class VideoAction extends CommonAction{
    function _before_index(){
        $this->relation=true;

    }
     function  upload(){
        if (IS_POST){
                import('ORG.Net.UploadFile');
                $upload = new UploadFile();// 实例化上传类
                $upload->maxSize  = 3145728 ;// 设置附件上传大小
                $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath =  './Public/uploads/video/';// 设置附件上传目录
                //设置需要生成缩略图，仅对图像文件有效
                $upload->thumb = true;
                // 设置引用图片类库包路径
                $upload->imageClassPath = 'ORG.Util.Image';
                //设置需要生成缩略图的文件后缀
                $upload->thumbPrefix = 'm_,s_';  //生产2张缩略图
                $upload->thumbMaxWidth = '250,108';
                //设置缩略图最大高度
                $upload->thumbMaxHeight = '150,60';
                //设置上传文件规则
                $upload->saveRule = uniqid;
                //删除原图
                //   $upload->thumbRemoveOrigin = true;
                if(!$upload->upload()) {// 上传错误提示错误信息
                    echo $upload->getErrorMsg();
                }else{// 上传成功 获取上传文件信息
                    $info =  $upload->getUploadFileInfo();
                }
                $data=array(
                    "id"=>"1000",
                    "fileName"=>$info[0]['savename'],
                    'thumbnail'=>$info[0]['savename'],
                    "attachmentPath"=>"/upload/测试文件.txt",
                    "attachmentSize"=>"1024"
                );
                if($info){
                    $this->ajaxReturn($data);
                }else{
                    $this->error("提交失败");

                }
        }else{
            $this->display();
        }
      }

    function update() {
        $model = D('Video');
        $data=$_POST;
        $data['thumbnail']=$_POST['img_thumbnail'];
        if (false === $model->create($data)) {
            $this->error ( $model->getError () );
        }

        // 更新数据
        $list=$model->where("id=".I('id'))->save();
        if (false !== $list) {
            //成功提示
            $this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
            $this->success ('编辑成功!');
        } else {
            //错误提示
            $this->error ('编辑失败!'.$model->getError());
        }
    }
}