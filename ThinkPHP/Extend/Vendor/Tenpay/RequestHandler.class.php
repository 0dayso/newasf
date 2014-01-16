<?php
/**
 * 锟斤拷锟斤拷锟斤拷
 * ============================================================================
 * api2说锟斤拷锟斤拷
 * init(),锟斤拷始锟斤拷锟斤拷锟斤拷默锟较革拷一些锟斤拷锟斤拷值锟斤拷锟斤拷cmdno,date锟饺★拷
 * getGateURL()/setGateURL(),锟斤拷取/锟斤拷锟斤拷锟斤拷诘锟街?锟斤拷锟斤拷锟斤拷锟街?
 * getKey()/setKey(),锟斤拷取/锟斤拷锟斤拷锟斤拷钥
 * getParameter()/setParameter(),锟斤拷取/锟斤拷锟矫诧拷锟斤拷值
 * getAllParameters(),锟斤拷取锟斤拷锟叫诧拷锟斤拷
 * getRequestURL(),锟斤拷取锟斤拷锟斤拷锟斤拷锟斤拷锟斤拷URL
 * doSend(),锟截讹拷锟津到财革拷通支锟斤拷
 * getDebugInfo(),锟斤拷取debug锟斤拷息
 * 
 * ============================================================================
 *
 */
class RequestHandler {
	
	/** 锟斤拷锟絬rl锟斤拷址 */
	var $gateUrl;
	
	/** 锟斤拷钥 */
	var $key;
	
	/** 锟斤拷锟斤拷牟锟斤拷锟?*/
	var $parameters;
	
	/** debug锟斤拷息 */
	var $debugInfo;
	
	function __construct() {
		$this->RequestHandler();
	}
	
	function RequestHandler() {
		$this->gateUrl = "https://www.tenpay.com/cgi-bin/v1.0/service_gate.cgi";
		$this->key = "";
		$this->parameters = array();
		$this->debugInfo = "";
	}
	
	/**
	*锟斤拷始锟斤拷锟斤拷锟斤拷
	*/
	function init() {
		//nothing to do
	}
	
	/**
	*锟斤拷取锟斤拷诘锟街?锟斤拷锟斤拷锟斤拷锟街?
	*/
	function getGateURL() {
		return $this->gateUrl;
	}
	
	/**
	*锟斤拷锟斤拷锟斤拷诘锟街?锟斤拷锟斤拷锟斤拷锟街?
	*/
	function setGateURL($gateUrl) {
		$this->gateUrl = $gateUrl;
	}
	
	/**
	*锟斤拷取锟斤拷钥
	*/
	function getKey() {
		return $this->key;
	}
	
	/**
	*锟斤拷锟斤拷锟斤拷钥
	*/
	function setKey($key) {
		$this->key = $key;
	}
	
	/**
	*锟斤拷取锟斤拷锟斤拷值
	*/
	function getParameter($parameter) {
		return $this->parameters[$parameter];
	}
	
	/**
	*锟斤拷锟矫诧拷锟斤拷值
	*/
	function setParameter($parameter, $parameterValue) {
		$this->parameters[$parameter] = $parameterValue;
	}
	
	/**
	*锟斤拷取锟斤拷锟斤拷锟斤拷锟斤拷牟锟斤拷锟?
	*@return array
	*/
	function getAllParameters() {
		return $this->parameters;
	}
	
	/**
	*锟斤拷取锟斤拷锟斤拷锟斤拷锟斤拷锟斤拷URL
	*/
	function getRequestURL() {
	
		$this->createSign();
		
		$reqPar = "";
		ksort($this->parameters);
		foreach($this->parameters as $k => $v) {
			$reqPar .= $k . "=" . urlencode($v) . "&";
		}
		
		//去锟斤拷锟斤拷锟揭伙拷锟?
		$reqPar = substr($reqPar, 0, strlen($reqPar)-1);
		
		$requestURL = $this->getGateURL() . "?" . $reqPar;
		
		return $requestURL;
		
	}
		
	/**
	*锟斤拷取debug锟斤拷息
	*/
	function getDebugInfo() {
		return $this->debugInfo;
	}

	/**
	*锟截讹拷锟津到财革拷通支锟斤拷
	*/
	function doSend() {
		header("Location:" . $this->getRequestURL());
		exit;
	}
	
	/**
	*锟斤拷锟斤拷md5摘要,锟斤拷锟斤拷锟斤拷:锟斤拷锟斤拷锟斤拷锟斤拷锟絘-z锟斤拷锟斤拷,锟斤拷锟斤拷锟斤拷值锟侥诧拷锟斤拷渭锟角╋拷锟?
	*/
	function createSign() {
		$signPars = "";
		ksort($this->parameters);
		foreach($this->parameters as $k => $v) {
			if("" != $v && "sign" != $k) {
				$signPars .= $k . "=" . $v . "&";
			}
		}
		$signPars .= "key=" . $this->getKey();
		$sign = strtolower(md5($signPars));
		$this->setParameter("sign", $sign);
		
		//debug锟斤拷息
		$this->_setDebugInfo($signPars . " => sign:" . $sign);
		
	}
	
	/**
	*锟斤拷锟斤拷debug锟斤拷息
	*/
	function _setDebugInfo($debugInfo) {
		$this->debugInfo = $debugInfo;
	}

}

?>