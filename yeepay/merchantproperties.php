<?php
/*
 * @Title 易宝支付EPOS范例
 * @Description 属性文件，可配置商户编号，密钥和测试，正式地址
 * @V3.0
 * @Author  wenhua.cheng
 */

// 日志文件路径
$logName='YeePay_EPOS.log';

// 业务类型
$p0_Cmd='EposSale';

// 交易币种
$p4_Cur='CNY';

// 是否需要应答
$pr_NeedResponse=1;	
// 商户编号$p1_MerId='10011843675';

// 商户密钥,用于生成hmac(hmac的说明详见文档)key为测试
$merchantKey='nG2A00ng85Qa376L6xBWi56y8n9S444k09k32c7vq19C9229t970s29Sj6VH';

// 正式地址$actionURL='https://www.yeepay.com/app-merchant-proxy/command.action';

// 测试地址//$actionURL='http://tech.yeepay.com:8080/robot/debug.action';
// 测试商户编号//$p1_MerId='10001126856';
// 测试商户密钥,用于生成hmac(hmac的说明详见文档)key为测试//$merchantKey='69cl522AV6q613Ii4W6u8K6XuW8vM1N6bFgyv769220IuYe9u37N4y7rI4Pl';


?> 