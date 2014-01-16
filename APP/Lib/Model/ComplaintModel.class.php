<?php

class ComplaintModel extends RelationModel{
    protected $_validate = array(
        array('title', 'require', '标题不能为空！'),
        array('contents','require','内容不能为空！'), //默认情况下用正则进行验证
        array('verify_code','require','验证码必须！'), //默认情况下用正则进行验证

        array('mobile','/^\d{6,12}$/','电话号码输入有误',2), // 在新增的时候验证name字段是否唯一
        array('email','email','邮件帐号输入有误',1), // 自定义函数验证密码格式
    );

    protected $_link=array(
        'member'=> array(
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'member',
            'member_id'=>'id',
            'mapping_fields'=>'id,username,name',
            // 定义更多的关联属性
        ),
    );


    function getList($where='',$limit=20){
        $list=$this->where($where)->Relation(true)->order("create_time desc")->limit($limit)->select();
        foreach($list as $k=>$v){
            $list[$k]['type_name'] = $v['type']==1?"投诉":"建议";
            $list[$k]['time'] = date("Y-m-d H:i:s",$v['create_time']);
            $list[$k]['contents_sub'] = cutStr($v['contents'],17);
            $list[$k]['member_name']=$v['member']['name']?$v['member']['name']:($v['member']['username']?$v['member']['username']:"匿名");
        }
        return $list;
    }

    function getInfo(){
        $info=$this->Relation(true)->find(I('id'));
        $info['type_name'] = $info['type']==1?"投诉":"建议";
        $info['time'] = date("Y-m-d H:i:s",$info['create_time']);
        $info['member_name']=$info['member']['name']?$info['member']['name']:($info['member']['username']?$info['member']['username']:"匿名");
        return $info;
    }

    //添加
    function addNews(){
        if(C('VERIFY_CODE') && I('verify_code','','md5') != session('verify')){
           return('验证码错误！');
        }
        if($_FILES['jietu']['name']){
            import('ORG.Net.UploadFile');
            $upload = new UploadFile();// 实例化上传类
            $upload->maxSize  = 3145728 ;// 设置附件上传大小
            $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->savePath =  './Public/uploads/complaint/';// 设置附件上传目录
            if(!$upload->upload()) {// 上传错误提示错误信息
                $this->error($upload->getErrorMsg());
            }else{// 上传成功 获取上传文件信息
                $info =  $upload->getUploadFileInfo();
            }
            $this->jietu='/complaint/'.$info[0]['savename'];
        }
        if(!$this->create()){
            return $this->getError();
        }
        $this->title=I('title');
        $this->type=I('type');
        $this->contents=I('contents');
        $this->email=I('email');
        $this->mobile=I('mobile');
        $this->member_id=getUid();
        $this->create_time=time();
        $this->ip = get_client_ip();
        $id=$this->add(); //插入数据库
        if($id){
            return true;
        }
        return false;
    }

}