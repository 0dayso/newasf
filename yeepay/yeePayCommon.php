<?php

/*
 * @Title 易宝支付EPOS范例
 * @Description 通用文件，包含签名机制，数据发送和日志写入
 * @V3.0
 * @Author  wenhua.cheng
 */
 //---------------------------------------------------------------------------------------------
 error_reporting(0);
require_once 'merchantProperties.php';
require_once 'HttpClient.class.php';


// 支付请求函数
function getReqHmacString(Array $bizArray){
	global $merchantKey;
	$str="";
	foreach($bizArray as $key=>$value){
		 $str=$str.$value;
	}
	return HmacMd5($str,$merchantKey);	
}


function eposSale(Array $bizArray) {

	global $actionURL;
	global $merchantKey;

	// 调用签名函数生成签名串
	$ReqHmacString=getReqHmacString($bizArray);

	// 组成请求串
	$actionHttpString=HttpClient::buildQueryString($bizArray)."&pr_NeedResponse=1"."&hmac=".$ReqHmacString;
	//echo $actionURL."?".$actionHttpString; ------

	// 记录发起支付请求的参数
	logurl("发起请求",$actionURL."?".$actionHttpString);
	// 发起支付请求
	$pageContents = HttpClient::quickPost($actionURL,$actionHttpString);

	//echo $pageContents;

	// 记录收到的提交结果
	logurl("请求回写",$pageContents);
    $result=explode("\n",$pageContents);
	for($index=0;$index<count($result);$index++){		
		$result[$index] = trim($result[$index]);
		if (strlen($result[$index]) == 0) {
			continue;
		}
		$aryReturn= explode("=",$result[$index]);
		$sKey= $aryReturn[0];
		$sValue	= $aryReturn[1];
		if($sKey=="r0_Cmd"){				
			$r0_Cmd	= $sValue;
		}elseif($sKey=="r1_Code"){			        
			$r1_Code= $sValue;
		}elseif($sKey=="r2_TrxId"){			       
			$r2_TrxId=$sValue;
		}elseif($sKey=="r6_Order"){			       
			$r6_Order=$sValue;
		}elseif($sKey =="errorMsg"){				
			$errorMsg=$sValue;
		}elseif($sKey == "hmac"){						
			$hmac = $sValue;	 
			
		} 
	}

		
    // 进行校验码检查 取得加密前的字符串
	$sbOld="";
	// 加入业务类型
	$sbOld = $sbOld.$r0_Cmd;                
	// 加入支付结果
	$sbOld = $sbOld.$r1_Code;
	// 加入易宝支付交易流水号
	$sbOld = $sbOld.$r2_TrxId;                
	// 加入商户订单号
	$sbOld = $sbOld.$r6_Order;                
	    
	$sNewString = HmacMd5($sbOld,$merchantKey);  
	    
    logurl("订单号:".$r6_Order,"本地生成HMAC:".$sNewString."返回HMAC:".$hmac);
	
	// 校验码正确
	if($sNewString==$hmac) {
		if($r1_Code=="1"){
		      logurl("请求成功","本地生成HMAC:".$sNewString."返回HMAC:".$hmac);
			  echo '<div style="border:2px solid green;padding:20px;width:950px;margin:0 auto;background:#DFF0D8">';
			  echo "<br>您已支付成功!";
		      echo "<br>商户订单号:".$r6_Order."<br>";
		      return; 
		} elseif($r1_Code!="1"){
			  echo '<div style="border:2px solid red;padding:20px;width:950px;margin:0 auto;background:#FCE3E3">';
		      echo "抱歉，本次支付失败";
		      echo "<br>错误原因：".$r1_Code.",".$errorMsg;
			  echo '<br><a href="javascript:void(0)" onclick= "javascript:history.go(-1)">返回</a></div>';
		      return; 
			  	  
		}else{
			  echo '<div style="border:2px solid red;padding:20px;width:950px;margin:0 auto;background:#FCE3E3">';
		      echo "提交失败";
		      echo "<br>请检查后重新测试支付";	
			  echo '<br><a href="javascript:void(0)" onclick= "javascript:history.go(-1)">返回</a></div>';
		      return;       
		}
	} else{
		echo '<div style="border:2px solid red;padding:20px;width:950px;margin:0 auto;background:#FCE3E3">';
		echo "<br>localhost:".$sNewString;	
		echo "<br>YeePay:".$hmac;
		echo "<br>交易签名无效!";
		echo '<br><a href="javascript:void(0)" onclick= "javascript:history.go(-1)">返回</a></div>';
		exit; 
	}
}


// 校验支付结果

function verifyCallback(Array $bizArray,$callBackHmac){
    global $merchantKey;
    $callBackString="";
	$callBackStringLog="";
	foreach($bizArray as $key=>$value){
			$callBackString.=$value;
			$callBackStringLog.=$key."=".$value."&";
	
	}
	$newLocalHmac=HmacMd5( $callBackString,$merchantKey);	
	if ($newLocalHmac==$callBackHmac){
		logurl("callBack页面回调成功，交易信息正常!","回调参数串:".$callBackStringLog."LocalHmac(".$$newLocalHmac.") == ResponseHmac(".$callBackHmac.")!");
	 	return true;
	
	}
    else{
		echo "交易信息被篡改！</br>newLocalHmac=".$newLocalHmac."</br>callBackHmac=".$callBackHmac;
		logurl("callBack页面回调成功，但交易信息被篡改!","回调参数串:".$callBackStringLog."LocalHmac(".$newLocalHmac.") != ResponseHmac(".$callBackHmac.")!");
		return false; 
	}

}


// 生成hmac的函数
function HmacMd5($data,$key)
{
	//logurl("iconv Q logdata",$data);
	$logdata=$data;
	$logkey=$key;

	// RFC 2104 HMAC implementation for php.
	// Creates an md5 HMAC.
	// Eliminates the need to install mhash to compute a HMAC
	// Hacked by Lance Rushing(NOTE: Hacked means written)

	// 需要配置环境支持iconv，否则中文参数不能正常处理
	$ke=iconv("GB2312","UTF-8",$key);
	$data=iconv("GB2312","UTF-8",$data);
	$b=64; // byte length for md5
	if (strlen($key) > $b) {
			$key = pack("H*",md5($key));
								}
	$key=str_pad($key, $b, chr(0x00));
	$ipad=str_pad('', $b, chr(0x36));
	$opad=str_pad('', $b, chr(0x5c));
	$k_ipad=$key ^ $ipad ;
	$k_opad=$key ^ $opad;

	$log_hmac=md5($k_opad . pack("H*",md5($k_ipad . $data)));
	loghmac($logdata,$logkey,$log_hmac);
	return md5($k_opad . pack("H*",md5($k_ipad . $data)));
}

// 记录请求URL到日志
function logurl($title,$content)
{
	global $logName;
	$james=fopen($logName,"a+");
	date_default_timezone_set(PRC);
	fwrite($james,"\r\n".date("Y-m-d H:i:s,A")." [".$title."]   ".$content."\n");
	fclose($james);
}

// 记录生成hmac时的日志信息
function loghmac($str,$merchantKey,$hmac)
{
	global $logName;
	global $merchantKey;
	$james=@fopen($logName,"a+");
	date_default_timezone_set("Asia/Shanghai");
	@fwrite($james,"\r\n".date("Y-m-d H:i:s,A")."  [构成签名的参数:]".$str."  [商户密钥:]".$merchantKey."   [本地HMAC:]".$hmac);
	@fclose($james);
}
?>