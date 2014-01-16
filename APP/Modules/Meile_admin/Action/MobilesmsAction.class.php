<?php
//
//短信管理控制器

class MobilesmsAction extends CommonAction{
	function _before_index(){//首页	
        if(I('so')){
            $where['mobile'] = array('like',"%".I('so')."%");
            $where['ip']  = array('like',"%".I('so')."%");
            $where['source']  = array('like',"%".I('so')."%");
			$where['return_var']  = array('like',"%".I('so')."%");
            $where['_logic'] = 'or';
            $map['_complex'] = $where;
        }
		 if($_POST['so_date1'] && $_POST['so_date2']){ //日期搜索
            $map['sent_time']=array(array('egt',strtotime(I('so_date1'))),array('elt',strtotime(I('so_date2'))));
        }
		if(!empty($map))
			$this->map = $map;		  
        $this->order='id desc';//默认倒序
	    $this->index(D('Mobilesms'));
	}

	function check(){//查看页
		$sms=D('Mobilesms');		
		$where['id']=I('id');
		$this->vo=$sms->where($where)->find();	
		$this->display();
	}

	function send(){//发送页
		$this->display();		 
	}
	
	function confirmsend(){//短信发送提交处理				  
		$mobile=I('mobile');	   
		$contents=I('content');	
	   if(sendMobileSms($mobile,$contents)){
		   $this->success("发送成功");
	   }else{
		   $this->error("发送失败");
	   }
	
	}

}

