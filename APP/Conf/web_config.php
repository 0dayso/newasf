<?php
return array(
    'WEB_NAME'=>'爱尚飞国际机票网',
    'WEB_DOMAIN'=>'www.aishangfei.com',
	'WEB_KEYWORD'=>'爱尚飞',
	'WEB_DESCRIPTION'=>'爱尚飞',

    //SMTP服务器设置
    'SMTP_SERVER'=>'smtp.exmail.qq.com',
    'SMTP_SERVERPORT'=>'25',
    'SMTP_USERMAIL'=>'mailservice@aishangfei.net',
    'SMTP_EMAILTO'=>'178411643@qq.com',
	'SMTP_TEST'=>'yin.pengfei@qq.com',
	'SMTP_FROM_NAME'=>'aishangfei',
    'SMTP_USER'=>'mailservice@aishangfei.net',
    'SMTP_PASS'=>'aishangfei123',
	
	//手机短信
	'SMS_SN'=>'SDK-GKG-010-00236',
	'SMS_PWD'=>'283358',
	'SMS_SIGN'=>'【爱尚飞国际机票网】',
	'SMS_TEST'=>'18673800250',

    //胜意 ASMS5000
    'ASMS_HOST'=>'http://121.8.201.163:8150/',
    'ASMS_ACCOUNT'=>6000,
    'ASMS_PWD'=>'77169',
    'CACHE_TIME'=>50,
    'ASMS_ONLINE'=>0,//asms 联网模式
    'ASMS_WEBSITECODE'=>'1',
    'ASMS_VERSION'=>'1',


    //自由飞越 订单同步
    'SYNORDER_URL'=>'http://interface.trafree.com/portal',
    'SYNORDER_ID'=>'aishangfei',
    'SYNORDER_KEY'=>'B2767789E670B0A770923A01CB42B532',

    //支付
    //财付通
    'TENPAY_SPNAME'=>"广州美乐商务服务有限公司",
    'TENPAY_PARTNER' => '1213106701',
    'TENPAY_KEY' => '85b204736ea51dc83f93729f283c5580',
    'TENPAY_RETURN_URL' => "http://www.aishangfei.com/pay/payReturnUrl.php",
    'TENPAY_NOTIFY_URL' => "http://www.aishangfei.com/pay/payNotifyUrl.php",


    //aliPay 支付宝
    'ALIPAY_SPNAME'=>"广州美乐商务服务有限公司",
    'ALIPAY_PARTNER' => '2088502941273894',
    'ALIPAY_KEY' => 'farwfl31btngq19q5ew1rg4ax0ze6ns2',
	'seller_email'=>'mlcw@aishangfei.net',//这里是卖家的支付宝账号，也就是你申请接口时注册的支付宝账号
	//	'ALIPAY_RETURN_URL' => "http://www.aishangfei.com/Alipay/return_url.php",//这里是异步通知页面url，提交到项目的Pay控制器的notifyurl方法；
	'ALIPAY_RETURN_URL' => "http://www.aishangfei.com/Member/pay/returnurl",

	//	'ALIPAY_NOTIFY_URL' => "http://www.aishangfei.com/Alipay/notify_url.php", //这里是页面跳转通知url，提交到项目的Pay控制器的returnurl方法；
	'ALIPAY_NOTIFY_URL' => "http://www.aishangfei.com/Member/pay/notifyurl.php",
	'successpage'=>'http://www.aishangfei.cn/Member/booking?status=process',     //支付成功跳转到的页面
	'errorpage'=>'http://www.aishangfei.com/Member/booking?status=pending',       //支付失败跳转到的页面	
	
	
        //其它配置项
    'VERIFY_CODE'=>1,       //注册码开关
    'REG_REBATE'=>50,	//注册返利rebate
    'REG_POINTS'=>1000,   //注册送积分

    'INVITE_POINTS'=>500,   //邀请注册送积分

    'TEL'=>'400-608-5188',

    'REG_AUTH'=>'您的手机号验证码为xxxxxx，欢迎注册成为爱尚飞国际机票网会员，我们将竭诚为您提供尊贵的一站式国际商旅服务',//会员注册
    'REG_SMS'=>'恭喜您注册成为爱尚飞国际机票网会员，请立即登录网站体验专职顾问咨询、特惠航班预订、首次预订返利、会员专享活动、积分兑换礼品等多项尊贵国际商旅服务',//会员注册
    'GETPASSWORD'=>'您的用户名和手机号已验证成功，校验码为xxxxxx,请根据提示输入验证码并设置新密码【爱尚飞国际机票网】',//忘记密码

		
	
);
?>