<?php

/*
 * @Title �ױ�֧��EPOS����
 * @Description ͨ���ļ�������ǩ�����ƣ����ݷ��ͺ���־д��
 * @V3.0
 * @Author  wenhua.cheng
 */
 //---------------------------------------------------------------------------------------------
 error_reporting(0);
require_once 'merchantProperties.php';
require_once 'HttpClient.class.php';


// ֧��������
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

	// ����ǩ����������ǩ����
	$ReqHmacString=getReqHmacString($bizArray);

	// �������
	$actionHttpString=HttpClient::buildQueryString($bizArray)."&pr_NeedResponse=1"."&hmac=".$ReqHmacString;
	//echo $actionURL."?".$actionHttpString; ------

	// ��¼����֧������Ĳ���
	logurl("��������",$actionURL."?".$actionHttpString);
	// ����֧������
	$pageContents = HttpClient::quickPost($actionURL,$actionHttpString);

	//echo $pageContents;

	// ��¼�յ����ύ���
	logurl("�����д",$pageContents);
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

		
    // ����У������ ȡ�ü���ǰ���ַ���
	$sbOld="";
	// ����ҵ������
	$sbOld = $sbOld.$r0_Cmd;                
	// ����֧�����
	$sbOld = $sbOld.$r1_Code;
	// �����ױ�֧��������ˮ��
	$sbOld = $sbOld.$r2_TrxId;                
	// �����̻�������
	$sbOld = $sbOld.$r6_Order;                
	    
	$sNewString = HmacMd5($sbOld,$merchantKey);  
	    
    logurl("������:".$r6_Order,"��������HMAC:".$sNewString."����HMAC:".$hmac);
	
	// У������ȷ
	if($sNewString==$hmac) {
		if($r1_Code=="1"){
		      logurl("����ɹ�","��������HMAC:".$sNewString."����HMAC:".$hmac);
			  echo '<div style="border:2px solid green;padding:20px;width:950px;margin:0 auto;background:#DFF0D8">';
			  echo "<br>����֧���ɹ�!";
		      echo "<br>�̻�������:".$r6_Order."<br>";
		      return; 
		} elseif($r1_Code!="1"){
			  echo '<div style="border:2px solid red;padding:20px;width:950px;margin:0 auto;background:#FCE3E3">';
		      echo "��Ǹ������֧��ʧ��";
		      echo "<br>����ԭ��".$r1_Code.",".$errorMsg;
			  echo '<br><a href="javascript:void(0)" onclick= "javascript:history.go(-1)">����</a></div>';
		      return; 
			  	  
		}else{
			  echo '<div style="border:2px solid red;padding:20px;width:950px;margin:0 auto;background:#FCE3E3">';
		      echo "�ύʧ��";
		      echo "<br>��������²���֧��";	
			  echo '<br><a href="javascript:void(0)" onclick= "javascript:history.go(-1)">����</a></div>';
		      return;       
		}
	} else{
		echo '<div style="border:2px solid red;padding:20px;width:950px;margin:0 auto;background:#FCE3E3">';
		echo "<br>localhost:".$sNewString;	
		echo "<br>YeePay:".$hmac;
		echo "<br>����ǩ����Ч!";
		echo '<br><a href="javascript:void(0)" onclick= "javascript:history.go(-1)">����</a></div>';
		exit; 
	}
}


// У��֧�����

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
		logurl("callBackҳ��ص��ɹ���������Ϣ����!","�ص�������:".$callBackStringLog."LocalHmac(".$$newLocalHmac.") == ResponseHmac(".$callBackHmac.")!");
	 	return true;
	
	}
    else{
		echo "������Ϣ���۸ģ�</br>newLocalHmac=".$newLocalHmac."</br>callBackHmac=".$callBackHmac;
		logurl("callBackҳ��ص��ɹ�����������Ϣ���۸�!","�ص�������:".$callBackStringLog."LocalHmac(".$newLocalHmac.") != ResponseHmac(".$callBackHmac.")!");
		return false; 
	}

}


// ����hmac�ĺ���
function HmacMd5($data,$key)
{
	//logurl("iconv Q logdata",$data);
	$logdata=$data;
	$logkey=$key;

	// RFC 2104 HMAC implementation for php.
	// Creates an md5 HMAC.
	// Eliminates the need to install mhash to compute a HMAC
	// Hacked by Lance Rushing(NOTE: Hacked means written)

	// ��Ҫ���û���֧��iconv���������Ĳ���������������
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

// ��¼����URL����־
function logurl($title,$content)
{
	global $logName;
	$james=fopen($logName,"a+");
	date_default_timezone_set(PRC);
	fwrite($james,"\r\n".date("Y-m-d H:i:s,A")." [".$title."]   ".$content."\n");
	fclose($james);
}

// ��¼����hmacʱ����־��Ϣ
function loghmac($str,$merchantKey,$hmac)
{
	global $logName;
	global $merchantKey;
	$james=@fopen($logName,"a+");
	date_default_timezone_set("Asia/Shanghai");
	@fwrite($james,"\r\n".date("Y-m-d H:i:s,A")."  [����ǩ���Ĳ���:]".$str."  [�̻���Կ:]".$merchantKey."   [����HMAC:]".$hmac);
	@fclose($james);
}
?>