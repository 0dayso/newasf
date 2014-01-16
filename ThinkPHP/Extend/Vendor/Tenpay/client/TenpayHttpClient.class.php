<?php

/**
 * http锟斤拷https通锟斤拷锟斤拷
 * ============================================================================
 * api2说锟斤拷锟斤拷
 * setReqContent($reqContent),锟斤拷锟斤拷锟斤拷锟斤拷锟斤拷锟捷ｏ拷锟斤拷锟斤拷post锟斤拷get锟斤拷锟斤拷锟斤拷get锟斤拷式锟结供
 * getResContent(), 锟斤拷取应锟斤拷锟斤拷锟斤拷
 * setMethod($method),锟斤拷锟斤拷锟斤拷锟襟方凤拷,post锟斤拷锟斤拷get
 * getErrInfo(),锟斤拷取锟斤拷锟斤拷锟斤拷息
 * setCertInfo($certFile, $certPasswd, $certType="PEM"),锟斤拷锟斤拷证锟介，双锟斤拷https时锟斤拷要使锟斤拷
 * setCaInfo($caFile), 锟斤拷锟斤拷CA锟斤拷锟斤拷式未pem锟斤拷锟斤拷锟斤拷锟斤拷锟津不硷拷锟?
 * setTimeOut($timeOut)锟斤拷 锟斤拷锟矫筹拷时时锟戒，锟斤拷位锟斤拷
 * getResponseCode(), 取锟斤拷锟截碉拷http状态锟斤拷
 * call(),锟斤拷锟斤拷锟斤拷媒涌锟?
 * 
 * ============================================================================
 *
 */

class TenpayHttpClient {
	//锟斤拷锟斤拷锟斤拷锟捷ｏ拷锟斤拷锟斤拷post锟斤拷get锟斤拷锟斤拷锟斤拷get锟斤拷式锟结供
	var $reqContent;
	//应锟斤拷锟斤拷锟斤拷
	var $resContent;
	//锟斤拷锟襟方凤拷
	var $method;
	
	//证锟斤拷锟侥硷拷
	var $certFile;
	//证锟斤拷锟斤拷锟斤拷
	var $certPasswd;
	//证锟斤拷锟斤拷锟斤拷PEM
	var	$certType;
	
	//CA锟侥硷拷
	var $caFile;
	
	//锟斤拷锟斤拷锟斤拷息
	var $errInfo;
	
	//锟斤拷时时锟斤拷
	var $timeOut;
	
	//http状态锟斤拷
	var $responseCode;
	
	function __construct() {
		$this->TenpayHttpClient();
	}
	
	
	function TenpayHttpClient() {
		$this->reqContent = "";
		$this->resContent = "";
		$this->method = "post";

		$this->certFile = "";
		$this->certPasswd = "";
		$this->certType = "PEM";
		
		$this->caFile = "";
		
		$this->errInfo = "";
		
		$this->timeOut = 120;
		
		$this->responseCode = 0;
		
	}
	
	
	//锟斤拷锟斤拷锟斤拷锟斤拷锟斤拷锟斤拷
	function setReqContent($reqContent) {
		$this->reqContent = $reqContent;
	}
	
	//锟斤拷取锟斤拷锟斤拷锟斤拷锟?
	function getResContent() {
		return $this->resContent;
	}
	
	//锟斤拷锟斤拷锟斤拷锟襟方凤拷post锟斤拷锟斤拷get	
	function setMethod($method) {
		$this->method = $method;
	}
	
	//锟斤拷取锟斤拷锟斤拷锟斤拷息
	function getErrInfo() {
		return $this->errInfo;
	}
	
	//锟斤拷锟斤拷证锟斤拷锟斤拷息
	function setCertInfo($certFile, $certPasswd, $certType="PEM") {
		$this->certFile = $certFile;
		$this->certPasswd = $certPasswd;
		$this->certType = $certType;
	}
	
	//锟斤拷锟斤拷Ca
	function setCaInfo($caFile) {
		$this->caFile = $caFile;
	}
	
	//锟斤拷锟矫筹拷时时锟斤拷,锟斤拷位锟斤拷
	function setTimeOut($timeOut) {
		$this->timeOut = $timeOut;
	}
	
	//执锟斤拷http锟斤拷锟斤拷
	function call() {
		//锟斤拷锟斤拷一锟斤拷CURL锟结话
		$ch = curl_init();

		// 锟斤拷锟斤拷curl锟斤拷锟斤拷执锟叫碉拷锟筋长锟斤拷锟斤拷
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeOut);

		// 锟斤拷取锟斤拷锟斤拷息锟斤拷锟侥硷拷锟斤拷锟斤拷锟斤拷式锟斤拷锟截ｏ拷锟斤拷锟斤拷直锟斤拷锟斤拷锟斤拷锟?
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

		// 锟斤拷证锟斤拷锟叫硷拷锟絊SL锟斤拷锟斤拷锟姐法锟角凤拷锟斤拷锟?
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
				
		
		$arr = explode("?", $this->reqContent);
		if(count($arr) >= 2 && $this->method == "post") {
			//锟斤拷锟斤拷一锟斤拷锟斤拷锟斤拷锟絇OST锟斤拷锟斤拷锟斤拷锟斤拷为锟斤拷application/x-www-form-urlencoded锟斤拷锟斤拷锟斤拷?锟结交锟斤拷一锟斤拷
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_URL, $arr[0]);
			//要锟斤拷锟酵碉拷锟斤拷锟斤拷锟斤拷锟?
			curl_setopt($ch, CURLOPT_POSTFIELDS, $arr[1]);
		
		}else{
			curl_setopt($ch, CURLOPT_URL, $this->reqContent);
		}
		
		//锟斤拷锟斤拷证锟斤拷锟斤拷息
		if($this->certFile != "") {
			curl_setopt($ch, CURLOPT_SSLCERT, $this->certFile);
			curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $this->certPasswd);
			curl_setopt($ch, CURLOPT_SSLCERTTYPE, $this->certType);
		}
		
		//锟斤拷锟斤拷CA
		if($this->caFile != "") {
			// 锟斤拷锟斤拷证证锟斤拷锟斤拷源锟侥硷拷椋?锟斤拷示锟斤拷止锟斤拷证锟斤拷暮戏锟斤拷缘募锟介。1锟斤拷要锟斤拷锟斤拷CURLOPT_CAINFO
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
			curl_setopt($ch, CURLOPT_CAINFO, $this->caFile);
		} else {
			// 锟斤拷锟斤拷证证锟斤拷锟斤拷源锟侥硷拷椋?锟斤拷示锟斤拷止锟斤拷证锟斤拷暮戏锟斤拷缘募锟介。1锟斤拷要锟斤拷锟斤拷CURLOPT_CAINFO
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		}
		
		// 执锟叫诧拷锟斤拷
		$res = curl_exec($ch);
		$this->responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		if ($res == NULL) { 
		   $this->errInfo = "call http err :" . curl_errno($ch) . " - " . curl_error($ch) ;
		   curl_close($ch);
		   return false;
		} else if($this->responseCode  != "200") {
			$this->errInfo = "call http err httpcode=" . $this->responseCode  ;
			curl_close($ch);
			return false;
		}
		
		curl_close($ch);
		$this->resContent = $res;

		
		return true;
	}
	
	function getResponseCode() {
		return $this->responseCode;
	}
	
}
?>