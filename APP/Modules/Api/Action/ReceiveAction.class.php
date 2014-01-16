<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-7-23
 * Time: 下午6:11
 * To change this template use File | Settings | File Templates.
 */
class ReceiveAction extends Action{
    function index(){
        $GLOBALS['responseUrl']="http://interface.trafree.com/portal"; # 同步响应和订单查询地址

        if($_GET['action']=='')
        {
            if(isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'],'POST'))  # 判断是否为post请求
            {
                $orderXml=file_get_contents('php://input');  # 获取trafree推送的信息

                $orderInfo=xml2array($orderXml);

                $dir='../protected/data/synOrders/'.substr($orderInfo['orderDateTime'],0,10);
                if(!is_dir($dir))
                    mkdir($dir,'0777',true);
                file_put_contents($dir.'/'.$orderInfo['OrderId'].'.xml',$orderXml);

                saveOrder($orderInfo);

                mailTip($orderInfo['OrderId']);
            }
            else
            {
                header("Content-type: text/html; charset=utf-8");
                exit('禁止访问!!!');
            }
        }
        elseif($_GET['action']=='queryOrder')
        {
            queryOrder();
        }




# 将xml订单转换成数组
        function  xml2array($orderInfo)
        {
            $order=array();

            $xml=simplexml_load_string($orderInfo);

            # 提取订单编号等信息
            $orderInfo=(array)$xml->Order->attributes();
            $order=$orderInfo['@attributes'];

            # 提取联系人信息
            $contactInfo=(array)$xml->Contactinfo->attributes();
            $order+=$contactInfo['@attributes'];

            # 提取乘机人信息
            $travelerCount=count($xml->Travelerinfos->Travelerinfo);//php5.2;  $xml->Travelerinfos->Travelerinfo->count();//php5.3

            for($i=0;$i<$travelerCount;$i++)
            {
                $travelerInfo=(array)$xml->Travelerinfos->Travelerinfo[$i]->attributes();
                $order['Travelerinfos'][$i]=$travelerInfo['@attributes'];
            }

            # 提取航班信息
            $bookingReferenceCount=count($xml->BookingReferences->BookingReference);//php5.2; $xml->BookingReferences->BookingReference->count();//php5.3
            for($i=0;$i<$bookingReferenceCount;$i++)
            {
                $bookingReference=$xml->BookingReferences->BookingReference[$i];

                $bookingReferenceID=(array)$bookingReference->BookingReferenceID->attributes();
                $order['BookingReferences']['BookingReference'][$i]['BookingReferenceID']=$bookingReferenceID['@attributes'];

                $priceInfo=$bookingReference->AirItineraryPricingInfo->Agent->PriceInfo;
                $priceInfoCount=count($priceInfo);//php5.2; $priceInfo->count();//php5.3
                for($num=0;$num<$priceInfoCount;$num++)
                {
                    $priceInfo=(array)$priceInfo[$num]->attributes();
                    $order['BookingReferences']['BookingReference'][$i]['AirItineraryPricingInfo']['Agent']['PriceInfo'][$num]=$priceInfo['@attributes'];
                }

                $ticketingInfo=(array)$bookingReference->AirItineraryPricingInfo->TicketingInfo->attributes();
                $order['BookingReferences']['BookingReference'][$i]['AirItineraryPricingInfo']['TicketingInfo']=$ticketingInfo['@attributes'];

                $originDestinationOptionCount=count($bookingReference->OriginDestinationOption);//php5.2; $bookingReference->OriginDestinationOption->count();//php5.3
                for($j=0;$j<$originDestinationOptionCount;$j++)
                {
                    $originDestination=$bookingReference->OriginDestinationOption[$j];
                    $originDestinationInfo=(array)$originDestination->attributes();
                    $order['BookingReferences']['BookingReference'][$i]['OriginDestinationOption'][$j]=$originDestinationInfo['@attributes'];

                    $flightSegmentCount=count($originDestination->FlightSegment);//php5.2; $originDestination->FlightSegment->count();//php5.3
                    for($k=0;$k<$flightSegmentCount;$k++)
                    {
                        $flightSegment=$originDestination->FlightSegment[$k];
                        $flightSegmentInfo=(array)$flightSegment->attributes();
                        $order['BookingReferences']['BookingReference'][$i]['OriginDestinationOption'][$j]['FlightSegment'][$k]=$flightSegmentInfo['@attributes'];

                        $departureAirport=(array)$flightSegment->DepartureAirport->attributes();
                        $order['BookingReferences']['BookingReference'][$i]['OriginDestinationOption'][$j]['FlightSegment'][$k]['DepartureAirport']=$departureAirport['@attributes']['LocationCode'];

                        $arrivalAirport=(array)$flightSegment->ArrivalAirport->attributes();
                        $order['BookingReferences']['BookingReference'][$i]['OriginDestinationOption'][$j]['FlightSegment'][$k]['ArrivalAirport']=$arrivalAirport['@attributes']['LocationCode'];

                        $marketingAirline=(array)$flightSegment->MarketingAirline->attributes();
                        $order['BookingReferences']['BookingReference'][$i]['OriginDestinationOption'][$j]['FlightSegment'][$k]['MarketingAirline']=$marketingAirline['@attributes']['Code'];
                    }
                }
            }

            return $order;
        }

# 订单存入数据库
        function saveOrder($orderInfo)
        {
            $config=require_once('../protected/config/main.php');
            $config=$config['components']['db'];

            $db=new PDO($config['connectionString'], $config['username' ], $config['password']);

            if($orderInfo)
            {
                # 判断该订单是否已经存在
                $sql='SELECT  order_id FROM '.$config['tablePrefix' ].'order WHERE order_id="'.$orderInfo['OrderId'].'"';
                $rs=$db->query($sql);

                if(!$rs->rowCount())
                {
                    # 根据订单中的客户信息查找是否为会员
                    $condition='';
                    if(!empty($orderInfo['Travelerinfos']))
                    {
                        foreach($orderInfo['Travelerinfos'] as $k=>$v)
                        {
                            $condition.='name="'.$v['lastName'].$v['firstName'].'" OR  identity_no="'.$v['idNumber'].'" OR ';
                        }
                    }
                    if(!empty($orderInfo['email']))
                        $condition.='email="'.$orderInfo['email'].'" ';
                    if(!empty($orderInfo['cell']))
                        $condition.='OR mobile="'.$orderInfo['cell'].'" OR telephone="'.$orderInfo['cell'].'"';
                    $sql='SELECT id,user_id FROM '.$config['tablePrefix' ].'member WHERE '.$condition.' limit 0,1';
                    $member=$db->query($sql)->fetchAll();

                    if(isset($member['user_id'])) #  若该订单的客户姓名存在会员表中,则订单分配给维护该会员的客服
                    {
                        $orderInfo['user_id']=$member['user_id'];
                        $orderInfo['member_id']=$member['id'];
                    }
                    else # 若姓名不存在于会员表中,则将该订单分配给订单最少的客服
                    {
                        $sql='select u.id,count(o.user_id) from '.$config['tablePrefix'].'user u left join '.$config['tablePrefix'].'order o on u.id in (select userid from '.$config['tablePrefix'].'authassignment where itemname="客服") and u.id=o.user_id GROUP BY u.id ORDER BY count(o.user_id) ASC limit 0,1';
                        $kefuList=$db->query($sql)->fetchAll();

                        $orderInfo['user_id']=$kefuList[0]['id'];
                        $orderInfo['member_id']='NULL';
                    }

                    $sql="insert ".$config['tablePrefix']."order(order_id,order_datetime,price_detail,account,email,cell,travelerinfos,booking_references,user_id,member_id) VALUES('".$orderInfo['OrderId']."','".$orderInfo['orderDateTime']."','".$orderInfo['priceDetail']."','".$orderInfo['account']."','".$orderInfo['email']."','".$orderInfo['cell']."','".json_encode($orderInfo['Travelerinfos'])."','".json_encode($orderInfo['BookingReferences'])."',".$orderInfo['user_id'].",".$orderInfo['member_id'].")";
                    $rs=$db->exec($sql);

                    if(!$rs)
                    {
                        $info=$orderInfo['OrderId']."\t订单信息没有正确入库\t".date('Y-m-d H:i:s')."\r\n";
                        file_put_contents('log.txt', $info, FILE_APPEND);
                        orderSynResponse($orderInfo['OrderId'],'failed');
                    }
                    else
                        orderSynResponse($orderInfo['OrderId'],'success');
                }
                else
                    orderSynResponse($orderInfo['OrderId'],'success');
            }
        }

# 订单同步响应
        function orderSynResponse($orderId,$status)
        {
            if($status='success')
                $xml='<OTA_OrderSynRQ xmlns="http://www.trafree.com/OTA/2011/05" Version="1.000"><POS><Requestors><Requestor Type="1" Password="" ID="" /></Requestors></POS><Success OrderId ="'.$orderId.'" /></OTA_OrderSynRQ>';
            elseif($status='failed')
                $xml='<OTA_OrderSynRQ xmlns="http://www.trafree.com/OTA/2011/05" Version="1.000"><POS><Requestors><Requestor Type="1" Password="" ID="" /></Requestors></POS><Failed OrderId ="'.$orderId.'" /></OTA_OrderSynRQ>';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $GLOBALS['responseUrl']);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

            $rs=curl_exec($ch);
            curl_close($ch);
        }
    }
}