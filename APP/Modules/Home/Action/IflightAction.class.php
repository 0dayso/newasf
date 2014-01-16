<?php
class IflightAction extends IniAction {
    public function index(){
        $this->title="国际机票";
        R('Common/cheap','arr'); //特价机票数据
        $this->push=D('News')->getList(33,5,1);
		$this->display();
    }

	 public function demand(){
         if(IS_POST){
             $rs=preg_match('/^([0-9]){11,12}$/i',I('phone'));
             if(!$rs){
                 echo "手机号格式不正确";
                 exit;
             }

             $data= I('post.');
             $data['from_city']=implode(',',$data['from_city']);
             $data['to_city']=implode(',',$data['to_city']);
             $data['origin_date']=implode(',',$data['originDate']);
             $data['telephone']=$data['area'].'-'.$data['phone_no'].'-'.$data['ext'];
             $rs=D('RequireOrder')->insert($data);
             if($rs){
                 $this->success('提交成功,稍后我们客服将联系您');
             }else{
                 $this->error('抱歉！ 提交失败,或您已提交过此需求单,请稍后再试, 建议直接给我们来电');
             }
        }else{
            $this->title="国际航班机票需求单";
		    $this->display();
        }
    }

    //接收需求表单提交信息
    function requireReceive(){
        if(IS_POST){
            $referer=isset($_POST['referer'])?$_POST['referer']:"http://".get_http_referer(1);
            $rs=preg_match('/^([0-9]){11,12}$/i',I('phone'));
            if(!$rs){
                if(IS_AJAX){
                    echo "手机号格式不正确"; exit;
                }else{
                    $this->error('手机号格式不正确');
                }
            }

            $_POST['from_city']=get_encoding($_POST['from_city']);
            $_POST['to_city']=get_encoding($_POST['to_city']);
            $_POST['name']=get_encoding($_POST['name']);

            $rs=D('RequireOrder')->insert();
            if($rs){
                $this->success('提交成功,稍后我们客服将联系您',$referer);
            }else{
                $this->error('抱歉！ 提交失败,或您已提交过此需求单, 建议直接给我们来电');
            }
        }else{
            $this->error('参数有误');
        }
    }

    function demandTemplates(){
        $this->display();
    }

    //测边需求提交单
    function sidebar(){
        if(IS_POST){
            $referer=isset($_POST['referer'])?$_POST['referer']:"http://".get_http_referer(1);
            $rs=preg_match('/^([0-9]){11,12}$/i',I('phone'));
            if(!$rs){
                if(IS_AJAX){
                    echo "手机号格式不正确"; exit;
                }else{
                    $this->error('手机号格式不正确');
                }
            }
            $rs=D('RequireOrder')->insert();
            if($rs){
                $this->success('提交成功,稍后我们客服将联系您',$referer);
            }else{
                $this->error('抱歉！ 提交失败,或您已提交过此需求单, 建议直接给我们来电');
            }
         }else{
            $html=$this->fetch('sidebar');
            header("Content-type: text/javascript; charset=utf-8");
            echo htmltojs($html);
        }
    }

    function ajaxSend(){
        $this->send=D('RequireOrder')->ajaxList();
        $this->display('sidebar');
    }

    function ajaxInfo(){
        $require=D('RequireOrder');
        $this->info=$require->ajaxInfo();
        //   $this->display('sidebar');
    }
	
	//国际机票验证
	function verify(){
		$this->title="国际机票验证";
        $this->display();
    }
	
	//航空公司专栏
	function specialColumn(){
		$this->title="航空公司专栏";
        $this->display();
    }
	
	//国际机票查询
	function flightquery(){
		$this->title="国际机票查询";
        $originCode=substr(I("originCode"),0,3);
        $originName=D("City")->getCityName($originCode);
        $desinationCode=substr(I("desinationCode"),0,3);
        $desinationName=D("City")->getCityName($desinationCode);

        $_GET['origin_name']=$originName."($originCode)";
        $_GET['desination_name']=$desinationName."($desinationCode)";
        //检查城市
        if(!$originName || !$desinationName){
            $this->error('抱歉！ 查询失败,您输入城市的有误 请检查！');
        }

        $_GET['origin_name']=urldecode(I("origin_name"));
        $_GET['desination_name']=urldecode(I("desination_name"));
        //修正日期
        $originTime=strtotime(I("originDate"));
        $_GET['originDate']=$originTime<time()?date('Y-m-d',strtotime("+7 day")):date('Y-m-d',$originTime);
        $returnTime=strtotime(I("returnDate"));
        $_GET['returnDate']=$returnTime<time()?date('Y-m-d',strtotime("+14 day")):date('Y-m-d',$returnTime);
        //加入搜索记录
        if(I('originCode')){
            $searchRecord=D('searchRecord');
            $data['type']=I("flightType")?I("flightType"):1;
            $data['from_city']=$originName;
            $data['from_code']=I("originCode");
            $data['from_date']=I("originDate");
            $data['to_city']=$desinationName;
            $data['to_code']=I('desinationCode');
            $data['to_date']=I('returnDate');
            if(!$searchRecord->create($data)){
                $this->error($searchRecord->getError());
            }
            $searchRecord->add();
        }
        $this->display();
    }
	
	//国际机票预约
	function orderflight(){
		$this->title="国际机票预约";
        $this->display();
    }
}