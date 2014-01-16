<?php
class CommonAction extends Action{
	function index(){
		
	}

    function verify_code($w=52,$h=27,$verify='verify'){ //验证码
        import("ORG.Util.Image");
        $w=I('w')?I('w'):$w;
        $w=I('h')?I('h'):$h;
        $w=I('verify')?I('verify'):$verify;
        Image::buildImageVerify($length=4, $mode=1, $type='png',$w,$h,$verify);
    }

	function CheckName(){
		if(IS_AJAX){
			if(isset($_GET['d'])){
				$username=I('d');
                 $rs=preg_match('/^([a-zA-Z0-9_@]){5,40}$/i',$username);
                if(!$rs){
                    echo "格式不正确 ";
                }
				$member=D('Member');			
				$rs=$member->checkName($username);
				if(empty($rs)){
					echo 1;
				}else{
					echo "用户名已存在 请更换重试";
				}
			}
		}
	}
	
	function CheckPassword(){
		if(IS_AJAX){
			if(isset($_GET['name']) && isset($_GET['password'])){
				$username=I('post.name');
				$password=I('post.password');
				$member=D('Member');
				$salt=$member->getsalt($username);
				$password=hashPassword($password,$salt);
				$userinfo=$member->where("username='$username' and password='$password'")->find();
				if(!empty($userinfo)){
					echo 1;
				}else{
					echo 0;
				}
			}
		}
	}
	
	function CheckPhone(){
		if(IS_AJAX){
			if(isset($_GET['d'])){
				$phone=$_GET['d'];
				$member=D('Member');
				$userinfo=$member->where("mobile='$phone'")->find();
				if(empty($userinfo)){
					echo 1;				
				}else{
					echo "该手机已注册过";
				}
			}
		}
	}
	
	
	function CheckVerify(){
		if(IS_AJAX){
			if(isset($_GET['d'])){
				if(md5($_GET['d']) == session('verify')){
					echo 1;
				}else{
					echo "验证码错误 请重新输入";
				}
			}
		}
	}

    function sendMobile(){
        if(!session('auth_num'))
            session('auth_num',1);
        else
            session('auth_num', session('auth_num')+1);
        if(session('auth_num')>5) session(null);

        if(session('auth_str')){
           $rs=sendMobileSms(session('auth_mobile'),session('auth_str'));
            if($rs){
                echo 1;
            }else{
                echo 0;
            }
        }
    }





}
?>