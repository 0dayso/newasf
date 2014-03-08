<?php
//特价机票模型
class CheapModel extends Model{
    function getList($zhou='美洲',$rowNum=10){
        $zhou=I('dq')?I('dq'):$zhou;
        $where['zhou']=$zhou;
        $data['zhou']=$this->cache(true)->field('zhou')->group('zhou')->order('sort')->select();

      //  print_R($data);
        $rs=$this->cache(true)->where($where)->select();
        $arr=array();
        foreach($rs as $k=>$v){
            //解析多种形式的日期格式
            $time=explode('-',$v['time']);
            if(is_array($time)){
                $times=isset($time[1])?$time[1]:$time[0];
                if(stristr($times,'.')){
                    $times=str_replace('.','-',$times);
                }
                $strtotime=strtotime($times);
                $v['time']=date("Y-m-d",$strtotime);
                if($strtotime<time()){
                    continue;
                }
            }

            if($v['from_city']=='广州'){
                if(count($arr['gz'])<$rowNum)
                $arr['gz'][$k]=$v;
            }
            elseif($v['from_city']=='北京'){
                if(count($arr['bj'])<$rowNum)
                $arr['bj'][$k]=$v;
            }
            elseif($v['from_city']=='上海'){
                if(count($arr['sh'])<$rowNum)
                $arr['sh'][$k]=$v;
            }
            elseif($v['from_city']=='深圳'){
                if(count($arr['sz'])<$rowNum)
                $arr['sz'][$k]=$v;
            }
            elseif($v['from_city']=='香港'){
                if(count($arr['xg'])<$rowNum)
                $arr['xg'][$k]=$v;
            }
            else{
                if(count($arr['qt'])<$rowNum)
                $arr['qt'][$k]=$v;
            }
        }
        $data['list']=$arr;
        return $data;

    }

    //后台获取列表
    function getAdminList(){
        $zhou=I('dq')?I('dq'):'美洲';
        if(I('where')){
            foreach(I('where') as $k=>$v){
                if($v!=''){
                    $where[$k]=$v;
                }
            }
        }
        if(I('search')!=''){
            $search=I('search');
            $where['_string'] = "( from_city like '%{$search}%') OR ( to_city like '%{$search}%') OR (zhou like '%{$search}%')";
        }else{
            $where['zhou']=$zhou;
        }

        $data['zhou']=$this->field('zhou')->group('zhou')->select();
        $rs=$this->where($where)->select();

        foreach($rs as $k=>$v){
            $time=explode('-',$v['time']);
            $strtotime=strtotime($time[1]);
            $rs[$k]['time']=$strtotime;
            $rs[$k]['time_name']=date("Y-m-d",$strtotime);;
            if($strtotime<time()){
                $rs[$k]['time_name']='过期';
            }
            $rs[$k]['update_time']=date("Y-m-d H:i:s",$v['update_time']);
        }
        $data['list']=$rs;
        return $data;

    }

    function getinfo(){

    }

    function edit(){
        if(IS_POST){
            $data = $_POST['info'];
            $data['update_time'] = time();
            if($_FILES['img']){
                import('ORG.Net.UploadFile');
                $upload = new UploadFile();// 实例化上传类
                $upload->maxSize  = 3145728 ;// 设置附件上传大小
                $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath =  './Public/uploads/cheap/';// 设置附件上传目录
                //设置需要生成缩略图，仅对图像文件有效
                $upload->thumb = true;
                // 设置引用图片类库包路径
                $upload->imageClassPath = 'ORG.Util.Image';
                //设置上传文件规则
                $upload->saveRule = uniqid;
                //删除原图
               //   $upload->thumbRemoveOrigin = true;

                if(!$upload->upload()){// 上传错误提示错误信息
                    echo $upload->getErrorMsg();
                }else{// 上传成功 获取上传文件信息
                    $info =  $upload->getUploadFileInfo();
                }
                $data['img']=$info[0]['savename'];

            }
            if ($this->save($data)) {
                return array('status' => 1, 'info' => "成功");
            }else{
                return array('status' => 0, 'info' => "失败，请刷新页面尝试操作");
            }
        }
    }

    function delImg(){
        if(!I('id')){
            return false;
        }
        $data['id']=I('id');
        $data['img']='';
        $data['update_time'] = time();
        $rs=$this->find(I('id'));
        if ($this->save($data)) {
            unlink('./Public/uploads/cheap/'.$rs['img']);
            return array('status' => 1, 'info' => "成功");
        }else{
            return array('status' => 0, 'info' => "失败，请刷新页面尝试操作");
        }
    }


		
		
}