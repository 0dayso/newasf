<?php
// 个人设置
class SettingAction extends IniAction {
	
    //我的信息
	function myinfo(){
		$member=D('Member');
		$userInfo=$this->userInfo;
		
		//构造新数组		
		$newarr['name']=$userInfo['name'];
		$newarr['nickname']=$userInfo['nickname'];
		$newarr['sex']=$userInfo['sex'];
		$newarr['birthday']=$userInfo['birthday'];
		$newarr['province']=$userInfo['province'];
		$newarr['city']=$userInfo['city'];
		if(empty($userInfo['email'])){$newarr['email']=$userInfo['email'];}
		
		//重新构造post
		$newpost['name']=$_POST['name'];
		$newpost['nickname']=$_POST['nickname'];
		$newpost['sex']=$_POST['sex'];
		$newpost['birthday']=$_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];
		$newpost['province']=$_POST['province'];
		$newpost['city']=$_POST['city'];
		if(!empty($_POST['email'])){$newpost['email']=$_POST['email'];}
		
		$data=array_udiff_uassoc($newpost,$newarr);//两函数比较取差值，键名也比较,返回的数组中键名保持不变
		
		if(!empty($data)){
			if($newpost['name'] = array('in',$data)){//姓名
				$en = '/^[_\w\d]{'.$minLen.','.$maxLen.'}$/iu';//纯英文验证规则
				$cn = '/^[_\x{4e00}-\x{9fa5}\d]{'.$minLen.','.$maxLen.'}$/iu';//纯中文验证规则
				//$en_cn = '/^[_\w\d\x{4e00}-\x{9fa5}]{'.$minLen.','.$maxLen.'}$/iu';//中英文结合验证规则
				if(!preg_match($en,$newpost('name')) || !preg_match($cn,$newpost('name')) ){					
					$this->error('姓名输入有误，请重新输入');
				}
			}
			if($newpost['email'] = array('in',$data)){//邮箱
				if(!preg_match('/^[\w\d]+[\w\d-.]*@[\w\d-.]+\.[\w\d]{2,10}$/i',$newpost['email'])){
					$this->error('邮箱格式不对,请重新输入');
				}
			}
			if($newpost['nickname'] = array('in',$data)){//昵称
				if(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<{}]|\]|\[|\/|\\\|\"|\|/",$newpost['nickname'])){
					$this->error('昵称含有特殊字符，请重新输入');
				}
			}
		    $where['id']=$userInfo['id'];
			$member->where($where)->setField($data);//更新数据		
		}
		
		$this->birthday=explode('-',$userInfo['birthday']);//生日处理
		$this->title="我的信息";
		$this->display();
    }
	
	//设置头像
	function seticon(){
		$userInfo=$this->userInfo;
		$this->title="设置头像";
		$this->display();
    }	
	
	public function uploadImg(){				
			import('ORG.UploadFile');
			$upload = new UploadFile();						// 实例化上传类
			$upload->maxSize = 2*1024*1024;					//设置上传图片的大小
			$upload->allowExts = array('jpg','png','gif');	//设置上传图片的后缀
			$upload->uploadReplace = true;					//同名则替换
			$upload->saveRule = 'uniqid';					//设置上传头像命名规则(临时图片),修改了UploadFile上传类				
			$path = './Public/member/images/';//完整的头像路径
			$upload->savePath = $path;				
			if(!$upload->upload()) {						// 上传错误提示错误信息
				$this->ajaxReturn('',$upload->getErrorMsg(),0,'json');
			}else{											// 上传成功 获取上传文件信息
				$info =  $upload->getUploadFileInfo();
				$temp_size = getimagesize($path.$info['0']['savename']);
				
				if($temp_size[0] < 100 || $temp_size[1] < 100){//判断宽和高是否符合头像要求
					$this->ajaxReturn(0,'图片宽或高不得小于100px！',0,'json');
				}
				$data['picname'] = $info['0']['savename'];
				$data['status'] = 1;
				$data['url'] = __ROOT__.'/Public/member/images/'.$data['picname'];
				$data['info'] = $info;
				$this->ajaxReturn($data,'json');
			}
		}
		
	//裁剪并保存用户头像
	public function cropImg(){				
		//图片裁剪数据
		$params = $this->_post();						//裁剪参数
		if(!isset($params) && empty($params)){
			return;
		}				
		//头像目录地址
		$path = './public/member/images/';				
		//要保存的图片
		$real_path = $path.$params['picname'];					
		//临时图片地址
		$pic_path = $path.$params['picname'];				
		$thumb=explode('.',$params['picname']);				
		import('ORG.ThinkImage.ThinkImage');
		$Think_img = new ThinkImage(THINKIMAGE_GD); 
		//裁剪原图
		$Think_img->open($pic_path)->crop($params['w'],$params['h'],$params['x'],$params['y'])->save($real_path);
		//生成缩略图
		$Think_img->open($real_path)->thumb(100,100, 1)->save($path.$thumb[0].'_100.jpg');
		$Think_img->open($real_path)->thumb(60,60, 1)->save($path.$thumb[0].'_60.jpg');	
		
		$this->success('上传头像成功');
	}
	
	//安全中心
	function securitycenter(){
		$userInfo=$this->userInfo;
		$this->title="安全中心";
		$this->display();
    }
	
	//常用旅客
	function passengerlist(){
		$this->title="常用旅客";
		$this->display();
    }
	//编辑常用旅客信息
	function passenger(){
		$this->title="编辑常用旅客信息";
		$this->display();
    }
	//增加常用旅客
	function passengeradd(){
		$this->title="增加常用旅客";
		$this->display();
    }
	
	//修改密码
	function password(){
		if($_POST){
            $rs=D('Member')->editPwd();
            if($rs===true){
                D('Message')->message_action('edit_pwd');//发送信息
                $this->success("修改成功");
            }else{
                $this->error($rs);
            }
        }else{
             $this->title="修改密码";
             $this->display();
        }
    }	
	
	//--------------------------------------------------------------
		//更换手机号1
	function replacephone(){		
		$userInfo=$this->userInfo;		
		$this->title="更换手机号";
		$this->display();
    }
		//更换手机号2
	function replacephone2(){
		$userInfo=$this->userInfo;
		session('auth_mobile',$userInfo['mobile']);		
		if($_POST){
			if(md5($_POST['code']) !== session('verify')){
				$this->error("验证码错误 请重新输入");					
			}elseif($_POST['auth_str'] !== session('auth_str')){
				$this->error("短信验证码错误 请重新输入");
			}
		}		
		$this->title="更换手机号";
		$this->display();
    }
		//更换手机号3
	function replacephone3(){
		if(IS_AJAX){
			$phone=I('phone');
			if(!empty($phone)){				
				$rs=preg_match('/^([0-9]){11,12}$/i',$phone);
				if(!$rs){
					echo "手机号格式不正确";
					exit;
				}
				if($this->checkMember(array('sj'=>$phone))!==true){
					echo "该手机已注册过";
					 exit;
				}else{
					echo 1;
				}
			}else{
				$this->error("请输入手机号码");
			}
			import('ORG.Util.String');
			$auth_str=String::randString(6,1);  //生成6位数的认证码			
			session('auth_str',$auth_str);
			$data['mobile']=$phone;
			$data['yzm']=$auth_str;
			$rs=D('Message')->message_action('replace_phone',$data);
		}			
		if($_POST){
			if($_POST['auth_str'] !== session('auth_str')){
				$this->error("短信验证码错误 请重新输入");
			}else{
				$userInfo=$this->userInfo;
				$member=D('Member');
				$where['member_id']=$userInfo['id'];
				$member->where($where)->setField('mobile',$_POST['phone']);
			}
		}
		
		$this->title="更换手机号";
		$this->display();
	
	}
	
	
	
	
	//手机账号申诉
	function appeal(){
		$this->title="手机账号申诉";
		$this->display();
    }
	
	//邮箱验证
	function mailboxverify(){
		$this->title="邮箱验证";
		$this->display();
    }
	
	//更换邮箱
	function replacemailbox(){	
		$userInfo=$this->userInfo;
		$member=D('Member');
		if(!empty($_POST)){
			//邮箱验证
			if(preg_match('/^[\w\d]+[\w\d-.]*@[\w\d-.]+\.[\w\d]{2,10}$/i',$_POST['email'])){
				$where['id']=$userInfo['id'];
				$res=$member->where($where)->setField('email',$_POST['email']);
				if($res){
					$this->success();
				}
			}else{
				$this->error('邮箱格式不对,请重新输入');
			}
		}
		$this->title="更换邮箱";
		$this->display();
    }
	//更换邮箱
	function replacemailbox2(){
		$this->title="更换邮箱";
		$this->display();
    }

	//邮寄地址
	function address(){
		$userInfo=$this->userInfo;
		$address=D('DeliverAddress');
		//$where['member_id']=$userInfo['id'];
		$where['member_id']=1103;//测试用
		$where['is_default']=1;		
		$addressInfo=$address->where($where)->find();
		$province_city = D('DeliverAddress')->xml_address($addressInfo['province'],$addressInfo['city']);		
		$this->assign('province_city',$province_city);	
		$this->assign('addressInfo',$addressInfo);		
		
		if(!empty($_POST)){
			if(I('act') == 'add'){//增加新地址
				if(empty($_POST['address'])){$this->error('请输入联系地址');}
				if(empty($_POST['name'])){$this->error('请输入收货人姓名');}
				if(empty($_POST['mobile'])){$this->error('请输入手机号');}			
				if(preg_match('/^[(86)|0]?(13\d{9})|(15\d{9})|(18\d{9})$/',$_POST['mobile'])){ 
					$where['mobile']= $_POST['mobile'];	         
				}else{ 
					$this->error('手机号码格式不对,请重新输入');
				}
				$rs=$this->D('DeliverAddress')->addressAdd();
				if($rs){$this->success();}
			}
			if(I('act') == 'edit'){//编辑地址
				$rs=$this->D('DeliverAddress')->addressEdit();
				if($rs){$this->success();}
			}
		}
		
		$this->title="邮寄地址";
		$this->display();
    }

	
	//邮寄地址列表
	function addresslist(){
		$userInfo=$this->userInfo;
		$address=D('DeliverAddress');
		//$where['member_id']=$userInfo['id'];
		$where['member_id']=1103;//测试用
		$addressInfo=$address->where($where)->select();
		foreach($addressInfo as $v){
			$data[] = D('DeliverAddress')->xml_address($v['province'],$v['city']);	
		}
		foreach($addressInfo as $k=>$v){
			$data[$k]['id']=$v['id'];
			$data[$k]['name']=$v['name'];
			$data[$k]['mobile']=$v['mobile'];
			$data[$k]['telephone']=$v['telephone'];
			$data[$k]['address']=$v['address'];	
			$data[$k]['zip_code']=$v['zip_code'];	
		}		
		$this->assign('addressInfo',$data);	
		
		$act=I('act');
		if(!empty($act)){
			$id=I('id');
			if(is_array($id)){
				$wh['id']=array('in',$id);
			}else{
				$wh['id']=$id;
			}
			if(I('act') == 'del'){
				$address->where($wh)->delete();
				$this->success();
			}
		}
		$this->title="邮寄地址列表";
		$this->display();
    }


	//使用新地址
	function addressadd(){
		$this->title="使用新地址";
		$this->display();
    }
	//修改邮寄地址
	function addressedit(){
		$userInfo=$this->userInfo;
		$address=D('DeliverAddress');
		$where['id']=I('id');
		//$where['member_id']=$userInfo('id');
		$address=$address->where($where)->find();	
		
		$info= D('DeliverAddress')->xml_address($address['province'],$address['city']);//省市的转换	
		
		$info['address']=$address['address'];
		$info['name']=$address['name'];
		$info['mobile']=$address['mobile'];
		$info['area_code']=$address['area_code'];
		$info['telephone']=$address['telephone'];
		$info['is_default']=$address['is_default'];	
		//print_r($info);
		$this->assign('info',$info);
		
		//如何设置默认，将原来的默认释放
		$data=array_udiff_uassoc($_POST,$address);//两函数比较取差值，键名也比较,返回的数组中键名保持不变
		if(!empty($data)){
			$address->where($where)->setField($data);
			if($_POST['is_default'] = array('in',$data)){
				$wh['member_id']=$userInfo('id');
				$wh['is_default']=1;
				$address->where($wh)->setField('is_default',0);
			}
		}
		
		$this->title="修改当前地址";
		$this->display();
    }

	//站内信
	function message(){
		$this->title="站内信";
		$this->display();
    }
	
	
	
}