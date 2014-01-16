<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-7-24
 * Time: 上午10:16
 * To change this template use File | Settings | File Templates.
 */
class SynorderAction extends Action{
    function index(){ //订单同步
        $order=D('Synorder');
        if(isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'],'POST'))  # 判断是否为post请求
        {
            $orderXml=file_get_contents('php://input');  # 获取trafree推送的信息
            Log::write($orderXml,'INFO','','log/synOrders/'.date('y_m_d').'.log');
            $orderInfo=$order->xml2array($orderXml);
            $dir=WEB_ROOT.'/log/synOrders/'.substr($orderInfo['orderDateTime'],0,10);
            if(!is_dir($dir))
                mkdir($dir,'0777',true);
            file_put_contents($dir.'/'.$orderInfo['OrderId'].'.xml',$orderXml);
            $rs= $order->saveOrder($orderInfo);
            header("Content-type: text/xml; charset=utf-8");
            echo $rs;

        //    mailTip($orderInfo['OrderId']);
        }
        else
        {
            header("Content-type: text/html; charset=utf-8");
            exit('禁止访问!!!');
        }
    }

    //查询订单；
    function queryOder(){
        $order=D('Synorder');
        $s=I('q');
        $rs=$order->queryOrder($s);
        echo $rs;
        $orderInfo=$order->xml2array($rs);
        $order->saveOrder($orderInfo);

  //     $rs2= $order->xml2array($rs);

    //    $xml=simplexml_load_string($rs);
        header("content-type:text/xml");
        print_r($rs2);
    }

}