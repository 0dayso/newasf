<?php
class IniAction extends PublicAction{
	private $userinfo; //用户信息
    public function _initialize(){
        if($_SERVER['HTTP_HOST']=='sl.aishangfei.com'){ //为不同部门启用不同域名 不同主题
            $_GET['t']='Sl';         //主题
            $_GET['department']=10; //自动分配客服用
        }else{
            $_GET['department']=9; //自动分配客服用
            if(MODULE_NAME=='Adviser' || $_GET['company']>1){
                unset($_GET['department']);
            }
        }
        $member=D('Member');
		if(!session('uid')){ //cookie 登陆  取得用户数据
			if(cookie('uid') && cookie('salt')){			
				$userinfo=$member->UserInfo(cookie('uid'));
				if($userinfo){
					if(md5($userinfo['salt'])==cookie('salt')){
						session('uid',cookie('uid'));
						$this->userinfo=$userinfo;
                        D('Member')->updateLogin();//更新登陆
					}else{
                        cookie('uid',null);  cookie('salt',null);
                    }
				}
			}
		}elseif(session('uid')){  //取得用户数据
            $this->userinfo=$member->UserInfo(session('uid'));
            cookie('uid',session('uid'));
		}


        if(isset($_GET['mobile'])){
            cookie('ts_refer','mobile');
        }elseif(cookie('ts_refer')=='mobile'){
            cookie('ts_refer','mobile');
        }

       // $this->restrict();//未登录不能访问

        //当没有对应客服,跳到客服选择页
        if($this->userinfo && !$this->userinfo['user_id'] && MODULE_NAME!='Index'){
            if(MODULE_NAME!='Adviser' && ACTION_NAME!='set_kf'){
                redirect(U('/Index'));
            }
        }

		$this->assign('userinfo',$this->userinfo);//用户信息输出到模板

        if(MODULE_NAME == "Jifen"){
            $this->jifen();
        }
	}

    //未登录不能访问
	function restrict(){
		$array=array('register','login','getpassword');//不需要登陆可访问的function
		if(MODULE_NAME == "Member"){
			if(!in_array(ACTION_NAME,$array)){
				if(!session('uid')){
                    $u=isset($_GET["u"])?$_GET["u"]:get_cur_url(1);
				//	$this->redirect('member/login',"u='$cururl'");
                    redirect(U('member/login')."/?u=$u");
					exit;
				}
			}else{
				if(session('uid')){
					$this->success("你已经登陆",U('member/index'));
					exit;
				}
			}
		}
	}

    //积分测边栏数据
    function jifen(){	
		$cid=I('cid');
        $mall=D('Mall');
		import('@.ORG.Category');
        $cat = new Category('mall_category', array('cid','pid','name'));
        $this->path=$cat->getPath($cid);			
        $this->category=M('mall_category')->select();			
        $clist=list_to_tree($this->category); 	
		$this->assign('clist',$clist);
	
		//积分兑换排行榜		
		$where['status']=1;
		$where['type']=0;
		$this->jifen=$mall->field('id,title,img,jifen')->where($where)->order("sales DESC")->limit(5)->select();
		//爱钻兑换排行榜
		$where['status']=1;
		$where['type']=1;
		$this->aizuan=$mall->field('id,title,img,jifen')->where($where)->order("sales DESC")->limit(5)->select();
		
		//用户信息
		$points=D('Points');
		$where['member_id']=$_SESSION['uid'];
		$where['type2']=0;		
		$this->jfpoints=$points->where($where)->sum('points');
		if($this->jfpoints <= 0){
			$this->jfpoints = 0;
		}
		$where['type2']=1;		
		$this->azpoints=$points->where($where)->sum('points');
		if($this->azpoints <= 0){
			$this->azpoints = 0;
		}
		
		//print_r($_SESSION['uid']);
		//print_r($this->jfpoints);
		//print_r($this->azpoints);
    }
}	
?>