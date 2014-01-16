<?php 
class Opadmin{
	private $username;//用户名
	private $password;//密码
	private $userid;  //用户id
	private $nickname;//昵称
	private $purviews;//权限字符串
	private $cfg_prefix;//SESSION前缀
	//保存SESSION的成员变量名称
    private $kUserid='userid';
	private $kNickname='nickname';
	private $kUsername='username';
	private $kPurviews='purviews';
	private $childid;//栏目的所有子栏目id,id之间是用逗号隔开
	 /**
     * +----------------------------------------------------------
     * 构造函数，对象初始化
     * +----------------------------------------------------------
     * @param string  $username  用户名
     * @param string  $password  密码
     * +----------------------------------------------------------
     */
	Public function __construct($username='',$password='')
	{
		$this->cfg_prefix=C('cfg_prefix');
		//给保存SESSION的成员变量名称加上前缀
		$this->kUserid=$this->cfg_prefix.$this->kUserid;
		$this->kNickname=$this->cfg_prefix.$this->kNickname;
		$this->kUsername=$this->cfg_prefix.$this->kUsername;
		$this->kPurviews=$this->cfg_prefix.$this->kPurviews;
		//用于登陆的时候初始化变量
		$this->username=$username;
		$this->password=$this->joinmd5($password);
		//判断session是否存在，存在就赋值
		if(isset($_SESSION[$this->kUserid])&&$_SESSION[$this->kUserid]!='')
			$this->userid=$_SESSION[$this->kUserid];
		if(isset($_SESSION[$this->kNickname])&&$_SESSION[$this->kNickname]!='')
			$this->nickname=$_SESSION[$this->kNickname];
	 	if(isset($_SESSION[$this->kUsername])&&$_SESSION[$this->kUsername]!='')
			$this->username=$_SESSION[$this->kUsername];
		if(isset($_SESSION[$this->kPurviews])&&$_SESSION[$this->kPurviews]!='')
			$this->purviews=$_SESSION[$this->kPurviews];
	}
	/**
     * +----------------------------------------------------------
     * 用户登陆
     * @return bool
     * +----------------------------------------------------------      
     * +----------------------------------------------------------
     */
	public function login()
	{
		$Admin=D('Admin');
		$where['username']=$this->username;
		$where['password']=$this->password;
		$data=$Admin->where($where)->find();
		if($data)
		{
			//登陆成功赋值
			$this->userid	=$data['id'];
			$this->nickname	=$data['nickname'];
			$this->purviews	=$data['purviews'];
			$this->SaveSession();
			//更新登陆信息
			$data['loginip']=get_client_ip();
			if($this->updateinfo($data))
				return true;
			else
				return false;
		}
		else
		{
			return false;
		}
	}
	/**
     * +----------------------------------------------------------
     * 保存session
     * +----------------------------------------------------------      
     * +----------------------------------------------------------
     */
	private function SaveSession()
	{
		$_SESSION[$this->kUserid]	=$this->userid;
		$_SESSION[$this->kNickname]	=$this->nickname;
		$_SESSION[$this->kUsername]	=$this->username;
		$_SESSION[$this->kPurviews]	=$this->purviews;
	}
	/**
     * +----------------------------------------------------------
     * 判断用户是否登陆
     * @return bool
     * +----------------------------------------------------------      
     * +----------------------------------------------------------
     */
	 public function islogin()
	 {
	 	if($this->userid!='')
	 		return true;
	 	else
	 		return false;
	 }
	 /**
     * +----------------------------------------------------------
     * 判断密码是否正确
     * @param string  $pwd   要匹配的密码
     * @return int    $count 
     * +----------------------------------------------------------      
     * +----------------------------------------------------------
     */
     public function  issame($pwd)
     {
     	$Admin=D('Admin');
     	$where['id']=$this->userid;
		$where['password']=$this->joinmd5($pwd);
		$count=$Admin->where($where)->count();
		return $count;
     }
	/**
     * +----------------------------------------------------------
     * 更新用户信息
     *@param array  $datainfo  更新数组
     *@return bool
     * +----------------------------------------------------------      
     * +----------------------------------------------------------
     */
	public function updateinfo($datainfo)
	{
		$Admin=D('Admin');
		$datainfo['logintime']=time();
		$where['id']=$this->userid;
		$count=$Admin->where($where)->save($datainfo);
		if($count>0)
			return true;
		else
			return false;
	}
	/**
     * +----------------------------------------------------------
     * 检查权限 管理员id为1直接返回true
     *@param string  $str  权限字符串
     *@return bool
     * +----------------------------------------------------------      
     * +----------------------------------------------------------
     */
	public function checkPurview($str)
	{
		if($this->userid==1)
			return true;

		$purviews=explode(',', $this->purviews);
		if(in_array($str, $purviews))
			return true;
		else
			return false;
	}
	public function checkCategory($str)
	{
		if($this->userid==1)
			return true;
		$parentid=$this->getParentid($str);
		if($this->checkPurview($parentid))
			return true;
		else
			return false;
	}
	/**
     * +----------------------------------------------------------
     * 获取id的顶级的id
     *@return string
     * +----------------------------------------------------------      
     * +----------------------------------------------------------
     */
	public function  getParentid($str)
	{
		$Category =D('Category');
		$where['id']=$str;
		$data=$Category->where($where)->find();
		if($data['reid']==0)
			return $str;
		else
			return $this->getParentid($data['reid']);
	}
	//读取一个栏目的所有的子栏目的id
    public function getchildid($reid)
    {
    	$this->childid=$this->childid.",".$reid;
    	$Category=D('Category');
    	$where['reid']=$reid;
    	$data=$Category->where($where)->select();
    	foreach($data as $item)
    	{
    		
    		$this->getchildid($item['id']);
    	}
    	return $this->childid;
    }
    

	/**
     * +----------------------------------------------------------
     * 用户退出
     *@return bool
     * +----------------------------------------------------------      
     * +----------------------------------------------------------
     */
	public function loginout()
	{
		$this->userid="";
		$this->nickname="";
		$this->username="";
		$this->purviews="";
		unset($_SESSION[$this->kUserid]);
		unset($_SESSION[$this->kNickname]);
		unset($_SESSION[$this->kUsername]);
		unset($_SESSION[$this->kPurviews]);
		return true;
	}
	/**
     * +----------------------------------------------------------
     * md5加密
     *@return md5加密后字符串
     * +----------------------------------------------------------      
     * +----------------------------------------------------------
     */
	public function joinmd5($pwd)
	{
		return md5($pwd);
	}
	/**
     * +----------------------------------------------------------
     *获得username
     *@return 字符串
     * +----------------------------------------------------------      
     * +----------------------------------------------------------
     */
	 public function getUsername()
	 {
	   return $this->username;
	 }
	 /**
     * +----------------------------------------------------------
     *获得userid
     *@return 字符串
     * +----------------------------------------------------------      
     * +----------------------------------------------------------
     */
	 public function getUserid()
	 {
	   return $this->userid;
	 }
	 /**
     * +----------------------------------------------------------
     *获得nickname
     *@return 字符串
     * +----------------------------------------------------------      
     * +----------------------------------------------------------
     */
	 public function getNickname()
	 {
	   return $this->nickname;
	 }
	
}
?>