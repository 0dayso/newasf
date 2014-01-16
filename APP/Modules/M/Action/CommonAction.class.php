<?php
class CommonAction extends IniAction{
	function index(){
		
	}
	
	function verify_code($w=52,$h=27,$verify='verify'){ //验证码
		import("ORG.Util.Image");
        $w=I('w')?I('w'):$w;
        $h=I('h')?I('h'):$h;
        $verify=I('verify')?I('verify'):$verify;
		Image::buildImageVerify($length=4, $mode=1, $type='png',$w,$h,$verify);
    }
	
	function CheckName(){
		if(IS_AJAX){
			if(isset($_GET['d'])){
				$username=I('d');
				$member=D('Member');
                echo $member->regCheckName($username);
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

    //检测手机号
	function CheckPhone(){
		if(IS_AJAX){
			if(isset($_GET['d'])){
				$phone=$_GET['d'];
                $rs=preg_match('/^([0-9]){11,12}$/i',$phone);
                if(!$rs){
                    echo "手机号格式不正确";
                    exit;
                }

				$member=D('Member');
				$userinfo=$member->where("mobile='$phone'")->find();
				if(empty($userinfo)){
					echo 1;				
				}else{
					echo "该手机已注册过";
				}
			}elseif(isset($_GET['phone'])){
                $phone=I('phone');
                $member=D('Member');
                $userinfo=$member->where("mobile='$phone'")->find();
                if($userinfo){
                    echo "该手机已注册过";exit;
                }
                import('ORG.Util.String');
                $auth_str=String::randString(6,1);  //生成6位数的认证码
                session('auth_reg_mobile', $phone);
                session('auth_reg_str',$auth_str);
                $data['mobile']=$phone;
                $data['yzm']=$auth_str;
                $rs=D('Message')->message_action('reg_verify_phone',$data);
              //  $rs=sendMobileSms(session('auth_reg_mobile'),str_replace('xxxxxx',$auth_str,C('REG_AUTH')));//
                if($rs==1){
                    echo 1;
                }else{
                    echo 0;
                }
            }elseif(isset($_GET['num'])){
                if(I('num')==session('auth_reg_str')){
                    session('reg_mobile',session('auth_reg_mobile'));
                    echo 1;
                }else{
                    echo 0;
                }
            }
		}
	}
	
	//检测验证码
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

    //发送密码找回验证码
    function forgotPwd(){
        if(!session('auth_num'))
            session('auth_num',1);
        if(session('auth_num')>3) session(null);
        if(session('auth_str')){
            $data['mobile']=session('auth_mobile');
            $data['yzm']=session('auth_str');
            $rs=D('Message')->message_action('forgot_pwd_verify_phone',$data);
            if($rs){
                session('auth_num', session('auth_num')+1);
                echo 1;
            }else{
                echo 0;
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
            $rs=sendMobileSms(session('auth_mobile'), str_replace('xxxxxx',session('auth_str'),C('GETPASSWORD')));;
            if($rs){
                echo 1;
            }else{
                echo 0;
            }
        }
    }

    //特价机票
    function cheap($type='json'){
        $data=D('Cheap')->getList();
        $this->zhou=$data['zhou'];
        $this->list=$data['list'];
        if($type=='json'){
            $cb = $_GET['callback'];
            echo $cb."({code:".json_encode( $this->list)."})";
        // echo json_encode($arr);
         }
    }


    //关闭窗口
    function close(){
        echo "<script type='text/javascript'>window.opener=null; window.open('', '_self', '');window.close(); </script>";
    }


}
?>