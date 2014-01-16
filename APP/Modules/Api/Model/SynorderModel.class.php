<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-7-24
 * Time: 上午10:24
 * To change this template use File | Settings | File Templates.
 */
class SynorderModel extends Model{
    # 将xml订单转换成数组
    function  xml2array($orderInfo){
        $order=array();
        $xml=simplexml_load_string($orderInfo);
        if(!$xml){
            exit("格式错误");
        }
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
         //   $rs=$priceInfo[0]->attributes();
       //     dump($rs);exit;
            for($num=0;$num<$priceInfoCount;$num++)
            {
                $priceInfo1=$priceInfo[$num]->attributes();
                $priceInfo2=(array)$priceInfo1;

                $order['BookingReferences']['BookingReference'][$i]['AirItineraryPricingInfo']['Agent']['PriceInfo'][$num]=$priceInfo2['@attributes'];
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


    # 订单同步响应
    function orderSynResponse($orderId,$status){
        $SYNORDER_ID=C('SYNORDER_ID');
        $SYNORDER_KEY=C('SYNORDER_KEY');
        if($status=='success'){
            $xml=<<<xml
            <OTA_OrderSynRQ xmlns="http://www.trafree.com/OTA/2011/05" Version="1.000">
            <POS>
              <Requestors>
                   <Requestor Type="1" Password="$SYNORDER_KEY" ID="$SYNORDER_ID" />
               </Requestors>
            </POS>
            <Success OrderId ="$orderId" />
            </OTA_OrderSynRQ>
xml;

        }elseif($status=='failed'){
            $xml=<<<xml
            <OTA_OrderSynRQ xmlns="http://www.trafree.com/OTA/2011/05" Version="1.000">
            <POS>
                 <Requestors>
                  <Requestor Type="1" Password="$SYNORDER_KEY" ID="$SYNORDER_ID" />
                 </Requestors>
            </POS>
            <Failed OrderId ="$orderId" />
            </OTA_OrderSynRQ>
xml;
        }
   //
    /*    echo $xml;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, C('SYNORDER_URL'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $rs=curl_exec($ch);
        curl_close($ch);
    */
        return  $xml;
    }



    # 查询订单
    function queryOrder($orderId)
    {
        $SYNORDER_ID=C('SYNORDER_ID');
        $SYNORDER_KEY=C('SYNORDER_KEY');
		$SYNORDER_URL=C('SYNORDER_URL');

        $xml=<<<xml
        <OTA_AirOrderQueryRQ xmlns="http://www.trafree.com/OTA/2011/05" Version="1.000" encoding="UTF-8">
        <POS>
        <Requestors>
        <Requestor Type="13"  Password="$SYNORDER_KEY" ID="$SYNORDER_ID" />
        </Requestors>
        </POS>
        <Order OrderId="$orderId"/>
        </OTA_AirOrderQueryRQ>
xml;
        $ch = curl_init();
        $header[] = "Content-type: text/xml";//定义content-type为xml
        curl_setopt($ch, CURLOPT_URL, $SYNORDER_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$xml);
       $rs=curl_exec($ch);
        curl_close($ch);
        return $rs;

        $ch=curl_init();
        $header[]="Content-type:text/xml";
        curl_setopt($ch,CURLOPT_URL,$URL);
        curl_setopt($ch,CURLOPT_RETURNTRANSFERT_);

    }

    # 订单存入数据库
    function saveOrder($orderInfo){

       $db=M('order');
	   $PREFIX=C('DB_PREFIX');
        if($orderInfo) {
            # 判断该订单是否已经存在
            $sql="SELECT  order_id FROM {$PREFIX}booking WHERE order_id='{$orderInfo['OrderId']}'";
            $rs=$db->query($sql);
            if(!$rs)
            {

                # 根据订单中的客户信息查找是否为会员
                if(is_numeric($orderInfo['uid'])){
                    $orderInfo['member_id']=$orderInfo['uid'];
                    $member=M("member");
                    $orderInfo['user_id']=$member->where("id='$orderInfo[member_id]'")->getField('user_id');
                }
                if(!$orderInfo['user_id']){
                    $condition='';
                    if(!empty($orderInfo['Travelerinfos'])) {
                        foreach($orderInfo['Travelerinfos'] as $k=>$v){
                            $condition.='name="'.$v['lastName'].$v['firstName'].'" OR ';
                        }
                    }
                    if(!empty($orderInfo['email']))
                        $condition.=' email="'.$orderInfo['email'].'" ';
                    if(!empty($orderInfo['cell']))
                        $condition.='OR mobile="'.$orderInfo['cell'].'" OR telephone="'.$orderInfo['cell'].'"';
                    $sql='SELECT id,user_id FROM '.$PREFIX.'member WHERE '.$condition.' limit 0,1';

                    $member=$db->query($sql);
                    if($member[0]['id']) //#  若该订单的客户姓名存在会员表中,则订单分配给维护该会员的客服
                    {
                        $orderInfo['user_id']=$member[0]['user_id'];
                        $orderInfo['member_id']=$member[0]['id'];
                    } else{ //# 若姓名不存在于会员表中,则自动客服
                        $orderInfo['user_id']=D("User")->AutoUserid();
                    }
                }

                $orderInfo['member_id']=$orderInfo['member_id']?$orderInfo['member_id']:0;
                $orderInfo['user_id']=$orderInfo['user_id']?$orderInfo['user_id']:0;
                $FlightSegment=$DepartureAirport= $orderInfo['BookingReferences']['BookingReference'][0]['OriginDestinationOption'][0]['FlightSegment'];
                $line_type=count($FlightSegment);
                $DepartureAirport= $FlightSegment[0]['DepartureAirport'];
                $ArrivalAirport= $FlightSegment[count($FlightSegment)-1]['ArrivalAirport'];
                $departure_time=$FlightSegment[0]['DepartureDateTime'];

                $sql="insert  into ".$PREFIX."booking(order_id,order_datetime,price_detail,email,cell,from_iata,to_iata,departure_time,line_type,travelerinfos,booking_references,user_id,member_id) VALUES('".$orderInfo['OrderId']."','".$orderInfo['orderDateTime']."','".$orderInfo['priceDetail']."','".$orderInfo['email']."','".$orderInfo['cell']."','".$DepartureAirport."','".$ArrivalAirport."','".$departure_time."','".$line_type."','".json_encode($orderInfo['Travelerinfos'])."','".json_encode($orderInfo['BookingReferences'])."',".$orderInfo['user_id'].",".$orderInfo['member_id'].")";

                $rs=$db->execute($sql);

                if(!$rs)
                {
                    $info=$orderInfo['OrderId']."\t订单信息没有正确入库\t".date('Y-m-d H:i:s')."\r\n";
                    file_put_contents('log/log.txt', $info, FILE_APPEND);
                 return   $this->orderSynResponse($orderInfo['OrderId'],'failed');
                }
                else
                    return   $this->orderSynResponse($orderInfo['OrderId'],'success');
            }
            else
               return  $this->orderSynResponse($orderInfo['OrderId'],'success');
        }
    }



}