<?php
/**
 * 锟斤拷台应锟斤拷锟斤拷
 * ============================================================================
 * api2说锟斤拷锟斤拷
 * getKey()/setKey(),锟斤拷取/锟斤拷锟斤拷锟斤拷钥
 * getContent() / setContent(), 锟斤拷取/锟斤拷锟斤拷原始锟斤拷锟斤拷
 * getParameter()/setParameter(),锟斤拷取/锟斤拷锟矫诧拷锟斤拷值
 * getAllParameters(),锟斤拷取锟斤拷锟叫诧拷锟斤拷
 * isTenpaySign(),锟角凤拷聘锟酵ㄇ╋拷锟?true:锟斤拷 false:锟斤拷
 * getDebugInfo(),锟斤拷取debug锟斤拷息
 * 
 * ============================================================================
 *
 */

class ClientResponseHandler  {
	
	/** 锟斤拷钥 */
	var $key;
	
	/** 应锟斤拷牟锟斤拷锟?*/
	var $parameters;
	
	/** debug锟斤拷息 */
	var $debugInfo;
	
	//原始锟斤拷锟斤拷
	var $content;
	
	function __construct() {
		$this->ClientResponseHandler();
	}
	
	function ClientResponseHandler() {
		$this->key = "";
		$this->parameters = array();
		$this->debugInfo = "";
		$this->content = "";
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
	
	//锟斤拷锟斤拷原始锟斤拷锟捷ｏ拷确锟斤拷PHP锟斤拷锟斤拷支锟斤拷simplexml_load_string锟皆硷拷iconv锟斤拷锟斤拷锟斤拷锟斤拷锟斤拷趴锟斤拷锟?
	//一锟斤拷PHP5锟斤拷锟斤拷锟斤拷没锟斤拷锟解，PHP4锟斤拷要锟斤拷锟揭伙拷禄锟斤拷锟斤拷欠锟阶帮拷锟絠conv锟皆硷拷simplexml模锟斤拷
	function setContent($content) {
		$this->content = $content;
		
		$xml = simplexml_load_string($this->content);
		$encode = $this->getXmlEncode($this->content);
		
		if($xml && $xml->children()) {
			foreach ($xml->children() as $node){
				//锟斤拷锟接节碉拷
				if($node->children()) {
					$k = $node->getName();
					$nodeXml = $node->asXML();
					$v = substr($nodeXml, strlen($k)+2, strlen($nodeXml)-2*strlen($k)-5);
					
				} else {
					$k = $node->getName();
					$v = (string)$node;
				}
				
				if($encode!="" && $encode != "UTF-8") {
					$k = iconv("UTF-8", $encode, $k);
					$v = iconv("UTF-8", $encode, $v);
				}
				
				$this->setParameter($k, $v);			
			}
		}	
	}
	
	//锟斤拷锟斤拷原始锟斤拷锟斤拷
	//锟斤拷锟絇HP4锟较伙拷锟斤拷锟铰诧拷支锟斤拷simplexml锟皆硷拷iconv锟斤拷锟杰的猴拷锟斤拷
	function setContent_backup($content) {
		$this->content = $content;
		$encode = $this->getXmlEncode($this->content);
		$xml = new SofeeXmlParser(); 
		$xml->parseFile($this->content); 
		$tree = $xml->getTree(); 
		unset($xml); 
		foreach ($tree['root'] as $key => $value) {
			if($encode!="" && $encode != "UTF-8") {
				$k = mb_convert_encoding($key, $encode, "UTF-8");
				$v = mb_convert_encoding($value[value], $encode, "UTF-8");								
			}
			else 
			{
				$k = $key;
				$v = $value[value];
			}
			$this->setParameter($k, $v);
		}
	}
	
	
	
	//锟斤拷取原始锟斤拷锟斤拷
	function getContent() {
		return $this->content;
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
	*锟角凤拷聘锟酵ㄇ╋拷锟?锟斤拷锟斤拷锟斤拷:锟斤拷锟斤拷锟斤拷锟斤拷锟絘-z锟斤拷锟斤拷,锟斤拷锟斤拷锟斤拷值锟侥诧拷锟斤拷渭锟角╋拷锟?
	*true:锟斤拷
	*false:锟斤拷
	*/	
	function isTenpaySign() {
		$signPars = "";
		ksort($this->parameters);
		foreach($this->parameters as $k => $v) {
			if("sign" != $k && "" != $v) {
				$signPars .= $k . "=" . $v . "&";
			}
		}
		$signPars .= "key=" . $this->getKey();
		
		$sign = strtolower(md5($signPars));
		
		$tenpaySign = strtolower($this->getParameter("sign"));
				
		//debug锟斤拷息
		$this->_setDebugInfo($signPars . " => sign:" . $sign .
				" tenpaySign:" . $this->getParameter("sign"));
		
		return $sign == $tenpaySign;
		
	}
	
	/**
	*锟斤拷取debug锟斤拷息
	*/	
	function getDebugInfo() {
		return $this->debugInfo;
	}
	
	//锟斤拷取xml锟斤拷锟斤拷
	function getXmlEncode($xml) {
		$ret = preg_match ("/<?xml[^>]* encoding=\"(.*)\"[^>]* ?>/i", $xml, $arr);
		if($ret) {
			return strtoupper ( $arr[1] );
		} else {
			return "";
		}
	}
	
	/**
	*锟斤拷锟斤拷debug锟斤拷息
	*/	
	function _setDebugInfo($debugInfo) {
		$this->debugInfo = $debugInfo;
	}
	
	/**
	 * 锟角凤拷聘锟酵ㄇ╋拷锟?
	 * @param signParameterArray 签锟斤拷牟锟斤拷锟斤拷锟斤拷锟?
	 * @return boolean
	 */	
	function _isTenpaySign($signParameterArray) {
	
		$signPars = "";
		foreach($signParameterArray as $k) {
			$v = $this->getParameter($k);
			if("sign" != $k && "" != $v) {
				$signPars .= $k . "=" . $v . "&";
			}			
		}
		$signPars .= "key=" . $this->getKey();
		
		$sign = strtolower(md5($signPars));
		
		$tenpaySign = strtolower($this->getParameter("sign"));
				
		//debug锟斤拷息
		$this->_setDebugInfo($signPars . " => sign:" . $sign .
				" tenpaySign:" . $this->getParameter("sign"));
		
		return $sign == $tenpaySign;		
		
	
	}
	
}


?>