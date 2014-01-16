<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-7-25
 * Time: 上午9:44
 * To change this template use File | Settings | File Templates.
 */
class MallModel extends RelationModel{
    protected $_link = array(
        'user'=> array( //
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'user',
            'user_id'=>'id',
            'mapping_fields'=>'id,username,name',
        ),
    );

    public function listNews($firstRow = 0, $listRows = 20,$where="",$order='desc'){
        $M = D("mall");
        $list = $M->where($where)->relation(true)->limit("$firstRow , $listRows")->order('id '.$order)->select();

        $statusArr = array("审核状态", "已发布状态");
    //    $aidArr = M("Admin")->field("`aid`,`email`,`nickname`")->select();
    //    foreach ($aidArr as $k => $v) {
     //       $aids[$v['aid']] = $v;
     //   }
        unset($aidArr);
        $cidArr = M("mall_category")->field("`cid`,`name`")->select();
        foreach ($cidArr as $k => $v) {
            $cids[$v['cid']] = $v;
        }
        unset($cidArr);

        foreach ($list as $k => $v) {
         //   $list[$k]['aidName'] =$aids[$v['aid']]['nickname'] == '' ? $aids[$v['aid']]['email'] : $aids[$v['aid']]['nickname'];
            $list[$k]['status'] = $statusArr[$v['status']];
            $list[$k]['cidName'] = $cids[$v['cid']]['name'];
            $list[$k]['type_name'] = $v['type']==0?'积分':'爱钻';
        }
        return $list;
    }

    public function category() {
        if (IS_POST) {
            $act = $_POST['act'];
            $data = $_POST['data'];
            $data['name'] = addslashes($data['name']);
            $M = M("mall_category");
            if ($act == "add") { //添加分类
                unset($data[cid]);
                if ($M->where($data)->count() == 0){
                    return ($M->add($data)) ? array( 'info' => '分类 ' . $data['name'] . ' 已经成功添加到系统中','status' => 1, 'url' => U('Jifen/category', array('time' => time()))) : array('status' => 0, 'info' => '分类 ' . $data['name'] . ' 添加失败');
                } else {
                    return array( 'info' => '系统中已经存在分类' . $data['name'],'status' => 0);
                }
            } else if ($act == "edit") { //修改分类
                if (empty($data['name'])) {
                    unset($data['name']);
                }
                if ($data['pid'] == $data['cid']) {
                    unset($data['pid']);
                }
                return ($M->save($data)) ? array('status' => 1, 'info' => '分类 ' . $data['name'] . ' 已经成功更新', 'url' => U('Jifen/category', array('time' => time()))) : array('status' => 0, 'info' => '分类 ' . $data['name'] . ' 更新失败');
            } else if ($act == "del") { //删除分类
                unset($data['pid'], $data['name']);
                return ($M->where($data)->delete()) ? array('status' => 1, 'info' => '分类 ' . $data['name'] . ' 已经成功删除', 'url' => U('Jifen/category', array('time' => time()))) : array('status' => 0, 'info' => '分类 ' . $data['name'] . ' 删除失败');
            }
        } else {
        import('@.ORG.Category');
        $cat = new Category('mall_category', array('cid','pid','name'));
        return $cat->getList('status=1',0,'sort desc');
    }
    }


    public function addNews() {
        $M = M("mall");
        $data = $_POST['info'];
        if($_FILES['img']){
            import('ORG.Net.UploadFile');
            $upload = new UploadFile();// 实例化上传类
            $upload->maxSize  = 3145728 ;// 设置附件上传大小
            $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->savePath =  './Public/uploads/mall/';// 设置附件上传目录
            //设置需要生成缩略图，仅对图像文件有效
            $upload->thumb = true;
            // 设置引用图片类库包路径
            $upload->imageClassPath = 'ORG.Util.Image';
            //设置需要生成缩略图的文件后缀
            $upload->thumbPrefix = 'm_,s_';  //生产2张缩略图
            $upload->thumbMaxWidth = '180,80';
            //设置缩略图最大高度
            $upload->thumbMaxHeight = '180,80';
            //设置上传文件规则
            $upload->saveRule = uniqid;
            //删除原图
            //   $upload->thumbRemoveOrigin = true;
            if(!$upload->upload()) {// 上传错误提示错误信息
                echo $upload->getErrorMsg();
            }else{// 上传成功 获取上传文件信息
                $info =  $upload->getUploadFileInfo();
            }
            $data['img']=$info[0]['savename'];
        }
        $data['create_time'] = time();
        $data['user_id'] = getUid();
        if (empty($data['summary'])) {
            $data['summary'] = cutStr($data['content'], 200);
        }

        if($M->create($data)){
            if ($M->add()) {
                return array('status' => 1, 'info' => "已经发布", 'url' => U('jifen/index'));
            }else{
                return array('status' => 0, 'info' =>$M->getDbError());
            }
        } else {
            return array('status' => 0, 'info' =>$M->getError());
        }
    }

    public function edit(){
        $M = M("mall");
        $data = $_POST['info'];
        $data['update_time'] = time();
        if($_FILES['img']){
            import('ORG.Net.UploadFile');
            $upload = new UploadFile();// 实例化上传类
            $upload->maxSize  = 3145728 ;// 设置附件上传大小
            $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->savePath =  './Public/uploads/mall/';// 设置附件上传目录
            //设置需要生成缩略图，仅对图像文件有效
            $upload->thumb = true;
            // 设置引用图片类库包路径
            $upload->imageClassPath = 'ORG.Util.Image';
            //设置需要生成缩略图的文件后缀
            $upload->thumbPrefix = 'm_,s_';  //生产2张缩略图
            $upload->thumbMaxWidth = '180,80';
            //设置缩略图最大高度
            $upload->thumbMaxHeight = '180,80';
            //设置上传文件规则
            $upload->saveRule = uniqid;
            //删除原图
            //   $upload->thumbRemoveOrigin = true;

            if(!$upload->upload()) {// 上传错误提示错误信息
                echo $upload->getErrorMsg();
            }else{// 上传成功 获取上传文件信息
                $info =  $upload->getUploadFileInfo();
            }
            $data['img']=$info[0]['savename'];
        }
        if ($M->save($data)) {
            return array('status' => 1, 'info' => "已经更新", 'url' => U('jifen/index'));
        } else {
            return array('status' => 0, 'info' => "更新失败，请刷新页面尝试操作");
        }
    }

    //分类信息
    function categoryInfo($cid){
        $cid=$cid?$cid:0;
        import('@.ORG.Category');
        $cat = new Category('mall_category', array('cid','pid','name'));
        return $cat->getPath($cid);
    }
}