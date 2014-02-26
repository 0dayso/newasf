<?php
// 首页控制器
class PayAction extends IniAction {

    function index(){
       /*$json= '{"out_trade_no":"201402251722011468","trade_no":"2014022564883210","total_fee":"0.03","trade_status":"TRADE_SUCCESS","notify_id":"RqPnCoPT3K9%2Fvwbh3I74nZE3ICVzfQQEk8hsEqTU2gVCFmROk%2BHdu0B0RGx8nPi750Ax","notify_time":"2014-02-25 17:23:57","buyer_email":"1000@yin.cc"}';
        $post=(array)json_decode($json);
        $post= http_build_query($post);
      //  dump($post);exit;
        $url="http://localhost/newasf/member/pay/returnurl?$post";
       $rs= curl_post($url,$post,COOKIE_FILE,0);
        print_R($rs);*/
    //    echo ASMSUID; dump(ASMSUID);
        $data['ddbh']='6565656565651402212';
        $data['ddbz']='test';
        $data['cjr_info']=array(1=>array('cjr_xsj'=>'100'));
     //   D("AsmsOrder")->editOrder($data);
        echo "支付";
    }

    /*
     *支付表单认证  防止数据修改
     */
    function pay_auth(){
        $pay_auth=session('pay_auth');
        if(I('order_no')!=$pay_auth['order_pay_id'] || I('order_price')!=$pay_auth['total_price'] || I('order_id_arr')!=$pay_auth['order_id_arr']){
            $this->error('非法操作 请重新刷新支付页面');
        }
        session('pay_auth',null);
    }
    /*
     *财付通支付
     */


    function tenPay(){
        if(!empty($_POST)){
            //支付表单认证
            $this->pay_auth();
            //创建支付单
            $PayOrder=D('PayOrder');

            Vendor('Tenpay.RequestHandler#class');
            $_POST['product_name']=get_encoding($_POST['product_name']);
            /* 创建支付请求对象 */
            $reqHandler = new RequestHandler();
            $reqHandler->init();
            $key= C('TENPAY_KEY');
            $reqHandler->setKey($key);
            $reqHandler->setGateUrl("https://gw.tenpay.com/gateway/pay.htm");
            $partner=C('TENPAY_PARTNER');
            $notify_url=C('TENPAY_NOTIFY_URL');
            $return_url=U('/Member/Pay/tenReturn','','','',true);//支付后返回地址
        //    echo $reqHandler.$key.$partner.$notify_url.$return_url;

            //----------------------------------------
            //设置支付参数
            //----------------------------------------

            $reqHandler->setParameter("partner", $partner);
            $reqHandler->setParameter("out_trade_no", $_POST['order_no']);
            $reqHandler->setParameter("total_fee",$_POST['order_price']*100);  //总金额
            $reqHandler->setParameter("return_url",  $return_url);
            $reqHandler->setParameter("notify_url", $notify_url);
            $reqHandler->setParameter("body", $_POST['product_name']);
            $reqHandler->setParameter("bank_type", "DEFAULT");  	  //银行类型，默认为财付通
            //用户ip
            $reqHandler->setParameter("spbill_create_ip", $_SERVER['REMOTE_ADDR']);//客户端IP
            $reqHandler->setParameter("fee_type", "1");               //币种
            $reqHandler->setParameter("subject",$_POST['product_name']);          //商品名称，（中介交易时必填）

            //系统可选参数
            $reqHandler->setParameter("sign_type", "MD5");  	 	  //签名方式，默认为MD5，可选RSA
            $reqHandler->setParameter("service_version", "1.0"); 	  //接口版本号
            $reqHandler->setParameter("input_charset", "UTF-8");   	  //字符集
            $reqHandler->setParameter("sign_key_index", "1");    	  //密钥序号

            //业务可选参数
            $reqHandler->setParameter("attach", ""); 	  //附件数据，原样返回就可以了
            $reqHandler->setParameter("product_fee", "");        	  //商品费用
            $reqHandler->setParameter("transport_fee", "0");      	  //物流费用
            $reqHandler->setParameter("time_start",date("YmdHis"));  //订单生成时间
            $reqHandler->setParameter("time_expire", "");             //订单失效时间
            $reqHandler->setParameter("buyer_id", "");                //买方财付通帐号
            $reqHandler->setParameter("goods_tag", $_POST['order_id_arr']);               //商品标记
            $reqHandler->setParameter("trade_mode",$_POST['trade_mode']);              //交易模式（1.即时到帐模式，2.中介担保模式，3.后台选择（卖家进入支付中心列表选择））
            $reqHandler->setParameter("transport_desc","");              //物流说明
            $reqHandler->setParameter("trans_type","1");              //交易类型
            $reqHandler->setParameter("agentid","");                  //平台ID
            $reqHandler->setParameter("agent_type","0");               //代理模式（0.无代理，1.表示卡易售模式，2.表示网店模式）
            $reqHandler->setParameter("seller_id",$partner);                //卖家的商户号

            $tenpayUrl=$reqHandler->getRequestURL();
            $debugInfo=$reqHandler->getDebugInfo();

            $data=$_POST;
            $data['id']=I('order_no');//交易号
            $route=I('route');
            if(is_array($route)){
                $data['route']=implode(',',$route);
            }else{
                $data['route']=$route;
            }
            $data['payUrl']=$tenpayUrl;
            $data['remark']=I('remarkexplain');
            $data['order_info']=json_decode(session('order_info'));
            $PayOrder->create($data,1);
            if(!$PayOrder->add()) $this->error('订单创建失败');
            //echo  $tenpayUrl;
            //转向支付页面
            //记录行为
            action_log('pay_tenPay', 'pay', getUid(), getUid(),$this);
            header("Location:$tenpayUrl");
        }
    }
       
    /*
     * 支付返回
     */
    function tenReturn(){
        unset($_GET[_URL_]);
        Vendor('Tenpay.ResponseHandler#class');
        /* 创建支付应答对象 */
        $resHandler = new ResponseHandler();
        $resHandler->setKey(C('TENPAY_KEY'));
        //记录行为
        action_log('pay_tenReturn', 'pay', getUid(), getUid(),$this);
        //判断签名
        if($resHandler->isTenpaySign()) {
            //通知id
            $notify_id = $resHandler->getParameter("notify_id");
            //商户订单号
            $out_trade_no = $resHandler->getParameter("out_trade_no");
            //财付通订单号
            $transaction_id = $resHandler->getParameter("transaction_id");
            //金额,以分为单位
            $total_fee = $resHandler->getParameter("total_fee");
            //如果有使用折扣券，discount有值，total_fee+discount=原请求的total_fee
            $discount = $resHandler->getParameter("discount");
            //支付结果
            $trade_state = $resHandler->getParameter("trade_state");
            //交易模式,1即时到账
            $trade_mode = $resHandler->getParameter("trade_mode");

            if("1" == $trade_mode ) {
                if( "0" == $trade_state){
                    //------------------------------
                    //处理业务开始
                    //------------------------------
                    //注意交易单不要重复处理
                    //注意判断返回金额

                    $PayOrder=D('PayOrder');
                    $rs= $PayOrder->find($out_trade_no);
                    if($rs['order_price']!=$total_fee/100){
                        $this->error('支付失败');
                    }

                    $data['id']=$out_trade_no;
                    $data['trade_mode']=$trade_mode;
                    $data['trade_state']=$trade_state;
                    $data['order_price']=$total_fee/100;
                    $data['status']=1;
                    $data['data_json']=json_encode($_REQUEST);

                    $PayOrder->update($data);
                    $order_info=object_to_array(json_decode($rs['order_info']));
                    $orderDB = D('AsmsOrder');
                    foreach($order_info as $val){
                        $orderDB->setField('zf_fkf','');
                        $orderDB->orderPay($val['ddbh'],ASMSUID,$val['yfje'],$val['xjj'],$out_trade_no,$rs['remark']);
                    }
                    //------------------------------
                    //处理业务完毕
                    //------------------------------
                    $this->success('即时到帐支付成功',U('/Member/booking')."?status=process");
                } else {
                    //当做不成功处理
                    $this->error('即时到帐支付失败');
                }
            }elseif( "2" == $trade_mode  ){
                if( "0" == $trade_state){
                    //------------------------------
                    //处理业务开始
                    //------------------------------
                    //注意交易单不要重复处理
                    //注意判断返回金额
                    $PayOrder=D('PayOrder');

                    $rs= $PayOrder->find($out_trade_no);

                    if($rs['order_price']!=$total_fee/100){
                        $this->error('支付失败');
                    }

                    $data['id']=$out_trade_no;
                    $data['trade_mode']=$trade_mode;
                    $data['trade_state']=$trade_state;
                    $data['order_price']=$total_fee/100;
                    $data['status']=1;
                    $data['data_json']=json_encode($_REQUEST);
                    $PayOrder->update($data);
                    $order_info=object_to_array(json_decode($rs['order_info']));
                    $orderDB = D('AsmsOrder');
                    foreach($order_info as $val){
                        $orderDB->setField('zf_fkf','');
                        $rr= $orderDB->orderPay($val['ddbh'],ASMSUID,$val['yfje'],$val['xjj'],$out_trade_no,$rs['remark']);
                    }
                    //------------------------------
                    //处理业务完毕
                    //------------------------------
                    $this->success('中介担保支付成功',U('/Member/booking')."?status=process");
                } else {
                    //当做不成功处理
                    $this->error('中介担保支付失败');
                }
            }
        } else {
            $this->error('认证签名失败');
        }
    }

    //财富通 异步通知
    function tenNotify(){
        action_log('pay_tenNotify', 'pay', getUid(), getUid(),$this);
        $data=array();
        $data['input']=file_get_contents('php://input');  # 获取trafree推送的信息
        $data['get']=$_GET;
        $data['post']=$_POST;
        $data=json_encode($data);
        $dir=WEB_ROOT.'log/pay';
        if(!is_dir($dir))mkdir($dir,'0777',true);
        file_put_contents($dir.'/'.date("Y-m-d").'.log',date("H:i:s")."\n".$data."\n",FILE_APPEND);

        Vendor('Tenpay.ResponseHandler#class');
        Vendor('Tenpay.RequestHandler#class');
        Vendor('Tenpay.ClientResponseHandler#class');
        Vendor('Tenpay.TenpayHttpClient#class');

        /* 创建支付应答对象 */
        $resHandler = new ResponseHandler();
        $resHandler->setKey(C('TENPAY_KEY'));

        //判断签名
        if($resHandler->isTenpaySign()) {
            //通知id
            $notify_id = $resHandler->getParameter("notify_id");

            //通过通知ID查询，确保通知来至财付通
            //创建查询请求
            $queryReq = new RequestHandler();
            $queryReq->init();
            $queryReq->setKey(C('TENPAY_KEY'));
            $queryReq->setGateUrl("https://gw.tenpay.com/gateway/simpleverifynotifyid.xml");
            $queryReq->setParameter("partner", C('TENPAY_PARTNER'));
            $queryReq->setParameter("notify_id", $notify_id);

            //通信对象
            $httpClient = new TenpayHttpClient();
            $httpClient->setTimeOut(5);
            //设置请求内容
            $httpClient->setReqContent($queryReq->getRequestURL());
            //后台调用
            if($httpClient->call()) {
                //设置结果参数
                $queryRes = new ClientResponseHandler();
                $queryRes->setContent($httpClient->getResContent());
                $queryRes->setKey(C('TENPAY_KEY'));
                if($resHandler->getParameter("trade_mode") == "1"){
                    //判断签名及结果（即时到帐）
                    //只有签名正确,retcode为0，trade_state为0才是支付成功
                    if($queryRes->isTenpaySign() && $queryRes->getParameter("retcode") == "0" && $resHandler->getParameter("trade_state") == "0") {
                        log_result("即时到帐验签ID成功");
                        //取结果参数做业务处理
                        $out_trade_no = $resHandler->getParameter("out_trade_no");
                        //财付通订单号
                        $transaction_id = $resHandler->getParameter("transaction_id");
                        //金额,以分为单位
                        $total_fee = $resHandler->getParameter("total_fee");
                        //如果有使用折扣券，discount有值，total_fee+discount=原请求的total_fee
                        $discount = $resHandler->getParameter("discount");
                        //------------------------------
                        //处理业务开始
                        //------------------------------
                        $PayOrder=D('PayOrder');
                        $rs= $PayOrder->find($out_trade_no);

                        $data['id']=$out_trade_no;
                        $data['trade_mode']=1;
                        $data['trade_state']=0;
                        $data['order_price']=$total_fee/100;
                        $data['status']=1;
                        $data['data_json']=json_encode($_REQUEST);
                        if($rs){
                            if($rs['order_price']!=$total_fee/100){
                                $this->error('支付失败');
                            }
                            $PayOrder->update($data);
                        }else{
                            $PayOrder->add($data);
                        }

                        $order_info=object_to_array(json_decode($rs['order_info']));
                        $orderDB = D('AsmsOrder');
                        foreach($order_info as $val){
                            $orderDB->setField('zf_fkf','');
                            $rr= $orderDB->orderPay($val['ddbh'],ASMSUID,$val['yfje'],$val['xjj'],$out_trade_no,$rs['remark']);
                        }

                        //------------------------------
                        //处理业务完毕
                        //------------------------------
                        echo "success";
                        file_put_contents($dir.'/'.date("Y-m-d").'.log',date("H:i:s")."\n success \n",FILE_APPEND);
                    } else {
                        //错误时，返回结果可能没有签名，写日志trade_state、retcode、retmsg看失败详情。
                        //echo "验证签名失败 或 业务错误信息:trade_state=" . $resHandler->getParameter("trade_state") . ",retcode=" . $queryRes->                         getParameter("retcode"). ",retmsg=" . $queryRes->getParameter("retmsg") . "<br/>" ;
                        file_put_contents($dir.'/'.date("Y-m-d").'.log',date("H:i:s")."\n"." 验证签名失败 或 业务错误信息:trade_state=" . $resHandler->getParameter("trade_state") ."\n",FILE_APPEND);
                        echo "fail";
                    }
                }
                echo "<br>DebugInfo :" . $queryRes->getDebugInfo() . "<br>";
                //获取查询的debug信息,建议把请求、应答内容、debug信息，通信返回码写入日志，方便定位问题
                /*
                    echo "<br>------------------------------------------------------<br>";
                    echo "http res:" . $httpClient->getResponseCode() . "," . $httpClient->getErrInfo() . "<br>";
                    echo "query req:" . htmlentities($queryReq->getRequestURL(), ENT_NOQUOTES, "GB2312") . "<br><br>";
                    echo "query res:" . htmlentities($queryRes->getContent(), ENT_NOQUOTES, "GB2312") . "<br><br>";
                    echo "query reqdebug:" . $queryReq->getDebugInfo() . "<br><br>" ;
                    echo "query resdebug:" . $queryRes->getDebugInfo() . "<br><br>";
                    */
            }else{
                //通信失败
                echo "fail";
                //后台调用通信失败,写日志，方便定位问题
                file_put_contents($dir.'/'.date("Y-m-d").'.log',date("H:i:s")."\n"."<br>call err:" . $httpClient->getResponseCode() ."," . $httpClient->getErrInfo() . "<br>"."\n",FILE_APPEND);
                echo "<br>call err:" . $httpClient->getResponseCode() ."," . $httpClient->getErrInfo() . "<br>";
            }
        }else{
            echo "<br/>" . "认证签名失败" . "<br/>";
            file_put_contents($dir.'/'.date("Y-m-d").'.log',date("H:i:s")."\n 认证签名失败".$resHandler->getDebugInfo() ."\n",FILE_APPEND);
            echo $resHandler->getDebugInfo() . "<br>";
        }
    }

	//支付宝
	 //在类初始化方法中，引入相关类库    
     public function _initialize() {
        vendor('Alipay.Corefunction');
        vendor('Alipay.Md5function');
        vendor('Alipay.Notify');
        vendor('Alipay.Submit');    
    }
		//入口
	 public function alipay(){		
		 if(!empty($_POST)){
             //支付表单认证
            $this->pay_auth();

			$alipay_config['partner']		= C('ALIPAY_PARTNER');
			$alipay_config['key']			= C('ALIPAY_KEY');
			$alipay_config['sign_type']     = strtoupper('MD5');
			$alipay_config['input_charset'] = strtolower('utf-8');
			$alipay_config['cacert']        = getcwd().'\\cacert.pem';
			$alipay_config['transport']     = 'http';
			
			/**************************请求参数**************************/
			$payment_type = "1"; //支付类型 //必填，不能修改
			$notify_url = C('ALIPAY_NOTIFY_URL'); //服务器异步通知页面路径
			$return_url =  $return_url=U('/Member/Pay/returnurl','','','',true); //页面跳转同步通知页面路径
			$seller_email = C('seller_email');//卖家支付宝帐户必填			
			$out_trade_no = $_POST['order_no'];//商户订单号 通过支付页面的表单进行传递，注意要唯一！
			$subject = $_POST['product_name'];  //订单名称 //必填 通过支付页面的表单进行传递
			$total_fee = $_POST['order_price'];   //付款金额  //必填 通过支付页面的表单进行传递
			$body = $_POST['product_name'];  //订单描述 通过支付页面的表单进行传递
			$anti_phishing_key = "";//防钓鱼时间戳 //若要使用请调用类文件submit中的query_timestamp函数
			$exter_invoke_ip = $_SERVER['REMOTE_ADDR']; //客户端的IP地址
			$show_url = "";//商品展示地址
			$goods_tag=$_POST['order_id_arr']; //商品标记
			$trade_mode=$_POST['trade_mode'];//交易模式（1.即时到帐模式，2.中介担保模式，3.后台选择（卖家进入支付中心列表选择））
			$trans_type=1; //交易类型
			$time_start=date("YmdHis");  //订单生成时间			
			/**********************************************************/
		
			//构造要请求的参数数组，无需改动
			$parameter = array(
				"service" => "create_direct_pay_by_user",
				"partner" => trim($alipay_config['partner']),
				"payment_type"    => $payment_type,
				"notify_url"    => $notify_url,
				"return_url"    => $return_url,
				"seller_email"    => $seller_email,
				"out_trade_no"    => $out_trade_no,
				"subject"    => $subject,
				"total_fee"    => $total_fee,
				"body"            => $body,
				"show_url"	=> $show_url,
				"anti_phishing_key"	=> $anti_phishing_key,
				"exter_invoke_ip"	=> $exter_invoke_ip,
				"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
			);
			//建立请求
			$alipaySubmit = new AlipaySubmit($alipay_config);
			$html_text = $alipaySubmit->buildRequestForm($parameter,"post", "确认");

			
			//写入数据
			$PayOrder=D('PayOrder');
			$_POST['product_name']=get_encoding($_POST['product_name']);
			
            $data['id']=$_POST['order_no'];//交易号
			$data['order_id_arr']=$_POST['order_id_arr'];
			$data['type']=1;
			$data['member_id']=getUid();
			$data['product_name']=$_POST['product_name'];			
            $route=I('route');
            if(is_array($route)){
                $data['route']=implode(',',$route);
            }else{
                $data['route']=$route;
            } 
			$data['coupon']=0;
			$data['order_price']=$_POST['order_price'];		
			$data['trade_mode']=$_POST['trade_mode'];
			$data['trade_state']=$payment_type;	
		   	$data['status']=0;
			$data['data_json']=0;
            $data['remark']=$_POST['remarkexplain'];//简要说明			
			$data['create_time']=time();
			$data['update_time']=time();
            $data['order_info']=json_encode(session('order_info'));
            if($PayOrder->create($data)){
                if(!$PayOrder->add()){
                    $this->error('订单写入失败');
                }else{
                   echo  $PayOrder->getDbError();
                }
            }else{
                $this->error('订单创建失败');
            }

            //转向支付页面
            //记录行为
            action_log('pay_aliPay', 'alipay', getUid(), getUid(),$this);
            echo $html_text;
            //header("Location:$alipayUrl");
		}
	 }

		/******************************
        服务器异步通知页面方法
        其实这里就是将notify_url.php文件中的代码复制过来进行处理        
        *******************************/
		public function notifyurl(){
            action_log('pay_notifyurl', 'pay', getUid(), getUid(),$this);
			$alipay_config['partner']		= C('ALIPAY_PARTNER');
			$alipay_config['key']			= C('ALIPAY_KEY');
			$alipay_config['sign_type']     = strtoupper('MD5');
			$alipay_config['input_charset'] = strtolower('utf-8');
			$alipay_config['cacert']        = getcwd().'\\cacert.pem';
			$alipay_config['transport']     = 'http';
			
			//计算得出通知验证结果
			$alipayNotify = new AlipayNotify($alipay_config);
			$verify_result = $alipayNotify->verifyNotify();
			
			if($verify_result) {
				   //验证成功
					   //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
				   $out_trade_no   = $_POST['out_trade_no'];      //商户订单号
				   $trade_no       = $_POST['trade_no'];          //支付宝交易号
				   $trade_status   = $_POST['trade_status'];      //交易状态
				   $total_fee      = $_POST['total_fee'];         //交易金额
				   $notify_id      = $_POST['notify_id'];         //通知校验ID。
				   $notify_time    = $_POST['notify_time'];       //通知的发送时间。格式为yyyy-MM-dd HH:mm:ss。
				   $buyer_email    = $_POST['buyer_email'];       //买家支付宝帐号；
				
				$parameter = array(
					"out_trade_no"     => $out_trade_no,      //商户订单编号；
					"trade_no"     => $trade_no,          //支付宝交易号；
					"total_fee"      => $total_fee,         //交易金额；
					"trade_status"     => $trade_status,      //交易状态
					"notify_id"      => $notify_id,         //通知校验ID。
					"notify_time"    => $notify_time,       //通知的发送时间。
					"buyer_email"    => $buyer_email,       //买家支付宝帐号
				);
				
			   if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
			   	   if(!checkorderstatus($out_trade_no)){//commom/commom.php
					    orderhandle($parameter);  //进行订单处理，并传送从支付宝返回的参数；		   
				   }				   
				   echo "success";        //请不要修改或删除
				}else {
						//验证失败
						echo "fail";
			  }    
		  }
		}
			
		
		/****************************
		支付返回
		****************************/		
		public function returnurl(){
            action_log('pay_returnurl', 'pay', getUid(), getUid(),$this);
			$alipay_config['partner']		= C('ALIPAY_PARTNER');
			$alipay_config['key']			= C('ALIPAY_KEY');
			$alipay_config['sign_type']     = strtoupper('MD5');
			$alipay_config['input_charset'] = strtolower('utf-8');
			$alipay_config['cacert']        = getcwd().'\\cacert.pem';
			$alipay_config['transport']     = 'http';
			
			$alipayNotify = new AlipayNotify($alipay_config);//计算得出通知验证结果
			$verify_result = $alipayNotify->verifyReturn();
			
			if($verify_result) {
        //    if(1) {
				//验证成功
				//获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
				$out_trade_no   = $_GET['out_trade_no'];      //商户订单号
				$trade_no       = $_GET['trade_no'];          //支付宝交易号
				$trade_status   = $_GET['trade_status'];      //交易状态
				$total_fee      = $_GET['total_fee'];         //交易金额
				$notify_id      = $_GET['notify_id'];         //通知校验ID。
				$notify_time    = $_GET['notify_time'];       //通知的发送时间。
				$buyer_email    = $_GET['buyer_email'];       //买家支付宝帐号；				
		
				$parameter = array(
					"out_trade_no"     => $out_trade_no,      //商户订单编号；
					"trade_no"     => $trade_no,          //支付宝交易号；
					"total_fee"      => $total_fee,         //交易金额；
					"trade_status"     => $trade_status,      //交易状态
					"notify_id"      => $notify_id,         //通知校验ID。
					"notify_time"    => $notify_time,       //通知的发送时间。
					"buyer_email"    => $buyer_email,       //买家支付宝帐号
				);
				
				if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
			   	   if(!checkorderstatus($out_trade_no)){    //commom/commom.php
					    orderhandle($parameter);  //进行订单处理，并传送从支付宝返回的参数；		   
				   }
				$this->success('即时到帐支付成功',U('/Member/booking')."?status=process");
				}else {					
					$this->error('支付失败',U('/Member/booking'));
				}
		 }else {
			//验证失败
			//如要调试，请看alipay_notify.php页面的verifyReturn函数			
			$this->error('验证失败',U('/Member/booking'));
		}
	}
}