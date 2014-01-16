<?php
class CheapViewModel extends ViewModel{
    public $viewFields = array(
       'Cheap'=>array('id','from_city','to_city','time','zhou','air','price','img','_type'=>'LEFT'),//travelerinfos,booking_references
    //   'City'=>array('_as'=>'City','iata'=>'from_iata', '_on'=>'Cheap.from_city=City.name','_type'=>'LEFT'),
   //    'City1'=>array('iata'=>'to_iata', '_on'=>'Cheap.to_city=City1.name','_table'=>"asf_city",'_type'=>'LEFT'),
   //    'CheapAd'=>array( 'img','_as'=>'CheapAd','_on'=>'Cheap.id=CheapAd.id','_type'=>'LEFT'),
    );

    function getList(){
        $zhou=I('dq')?I('dq'):'美洲';
        $where['zhou']=$zhou;
        $data['zhou']=$this->field('zhou')->group('zhou')->select();
        $rs=$this->where($where)->select();
        $arr=array();
        foreach($rs as $k=>$v){
            $time=explode('-',$v['time']);
            $v['time']=date("Y-m-d",strtotime($time[1]));
            if($v['from_city']=='广州'){
                $arr['gz'][$k]=$v;
            }
            elseif($v['from_city']=='北京'){
                $arr['bj'][$k]=$v;
            }
            elseif($v['from_city']=='上海'){
                $arr['sh'][$k]=$v;
            }
            elseif($v['from_city']=='深圳'){
                $arr['sz'][$k]=$v;
            }
            elseif($v['from_city']=='香港'){
                $arr['xg'][$k]=$v;
            }
        }
        $data['list']=$arr;
       return $data;

    }

    function getAdminList(){
        $zhou=I('dq')?I('dq'):'美洲';
        $where['zhou']=$zhou;
        $data['zhou']=$this->field('zhou')->group('zhou')->select();
        $rs=$this->where($where)->select();
        $arr=array();
        foreach($rs as $k=>$v){
            $time=explode('-',$v['time']);
            $rs[$k]['time']=date("Y-m-d",strtotime($time[1]));
        }
        $data['list']=$rs;
        return $data;

    }

    function getinfo(){

    }

    function edit(){
        if(IS_POST){
            $data = $_POST['info'];
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
            $data['update_time'] = time();

            if ($this->save($data)) {
                return array('status' => 1, 'info' => "已经发布", 'url' => U('jifen/index'));
            } else {
                return array('status' => 0, 'info' => "发布失败，请刷新页面尝试操作");
            }
        }
    }
    function delete(){

    }
		

		
		
}