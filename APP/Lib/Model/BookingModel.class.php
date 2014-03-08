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

class BookingModel extends RelationModel{
	protected $_link = array(
		'user'=> array(  
			'mapping_type'=>BELONGS_TO,
			'class_name'=>'user',
			'user_id'=>'id',

			'mapping_fields'=>'id,username,name,avatar,status',
           // 定义更多的关联属性
		),
		'member'=> array(  
			'mapping_type'=>BELONGS_TO,
			'class_name'=>'member',
			'member_id'=>'id',
			'mapping_fields'=>'id,username,name',     
		),
	);


    //最近订单
     function nearOrder($num=6){
        import('@.ORG.CJSON');
        $prefix=C('DB_PREFIX');

        $where_department=isset($_GET['department'])?" and u.department_id=".$_GET['department']:'';
        if(!S('near_order_'.$num)){ //检查是 最近订单 否缓存
            $recentOrder=$this->query("select b.id,b.order_id,b.user_id, b.member_id,b.from_iata,b.to_iata,b.booking_references,u.name u_name,u.id u_id,u.username u_username,u.avatar u_avatar,m.name m_name  from {$prefix}booking b left join {$prefix}user u on b.user_id=u.id left join {$prefix}member m on b.member_id=m.id where  u.avatar!='' $where_department  order by id desc limit $num");
            foreach($recentOrder as $k=>$v){
                $recentOrder[$k]['booking_references']=CJSON::decode($v['booking_references']);
            }

            $citydb=D('City');
            foreach($recentOrder as $k=>$v){ //最近订单
                $iata=$v['booking_references']['BookingReference'][0]['OriginDestinationOption'][0]['FlightSegment'];
                $from=$citydb->getCityName($iata[0]['DepartureAirport']);
                $airline=$v['booking_references']['BookingReference'][0]['OriginDestinationOption'][0]['FilingAirline'];

                $to=$citydb->getCityName(($iata[count($iata)-1]['ArrivalAirport']));

                $order[$k]['user']=array('id'=>$v['u_id'],'name'=>$v['u_name'],'avatar'=>$v['u_avatar'],'username'=>$v['u_username']);
                $order[$k]['membername']=$v['m_name'];
                $order[$k]['airline']=$airline;;
                $order[$k]['airline_name']=D('Airline')->getAirlineName($airline);;
                $order[$k]['from']=$from;
                $order[$k]['from_iata']=$citydb->getCityIata($iata[0]['DepartureAirport']);
                $order[$k]['to']=$to;
                $order[$k]['to_iata']=$citydb->getCityIata($iata[count($iata)-1]['ArrivalAirport']);
                $order[$k]['order_id']=$v['order_id'];
            }
            S('near_order_'.$num,$order,600);
        }else{
            $order=S('near_order_'.$num); //读取缓存
        }
       return $order;

    }

    function orderCancel(){
        if(I('id')){
            $where['id']=I('id');
            $where['member_id']=getUid();
            $data['order_status']=-1;

            $rs=$this->where($where)->save($data);
            if($rs)
                return true;
        }
    }
		
		

		
		
}