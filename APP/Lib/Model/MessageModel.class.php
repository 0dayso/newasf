<?php
class MessageModel extends RelationModel{
    public $data=array();
    Public $u_error;

    //发送系统消息
    //@tpl 模板名
    //@data array 模板变量替换
    function message_sys($tpl,$data=array()){
        if(empty($tpl)) return false;

        if(empty($data)){
            $content=$tpl;
        }else if(is_array($data)){
            $this->data=array_merge($this->data,$data);
        }

        if(!$content){
            $this->data['date_time']=date('Y-m-d');
            $content=M('message_sys_tpl')->where("name='$tpl'")->getField('contents');
            foreach($this->data as $k=>$v){
                $content=str_replace('{$'. $k.'}',$v,$content);
            }
        }

        if($this->data['to']){
            $to=$this->data['to'];
        }elseif($this->data['mobile']){
            $to=D('Member')->where("mobile='{$this->data['mobile']}'")->getField('id');
            echo $to;
        }else{
            $to=getUid();
        }

        return $this->message_sys_send('',$to,$this->data['title'],$content,1);
    }

    //发送手机通知
    function message_sms($mobile,$tpl,$data=array()){

        if(!$tpl) return false;
        if(empty($data)){
            $content=$tpl;
        }elseif(is_array($data)){
            $this->data=array_merge($this->data,$data);
        }
        if(!$mobile){
            if($data['mobile']){
                $mobile=$data['mobile'];
            }else{
                $rs=D('Member')->where("id=".getUid())->getField('mobile');
                if($rs){
                    $mobile=$rs;
                }else{
                    $this->u_error='手机号不能为空';
                    return false;
                }
            }
        }
        if(!preg_match('/^([0-9]){11,12}$/i',$mobile)){ //验证手机号码
            return false;
        }

        if(!$content){
            $this->data['date_time']=date('Y-m-d');
            $this->data['tel']=C('TEL');
            $content=M('message_sms_tpl')->where("name='$tpl'")->getField('contents');
            foreach($this->data as $k=>$v){
                $content=str_replace('{$'. $k.'}',$v,$content);
            }
        }
        return sendMobileSms($mobile,$content);
    }

    //发送系统消息
    function message_sys_send($from=0,$to,$title='',$contents='',$sys=0){
        if($to){
            $data['title']=$title;
            $data['contents']=$contents;
            $data['create_time']=time();
            $data['to_id']=$to;
            $data['from_id']=$from;
            $data['is_sys']=$sys;
            $rs=$this->add($data);
            return $rs;
        }
            return false;
    }

    //执行信息发送
    function message_action($act,$data=array()){
        if(!empty($data))
           $this->data=$data;
        switch($act){
            case 'reg_verify_phone': //注册验证手机号
                return $this->message_sms('','reg_verify_phone',$data);
                break;
            case 'reg_success':  //用户注册成功
                 $this->data['jifen']=C('INVITE_POINTS'); //邀请注册送积分
                $this->data['reg_rebate']=C('REG_REBATE');	//注册返利

                $this->message_sms('','reg_success',$data);//发送注册短信
                $this->message_sys('reg_success',$data);//注册成功发送站内信

                if(cookie('referee_id')){ //邀请注册送积分
                    $this->data['to_id']=cookie('referee_id');
                    $where['id']=cookie('referee_id');
                    $member=D('Member')->field('username,mobile')->find(cookie('referee_id'));
                    if($member){
                        $this->data['member_name']=$member['username'];
                        $this->data['mobile']=$member['mobile'];
                        $this->message_sys('invite_reg',array(1));
                        $this->message_sms($member['mobile'],'invite_reg',array(1));
                    }

                }
                break;
            //忘记密码发送验证码
            case 'forgot_pwd_verify_phone':
                return $this->message_sms('','forgot_pwd_verify_phone',$data);
                break;
            case 'forgot_pwd_success': //忘记密码
                $this->message_sys('forgot_pwd_success',$data);//发送站内信
                $this->message_sms('','forgot_pwd_success',$data);
                break;
            case 'booking_pending':
                $this->message_sys('booking_pending',array(1));//发送站内信
                break;
            case 'booking_process':
                $this->message_sys('booking_process',array(1));//发送站内信
                break;
            case 'booking_ticket':
                $this->message_sys('booking_ticket',array(1));//发送站内信
                break;
            case 'booking_cancel':
                $this->message_sys('booking_cancel',array(1));//发送站内信
                break;
            case 'edit_pwd':
                $this->message_sys('edit_pwd',array(1));//发送站内信
                $this->message_sms('','edit_pwd',array(1));
                break;
            default:
                ;
        }

    }

    function sysMessageList($limit=8){
        $where['is_sys']=1;
        $where['to_id']=getUid();
        $rs=$this->where($where)->order('create_time desc')->limit($limit)->select();
        foreach($rs as $k=>$v){
            $rs[$k]['time']=date('Y-m-d H:i:s',$v['create_time']);
            $rs[$k]['is_new']=($v['create_time']+(3600*24*3))>time();
        }
        return $rs;
    }

    function MessageList($limit=10){
        $where['to_id']=getUid();
        $rs=$this->where($where)->order('create_time desc')->limit($limit)->select();
        foreach($rs as $k=>$v){
            $rs[$k]['time']=date('Y-m-d H:i:s',$v['create_time']);
            $rs[$k]['is_new']=($v['create_time']+(3600*24*3))>time();
        }
        return $rs;
    }



		
		
}