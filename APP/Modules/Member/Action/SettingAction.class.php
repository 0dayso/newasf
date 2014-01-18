<?php
// 个人设置
class SettingAction extends IniAction {
	
    //我的信息 ok
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
				$this->name($newpost['name']);
			}
			if($newpost['email'] = array('in',$data)){//邮箱
				$this->e_mail($newpost['name']);
			}
			if($newpost['nickname'] = array('in',$data)){//昵称
				$this->nname($newpost['nickname']);
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
	
	//安全中心 ok
	function securitycenter(){
		$userInfo=$this->userInfo;
		$this->title="安全中心";
		$this->display();
    }	
	
	//修改密码 ok
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
		//更换手机号1 ok
	function replacephone(){		
		$userInfo=$this->userInfo;		
		$this->title="更换手机号";
		$this->display();
    }
		//更换手机号2 ok
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
		//更换手机号3 ok
	function replacephone3(){
		if(IS_AJAX){
			$phone=I('phone');
			if(!empty($phone)){				
				$rs=preg_match('/^([0-9]){11,12}$/i',$phone);
				if(!$rs){
					$this->error("手机号格式不正确");
				}
				if($this->checkMember(array('sj'=>$phone))!==true){
					$this->error("该手机已注册过");
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
				$this->success('更改成功',U('/Member/Setting/securitycenter'));
			}
		}		
		$this->title="更换手机号";
		$this->display();	
	}
		//手机账号申诉 ok
	function appeal(){
		if(IS_AJAX){
			$phone=I('phone');
			if(!empty($phone)){				
				$rs=preg_match('/^([0-9]){11,12}$/i',$phone);
				if(!$rs){
					$this->error("手机号格式不正确");
				}
				if($this->checkMember(array('sj'=>$phone))!==true){
					$this->error("该手机已注册过");
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
			if(md5($_POST['code']) !== session('verify')){
				$this->error("图像验证码错误 请重新输入");					
			}
			if($_POST['auth_str'] !== session('auth_str')){
				$this->error("短信验证码错误 请重新输入");
			}
			$userInfo=$this->userInfo;
			$member=D('Member');
			$where['member_id']=$userInfo['id'];
			$member->where($where)->setField('mobile',$_POST['phone']);//更新手机号码
			//$this->success('更改成功',U('/Member/Setting/securitycenter'));
			
			//写入数据appeal
			$appeal=D('Appeal');
			$data['name']=$_POST['name'];
			$data['oldphone']=$_POST['oldphone'];
			$data['describe']=$_POST['describe'];
			$data['create_time']=time();
			$rs=$appeal->data($data)->add();
			if($rs){
				$this->success('申诉成功！');
			}else{
				$this->error('申诉失败！');
			}
		}	
		$this->title="手机账号申诉";
		$this->display();
    }
	
	//--------------------------------------------------------
	//邮箱验证
	function mailboxverify(){
		$userInfo=$this->userInfo;
		$this->title="邮箱验证";
		$this->display();
    }
	
	//更换邮箱1 ok
	function replacemailbox(){	
		$userInfo=$this->userInfo;		
		$this->title="更换邮箱";
		$this->display();
    }
	
	//更换邮箱2 ok
	function replacemailbox2(){
		$userInfo=$this->userInfo;
		$member=D('Member');
		if(!empty($_POST['password'])){
			$member= D('Member');       
			$password=I('password');
			$uid=$userInfo['id'];
			$salt=$member->getsalt($uid,1);
			$password=hashPassword($password,$salt);
			$gpassword= $member->where("id=$uid")->getField('password');
			if($password != $gpassword){
				$this->error('输入的当前密码有误,请检查后重新输入');
			}
		}		
		if(!empty($_POST['mail'])){
			$this->e_mail($_POST['mail']);
			$where['id']=$userInfo['id'];
			$rs=$member->where($where)->setField('email',$_POST['mail']);
			if($rs){
				$this->success('邮箱更改成功');
			}else{
				$this->error('邮箱更改失败');
			}
		}		
		$this->title="更换邮箱";
		$this->display();
    }

	//邮寄地址 ok
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
			$this->address_check($_POST['address']);//验证联系地址
			$this->name($_POST['name']);//收货人姓名
			$this->mobi($_POST['mobile']);//手机号码
			if(!empty($_POST['area_code']) || !empty($_POST['telephone'])){//电话号码
				$tel=$_POST['area_code'].$_POST['telephone'];
				$this->telphone($tel);
			}

			$rs=$this->D('DeliverAddress')->addressAdd();
			if($rs){$this->success();}
			

		}
		
		$this->title="邮寄地址";
		$this->display();
    }
	
	
	//邮寄地址列表 ok
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

	//使用新地址 ok
	function addressadd(){
		$userInfo=$this->userInfo;
		$deliverAddress=D('DeliverAddress');
		if($_POST){	
			$this->address_check($_POST['address']);//验证联系地址
			$this->name($_POST['name']);//收货人姓名
			$this->mobi($_POST['mobile']);//手机号码
			if(!empty($_POST['area_code']) || !empty($_POST['telephone'])){//电话号码
				$tel=$_POST['area_code'].$_POST['telephone'];
				$this->telphone($tel);
			}
			if(empty($_POST['is_default'])){
				$_POST['is_default']=0;
			}else{
				$_POST['is_default']=1;
				$wh['member_id']=$userInfo['id'];
				$res=$deliverAddress->where($wh)->select();
				if(!empty($res)){
					$wh['is_default']=1;					
					$rs=$deliverAddress->where($wh)->select();
					if(!empty($re)){
						$field['is_default']=0;
						$deliverAddress->where($wh)->data($field)->save();
					}
				}
			}
			
			$_POST['member_id']=$userInfo['id'];			
			$_POST['sex']=$userInfo['sex'];
			$_POST['create_time']=time();
			$_POST['update_time']=time();
			$data=$_POST;			
			$re=$deliverAddress->add($data);
			if($re){
				$this->success('添加成功');
			}else{
				$this->error('添加失败');
			}		
		}
		$this->title="使用新地址";
		$this->display();
    }
	
	
	//修改邮寄地址 ok
	function addressedit(){
		$userInfo=$this->userInfo;
		$DeliverAddress=D('DeliverAddress');
		$where['id']=I('id');
		//$where['member_id']=$userInfo('id');
		$address=$DeliverAddress->where($where)->find();	
		
		$info= D('DeliverAddress')->xml_address($address['province'],$address['city']);//省市的转换	
		
		$info['id']=$address['id'];
		$info['address']=$address['address'];
		$info['name']=$address['name'];
		$info['mobile']=$address['mobile'];
		$info['area_code']=$address['area_code'];
		$info['telephone']=$address['telephone'];
		$info['is_default']=$address['is_default'];	
		//print_r($info);
		$this->assign('info',$info);
			
		if($_POST){
			//数据验证
			$this->address_check($_POST['address']);//验证联系地址
			$this->name($_POST['name']);//验证收货人姓名
			if(empty($_POST['mobile'])){  //手机号码验证
				$this->error('手机号码不能为空');
			}else{
				if(!preg_match('/^1[3|4|5|8][0-9]\d{4,8}$/',$_POST['mobile'])){ 
					$this->error('手机号码格式不对,请重新输入');         
				}
			}			
			if(!empty($_POST['area_code']) || !empty($_POST['telephone'])){//电话号码验证
				$tel=$_POST['area_code'].$_POST['telephone'];
				$this->telphone($tel);
			}
			
			//找出变动的数据并更新数据表
			function keys($v1,$v2){
				if ($v1===$v2){return 0;}
				return 1;
			}
			function values($v1,$v2){
				if ($v1===$v2){return 0;}
				return 1;
			}
			if(empty($_POST['is_default'])){
				$_POST['is_default']=0;
			}
			$data=array_udiff_uassoc($_POST,$address,'keys','values');//两函数比较取差值，键名也比较,返回的数组中键名保持不变
	
			if(!empty($data)){
				if($data['is_default'] = array('in',$data)){
					$wh['member_id']=$userInfo['id'];
					$wh['is_default']=1;
					$field['is_default']=0;
					$DeliverAddress->where($wh)->save($field);
				}
				$data['update_time']=time();			
				$DeliverAddress->where($where)->save($data);//更新已更改的数据
			}
		}
		$this->title="修改当前地址";
		$this->display();
    }
			
	//常用旅客 ok
	function passengerlist(){
		$userInfo=$this->userInfo;
		$passenger=D('Passenger');
		$idtype=D('IdType');//证件类型
		
		$where['member_id']=$userInfo['id'];
		$passengerInfo=$passenger->where($where)->order('first_choice desc,id desc')->select();		
		foreach($passengerInfo as $k=>$v){
			$wh['id']=$v['id_type'];
			$type=$idtype->where($wh)->find();
			$passengerInfo[$k]['type']=$type['id_type'];
			$passengerInfo[$k]['len']=strlen($v['id_number']);
		}
		$this->assign('Info',$passengerInfo);
		if($this->isAjax()){
			if(I('act') == 'del'){//删除
				$id=I('id');
				if(is_array($id)){
					$condition['id']=array('in',$id);
				}else{
					$condition['id']=$id;
				}
				$res=$passenger->where($condition)->delete();
				if($res){
					$this->success();
				}else{
					$this->error();
				}
			}
			if(I('act') == 'preferred'){//设为首选			
				$condition['member_id']=$userInfo['id'];
				$condition['first_choice']=1;
				$res=$passenger->where($condition)->find();
				if(!empty($res)){
					$res=$passenger->where($condition)->setField('first_choice',0);
				}
				
				$wheres['id']=I('id');
				$rs=$passenger->where($wheres)->setField('first_choice',1);
				if($res && $rs){
					$this->success();
				}else{
					$this->error();
				}
			}
		}		
		$this->title="常用旅客";
		$this->display();
    }
	
	//编辑常用旅客信息
	function passenger(){
		$userInfo=$this->userInfo;
		$passenger=D('Passenger');
		$idtype=D('IdType');//证件类型
		$id=I('id');
		$this->info=$passenger->where("id='$id'")->find();
		$wheres['id']=$this->info['id_type'];
		$this->type=$idtype->where($wheres)->find();//证件类型转换
		$this->birthday=explode('-',$this->info['birthday']);//出生年月日转换成数组
		$this->telphone=explode('-',$this->info['telphone']);//电话号码格式转换
		if($_POST){	
			$data['last_name']=$_POST['last_name'];
			$data['first_name']=$_POST['first_name'];
			$data['sex']=$_POST['sex'];
			$data['birthday']=$_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];
			$data['id_country']=$_POST['id_country'];
			$data['first_choice']=$_POST['first_choice'];
			$data['mobiel']=$_POST['mobiel'];
			$data['email']=$_POST['email'];
			$data['telphone']=$_POST['area_code'].'-'.$_POST['telphone'].'-'.$_POST['extension'];		
			$newdata=array_udiff_uassoc($data,$this->info);//两函数比较取差值，键名也比较,返回的数组中键名不变
			if(!empty($newdata)){
				if(empty($_POST['last_name']) || empty($_POST['first_name'])){
					$this->error('姓名不能为空');
				}
				if(empty($_POST['mobiel'])){
					$this->error('手机号码不能为空');
				}
				if(empty($_POST['email'])){
					$this->error('电子邮箱不能为空');
				}
				if(!preg_match('/^[_\w\d]$/iu',$_POST['last_name']) || !preg_match('/^[_\w\d]$/iu',$_POST['first_name'])){
					$this->error('姓名只能是拼音或英文');
				}
				if(!preg_match('/^1[3|4|5|8][0-9]\d{4,8}$/',$_POST['mobiel'])){ 
					$this->error('手机号码格式不对,请重新输入');         
				}	
				if(!preg_match('/^[\w\d]+[\w\d-.]*@[\w\d-.]+\.[\w\d]{2,10}$/i',$_POST['email'])){
					$this->error('邮箱格式不对,请重新输入');
				}				
				$passenger->where("id='$id'")->setField($newdata);//更新数据				
				if(!empty($_POST['first_choice'])){//如果设置为首选，将原来的首选释放
					$condition['member_id']=$userInfo['id'];
					$condition['first_choice']=1;
					$res=$passenger->where($condition)->find();
					if(!empty($res)){
						$passenger->where($condition)->setField('first_choice',0);
					}
				}
			}
			
		
		
		
		
		}
		$this->title="编辑常用旅客信息";
		$this->display();
    }
	//增加常用旅客
	function passengeradd(){
		$this->title="增加常用旅客";
		$this->display();
    }

	//站内信
	function message(){
		$userInfo=$this->userInfo;	
		$message=D('Message');
		
		//$where['to_id']=$userInfo['id'];
		$where['to_id']=1103;//测试用
		$this->info=$message->where($where)->select();
		
		if($this->isAjax()){
			$id=I('id');
			if(is_array($id)){
				$wh['id']=array('in',$id);
			}else{
				$wh['id']=$id;
			}
			$res=$message->where($wh)->delete();
			if($res){
				$this->success();
			}else{
				$this->error();
			}
		}
		$this->title="站内信";
		$this->display();
    }
	
	//--------------------------------------------------------------
	//数据验证	
	function mobi($data){//手机号码验证		
		if(empty($data)){
			$this->error('手机号码不能为空');
		}
		if(!preg_match('/^1[3|4|5|8][0-9]\d{4,8}$/',$data)){ 
			$this->error('手机号码格式不对,请重新输入');         
		}		
		$member=D('Member');
		$condition['mobile']=$data;		
		$mo=$member->where($condition)->select();		
		if(!empty($mo)){
			$this->error('该手机已注册过'); 				
		}		
	}
	function telphone($data){//电话号码验证
		if(!reg_match('/^\d{3,4}-\d{7,8}(-\d{3,4})?$/',$data)){
			$this->error('电话号码格式错误');
		}	
	}	
	function address_check($data){//地址验证
		if(empty($data)){
			$this->error('联系地址不能为空');
		}
		if(preg_match('/[\'.,:;*?~`!@#$%^&+=)(<{}]|\]|\[|\/|\\\|\"|\|/',$data)){
			$this->error('请勿输入特殊字符');
		}
		if(preg_match('/^\d+$/',$data)){
			$this->error('联系地址不能纯数字');
		}
		if(strlen($data)<=6){
			$this->error('你输入的地址太短，请重新输入');
		}
	}
	
	function name($data){//姓名验证
		if(empty($data)){
			$this->error('姓名不能为空');
		}
		if(preg_match('/[\'.,:;*?~`!@#$%^&+=)(<{}]|\]|\[|\/|\\\|\"|\|/',$data)){
			$this->error('请勿输入特殊字符');
		}
		if(preg_match('/^\d+$/',$data)){
			$this->error('姓名不能纯数字');
		}
		$en = '/^[_\w\d]$/iu';//纯英文验证规则
		$cn = '/^[_\x{4e00}-\x{9fa5}\d]$/iu';//纯中文验证规则
		if(!preg_match($en,$data) || !preg_match($cn,$data) ){					
			$this->error('姓名输入有误，请重新输入');
		}
	}	
	function nname($data){//昵称验证	
		if(preg_match('/[\'.,:;*?~`!@#$%^&+=)(<{}]|\]|\[|\/|\\\|\"|\|/',$data)){
			$this->error('请勿输入特殊字符');
		}
		$member=D('Member');
		$condition['nickname']=$data;		
		$mo=$member->where($condition)->find();		
		if(!empty($mo)){
			$this->error('该手机已注册过'); 				
		}		
	}	
	function e_mail($data){//邮箱验证
		if(empty($data)){
			$this->error('邮箱不能为空');
		}
		if(!preg_match('/^[\w\d]+[\w\d-.]*@[\w\d-.]+\.[\w\d]{2,10}$/i',$data)){
			$this->error('邮箱格式不对,请重新输入');
		}
		$member=D('Member');
		$condition['email']=$data;		
		$mo=$member->where($condition)->find();		
		if(!empty($mo)){
			$this->error('该邮箱已注册过'); 				
		}	
	}
	
	
}