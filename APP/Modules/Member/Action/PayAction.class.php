<?php
// 首页控制器
class PayAction extends IniAction {

    /*
     *财付通支付
     */
    function tenPay(){
        if(!empty($_POST)){
            if(!I('order_no')) $this->error('非法操作');
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
            $return_url=U('/Member/Pay/tenReturn','','','',true);;//支付后返回地址
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
            $PayOrder->create($data,1);
            if(!$PayOrder->add()) $this->error('订单写入失败');
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
                    $order_id_arr=explode(',',$rs['order_id_arr']);
                    foreach($order_id_arr as $val){
                        $orderDB = D('AsmsOrder');
                        $ors= $orderDB->field('ysje')->find($val);
                        $rr= $orderDB->orderPay($val,ASMSUID,$ors['ysje'],$out_trade_no,$rs['remark']);
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
                    $order_id_arr=explode(',',$rs['order_id_arr']);
                    foreach($order_id_arr as $val){
                        $orderDB = D('AsmsOrder');
                        $ors= $orderDB->field('ysje')->find($val);
                        $rr= $orderDB->orderPay($val,ASMSUID,$ors['ysje'],$out_trade_no,$rs['remark']);
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
	
}