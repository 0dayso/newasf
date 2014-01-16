<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-7-23
 * Time: 下午3:33
 * To change this template use File | Settings | File Templates.
 */
class Apiaction extends IniAction{
    function index(){
     //   import('ORG.Util.XmlSender');// 导入分页类

        $str_xml = <<< xml
        <OTA_AirOrderQueryRQ xmlns="http://www.trafree.com/OTA/2011/05" Version="1.000" encoding="UTF-8">
        <POS>
        <Requestors>
        <Requestor Type="13"  Password="B2767789E670B0A770923A01CB42B532" ID="aishangfei" />
        </Requestors>
        </POS>
        <Order OrderId="F130716001486d6"/>
        <!--OrderId本系统中的订单编号，并非是代理商自己的系统中的订单编号-->
        </OTA_AirOrderQueryRQ>
xml;


        $ch = curl_init();
        $url = "http://interface.trafree.com/portal";
        $header[] = "Content-type: text/xml";//定义content-type为xml
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$str_xml);
        $response = curl_exec($ch);
        if(curl_errno($ch)) {
            print curl_error($ch);
        }
        curl_close($ch);

        print_r($response);


        $this->display();
    }

}