<?php
//会员模型
class MemberModel extends RelationModel{
	private $uid;
	private $username;
	protected $_link = array(
		'user'=> array(  //关联客服表
			'mapping_type'=>BELONGS_TO,
			'class_name'=>'user',
			'user_id'=>'id',
			'mapping_fields'=>'id,username,name,asms_user_id,profile,avatar,signature,telephone,public_mobile,private_mobile,email,qq,good_review,ordinary_review,bad_review,status,asms_user_id',
           // 定义更多的关联属性 relation(true)
		),

        'asms'=> array(  //关联assms
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'asms_member',
            "foreign_key"=>"asms_member_id",//中间表的字段
        ),

		'ranks'=> array( //关联用户组表
			'mapping_type'=>BELONGS_TO,
			'class_name'=>'member_rank',
			'rank_id'=>'id',
			'mapping_fields'=>'id,name',
		),
	);
	
	protected $_validate = array(
        array('member_id', 'checkMemberId', '没有这个用户', 0,'callback',2),//编辑数据时候验证
		array('verify_code','require','验证码必须！'), //默认情况下用正则进行验证
		array('username','','帐号名称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
     // array('username','email','请输入一个有效的 email ！'), // email
	//	array('name','','帐号名称已经存在了！',0,'unique',1), // 在新增的时候验证name字段是否唯一
		array('mobile','','手机号已经存在',0,'unique',1), // 在新增的时候验证name字段是否唯一
	//	array('email','','邮件帐号已经存在',0,'unique'), // 自定义函数验证密码格式
	 );

    protected $_auto = array (
        array('create_time','time',1,'function'),
        array('last_login_ip','get_client_ip',3,'function'),
        array('domain','get_domain',1,'callback'),
    );

    //自动完成  返回提交域名
    function get_domain(){
        $host=get_http_referer(1)?get_http_referer(1):$_SERVER['HTTP_HOST'];
        return $host;
    }

//    protected $updateFields = array('nickname','email'); //更新有字段

    //会员关联详细信息
	function UserInfo($userid){
		$rs= $this->relation(true)->find($userid);
        if($rs['user_id'] && $rs['user']['status']!=1){ //如客服禁用 后新分配
           D("User")->assignUserid();
           $rs= $this->relation(true)->find($userid);
        }
        return $rs;
	}

    //获取用户名
    function getName($where,$type=0){
        if(!$where)
            return false;
        if(!is_array($where))
            $where['id']=$where;
        $rs=$this->field('username,name')->where($where)->find();
        if($rs){
            if($type==1){
                return (isset($rs['name'])?$rs['name']:$rs['username']);
            }else{
                return $rs['username'];
            }
        }
    }

    //检测id 是否存在
	function CheckId($uid){
		$rs=$this->find($uid);
        if($rs)
            return true;
        else
            return false;
	}

    //检测用户名返回ID
    function checkName($username){
        return $this->where("username='$username'  or mobile='$username'")->getField('id');
    }

    //注册检测用户名
    function regCheckName($username){
        $rs=preg_match('/^([a-zA-Z0-9]){6,20}$/i',$username);
        if(!$rs){
            return "格式不正确 ";
        }
        $rs=$this->checkName($username);
        if(!empty($rs)){
            return "用户名已存在 请更换重试";
        }else{
            return true;
        }
    }

    //获得某个用户salt
    //@type
	function getSalt($username,$type=0){
        if($type==1){
            return $this->where("id='$username' or username='$username' or mobile='$username'")->getField('salt');
        }else{
		    return $this->where("username='$username' or mobile='$username'")->getField('salt');
        }
    }

    //会员注册
    function register(){
        if(C('VERIFY_CODE') && I('verify_code','','md5') != session('verify'))
        return('验证码错误！');
        $this->username=strtolower(I('username'));//用户名转小写
        $checkName=$this->regCheckName($this->username);
        if($checkName!==true)
           return($checkName);
        if($this->checkName(I('mobile'))) //注册 用户名不能为已注册用户的手机号
            return('手机号已存在 请更换重试！');
        if(session('reg_mobile')!=I('mobile'))
            return('手机号未通过验证 请重试！');

        $result = $this->create();
        if (!$result)
            return $this->getError();

        $password=I('post.password');
        $salt=generateSalt();
        $this->salt=$salt;  // 设置salt字段值
        $this->password=hashPassword($password,$salt); //# 对密码进行md5 混合加密
        $this->rebate=C('REG_REBATE')?C('REG_REBATE'):0;  //注册返利

        $this->rank_id=1;                  //默认为普通用户组
        $this->login_count=1;
        $this->verify_mobile=I('mobile');
        $this->source=I('source')?I('source'):'pc'; //来路
        $this->invite_id=cookie('invite_id')?cookie('invite_id'):0; //推荐人ID
        $this->user_id=$this->invite_id; //分配客服
        $id=$this->add(); //插入数据库
     //   echo $this->getDBerror();
        if(!empty($id)){  //注册成功
            $this->uid=$id;
            session('uid',$id);
            if(C('REG_POINTS')){
                D('Points')->addPoints($id,C('REG_POINTS'),I('username').'注册 获得'.C('REG_POINTS').'积分'); //添加注册送积分
            }
           if(C('REG_COUPON')){
               D('Points')->addPoints($id,C('REG_COUPON'),I('username').'注册 获得'.C('REG_COUPON').'现金券',2); //添加注册送现金
           }
            if(cookie('invite_id')){ // 邀请注册
                $asms_member_id=$this->field('asms_member_id')->find(cookie('invite_id'));
                if($asms_member_id && D('AsmsOrder')->where("hyid=$asms_member_id and zf_fkf=1")->count()){
                    D('Points')->addPoints(cookie('invite_id'),1,'邀请 '.I('post.username').'注册 获得1 枚爱钻',1); //添加邀请注册积分
                }
                D('Points')->addPoints(cookie('invite_id'),C('INVITE_POINTS'),'邀请 '.I('post.username').'注册 获得'.C('INVITE_POINTS').'积分'); //添加邀请注册积分
            }
            //记录行为
            action_log('member_register', 'member', getUid(), getUid(),$this);
            return true;
        }else{
            return("注册出错！");
       }

    }


    /**
     * 用户登陆
     * return true 登陆成功
     */
    function login(){
        if( C('VERIFY_CODE') && I('verify_code','','md5') != session('verify')){
            return '验证码错误！';
        }
        $username=I('post.name');
        $password=I('post.password');
        if(!$username || !$password){
            return '请填写帐号 密码再提交！';
        }

        $salt=$this->getSalt($username);
        if(empty($salt)){
            return '用户名不正确';
        }
        $password=hashPassword($password,$salt);
        $userinfo=$this->where("(username='$username' or mobile='$username') and password='$password'")->find();
        if(!empty($userinfo)){
            $this->name=$userinfo['name'];
            $this->uid=$userinfo['id'];
            $this->username=$userinfo['username'];
            session('name',$userinfo['name']);
            session('uid',$userinfo['id']);
            session('username',$userinfo['username']);
            $rs=$this->updateLogin();
            if($rs){
                //记录行为
                action_log('member_login', 'member', getUid(), getUid());
                return true;
            }
        }else{
            return '密码不正确';
        }
    }

    //更新登陆
    function updateLogin(){
        $data['last_login_time']=time();
        $data['last_login_ip']=get_client_ip();
        $data['login_count'] = array('exp','login_count+1');// 用户的d登陆次数加1
        $where['id']=getUid();
        return  $this->where($where)->save($data);
    }


    //邀请注册返利
    function referee($referee_id){
        //	CheckId();
    }

    //设置密码
    function editPwd(){
        $member= D('Member');
        $uid=getUid();
        $oldpssword=I('oldpssword');
        if($oldpssword){
            $salt=$member->getsalt($uid,1);
            $oldpssword=hashPassword($oldpssword,$salt);
            $gpassword= $member->where("id=$uid")->getField('password');
            if($oldpssword!=$gpassword){
                return('输入的当前密码有误,请检查后 重新输入');
            }
        }
        if( strlen(I('password')) < 6 || I('password')!=I('re_password') ){
            return('密码长度不能少于6位数 ，或两次密码不一样');
        }
        $password=I('password');
        $salt=generateSalt();
        $data['salt']=$salt;  // 设置salt字段值
        $data['password']=hashPassword($password,$salt); //# 对密码进行md5 混合加密
        $rs=$member->where("id=$uid")->save($data);
        if($rs){
            //记录行为
            action_log('member_edit_pwd', 'member', getUid(), getUid());
            return true;
        }
        return false;
    }


    //服务评价计算
    function pjCount($where){
        if(!$where){
            $where="user_id=".$this->where("id=".getUid())->getField('user_id');
        }
        $evaluat=D('Evaluat');
        $sum= $evaluat->where($where)->sum('total');
        $pjCount['hao']= $evaluat->where($where." and total>=4 ")->count("id");
        $pjCount['zhong']= $evaluat->where($where." and total=3 ")->count("id");
        $pjCount['cha']= $evaluat->where($where." and  total<3 ")->count("id");
        $pjCount['count']  = $evaluat->where($where)->count("id");// 查询满足要求的总记录数
        $pjCount['server']=round(($sum/$pjCount['count']),1);
        return $pjCount;
    }
}

?>