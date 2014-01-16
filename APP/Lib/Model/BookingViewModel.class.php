<?php
/**
 * This is the model class for table "{{booking}}".
 *
 * The followings are the available columns in table '{{booking}':
 * @property string $id
 * @property string $order_id
 * @property string $order_datetime
 * @property string $price_detail
 * @property string $account
 * @property string $email
 * @property string $cell
 * @property string $travelerinfos
 * @property string $booking_references
 * @property integer $user_id
 * @property string $update_time
 * @property integer $order_status
 *
 * The followings are the available model relations:
 * @property User $user
 */

//订单视图模型
class BookingViewModel extends ViewModel{
    public $total_price; //总价格
    public $viewFields = array(
        'Booking'=>array('id','order_id','order_datetime','price_detail','email','cell','from_iata','to_iata','departure_time','line_type','user_id','member_id','order_status','_type'=>'LEFT'),//travelerinfos,booking_references
       'City'=>array('_as'=>'City','name'=>'from_city', '_on'=>'Booking.from_iata=City.iata','_type'=>'LEFT'),
       'City1'=>array('name'=>'to_city', '_on'=>'Booking.to_iata=City1.iata','_table'=>"asf_city",'_type'=>'LEFT'),

        'User'=>array('name'=>'user_name','username'=>'u_name', '_on'=>'Booking.user_id=User.id','_type'=>'LEFT'),
        'Member'=>array('name'=>'member_name','username'=>'m_name', '_on'=>'Booking.member_id=Member.id','_type'=>'LEFT'),
    );


    //订单列表
    function bookingList($where='',$limit=20){
        $lineTypeArr=array('','单程','往返','多程');
        $booking=$this->where($where)->order("order_datetime desc")->limit($limit)->select();
     //   echo $limit;
     //   print_r($booking);
        foreach($booking as $k=>$v){
            if(!$v['from_city'])
                $booking[$k]['from_city']=D("City")->getCityName($v['from_iata']);
            if(!$v['to_city'])
                $booking[$k]['to_city']=D("City")->getCityName($v['to_iata']);

            $booking[$k]['lineType']=$lineTypeArr[$v['line_type']];
            $price_des=explode("|",$v['price_detail']);
            $price_des=array_filter($price_des);

            $price_val=0;
            foreach($price_des as $kk=>$vv ){
                $preg="/\(([0-9]*)\+([0-9]*)\)\*([0-9]*)/";//(13690+2925)*2
                preg_match_all($preg,$vv,$info);
                switch($kk){
                    case(0):
                        $type_name="成人";
                        break;
                    case(1):
                        $type_name="儿童";
                        break;
                    case(2):
                        $type_name="学生";
                        break;
                    default:
                        $type_name="成人";
                }
                $price[$kk]['type_name']= $type_name;
                $price[$kk]['price']=$info[1][0];
                $price[$kk]['tax']=$info[2][0];
                $price[$kk]['num']=$info[3][0];
                $price[$kk]['total']=($info[1][0]+$info[2][0])*$info[3][0];
                $price_val+=$price[$kk]['total'];

            }
            $booking[$k]['price_des']=$price;

            $booking[$k]['member_name']=$v['member_name']?$v['member_name']:$v['m_name'];
            $booking[$k]['price']=$price_val;

           $this->total_price+=$price_val;

           $status= $v['order_status'];

            switch($status){
                case(-1):
                    $booking[$k]['status'] =  "已取消";
                    break;
                case(0):
                    $booking[$k]['status'] =  "待审核";
                    break;
                case(1):
                    $booking[$k]['status'] =  "待支付";
                    break;
                case(2):
                    $booking[$k]['status'] =  "已处理";
                    break;
                default:
                    $booking[$k]['status'] =  "待处理";

            }
        //   print_r($booking);
        }
        return $booking;
    }

    //订单详情
    function bookingInfo($where,$hb=1){
       // travelerinfos,booking_references
        $booking=D("Booking")->where($where)->find();
        import('@.ORG.CJSON');
        $info['order_id']=$booking['order_id'];
       $BookingReference=CJSON::decode($booking['booking_references']);
        $info['references']=$BookingReference['BookingReference'][0];

        foreach( $info['references']['OriginDestinationOption'] as $k=>$v){

            foreach($v['FlightSegment'] as $kk=>$vv ){
                $info['references']['OriginDestinationOption'][$k]['FlightSegment'][$kk]['FromCity']= D("City")->getCityName($vv['DepartureAirport']);
                $info['references']['OriginDestinationOption'][$k]['FlightSegment'][$kk]['FromAirport']= D("Airport")->getAirportCity($vv['DepartureAirport']);
                $info['references']['OriginDestinationOption'][$k]['FlightSegment'][$kk]['ToAirport']= D("Airport")->getAirportCity($vv['ArrivalAirport']);
                $info['references']['OriginDestinationOption'][$k]['FlightSegment'][$kk]['ToCity']= D("City")->getCityName($vv['ArrivalAirport']);
                $info['references']['OriginDestinationOption'][$k]['FlightSegment'][$kk]['Airline']=D("Airline")->getAirlineName($vv['MarketingAirline']);
            }

            $info['references']['OriginDestinationOption'][$k]['from']=$info['references']['OriginDestinationOption'][$k]['FlightSegment'][0]['FromCity'];
            $info['references']['OriginDestinationOption'][$k]['to']=$info['references']['OriginDestinationOption'][$k]['FlightSegment'][count($v['FlightSegment'])-1]['ToCity'];
        }



        $info['travelerinfo']=CJSON::decode($booking['travelerinfos']);

        foreach($info['travelerinfo'] as $k=>$v){
            switch($v['type']){
                case("ADT"):
                    $type_name="成人";
                    $type=0;
                    break;
                case("CNN"):
                    $type=1;
                    $type_name="儿童";
                    break;
                case("STU"):
                    $type=2;
                    $type_name="学生";
                    break;
                default:
                    $type=0;
                    $type_name="成人";
            }
            $info['travelerinfo'][$k]['type_name']=$type_name;

            $price_des=explode("|",$booking['price_detail']);
            $price_des=array_filter($price_des);
            foreach($price_des as $kk=>$vv ){
                $preg="/\(([0-9]*)\+([0-9]*)\)\*([0-9]*)/";//(13690+2925)*2
                preg_match_all($preg,$vv,$pf);
                if($type==0 && $kk==0){
                    $info['travelerinfo'][$k]['price']=$pf[1][0];
                    $info['travelerinfo'][$k]['tax']=$pf[2][0];
                }elseif($type==1 && $kk==1){
                    $info['travelerinfo'][$k]['price']=$pf[1][0];
                    $info['travelerinfo'][$k]['tax']=$pf[2][0];
                }elseif($type==2 && $kk==2){
                    $info['travelerinfo'][$k]['price']=$pf[1][0];
                    $info['travelerinfo'][$k]['tax']=$pf[2][0];
                }

            }

        }

     //   print_r($info);
        if($hb){
            $booking_view=$this->bookingList($where,1);
            $info=array_merge($info,$booking_view[0]);
        }
        return $info;

    }

    //订单支付详情
    function orderList($data,$where=''){
        $where['member_id']=getUid();

        foreach($data as $k=>$v){
            $where['id']=$v;
            $booking[$k]=$this->bookingInfo($where,"");
        }
        $where['id']=array("in",$data);
        $list=$this->bookingList($where);
      //  print_r($list);
        $array='';
        $this->total_price=0;
        foreach($list as $lk=>$lv){
           foreach($booking as $bk=>$bv){
               if($lv['order_id']==$bv['order_id']){
                  $array[$lk]=array_merge($lv,$bv);
               }
           }
           $this->total_price+=$lv['price'];
        }
        return $array;
    }

}