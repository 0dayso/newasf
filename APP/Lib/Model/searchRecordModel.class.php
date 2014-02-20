<?php
//搜索记录模型
class searchRecordModel extends RelationModel{
	protected $_link = array(
		'member'=> array(
			'mapping_type'=>BELONGS_TO,
            'class_name'=>'member',
            'member_id'=>'id',
            'mapping_fields'=>'id,username,name',
            // 定义更多的关联属性
      ),
	);

    protected $_auto = array (
        array('member_id','getUid',1,'function'),
        array('create_time','time',1,'function'),
        array('ip','get_client_ip',1,'function'),
        array('source','get_source',1,'callback'),
        array('domain','get_domain',1,'callback'),
    );


    //自动完成  返回提交域名
    function get_domain(){
        $host=get_http_referer(1)?get_http_referer(1):$_SERVER['HTTP_HOST'];
        return $host;
    }

    function get_source(){
        $REFERER=isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        return $REFERER;
    }

    //插入数据
    function insert($data){
        if($this->create($data)){
            $this->add();
        }else{
            return $this->getError();
        }
    }

     function getList($limit=10,$where=array()){
         if(empty($where)){
             $where['from_city']=array('neq','');
             $where['to_city']=array('neq','');
             $where['from_date']=array('neq','');
         }
       return $this->where($where)->order('id desc')->limit($limit)->select();
     }

    function whileSearch($limit=10,$where=array()){
        $search_log=$this->getList($limit,$where);
        $typeArr=array('','往返','单程','多程');
        foreach($search_log as $kel=>$val){
        //    $search_log[$kel]['from_city'] = substr($val['from_city'],0,strrpos($val['from_city'],'('));
        //    $search_log[$kel]['to_city'] = substr($val['to_city'],0,strrpos($val['to_city'],'('));
            $sec=time()-$val['create_time'];
            if($sec<60){
                $res= $sec.'秒';
            }else{
                $sec = round($sec/60);
                if ($sec >= 60){
                    $hour = floor($sec/60);
                    $min = $sec%60;
                //    $res = $hour.'小时 ';
                 //   $min != 0  &&  $res .= $min.'分';
                    $res = $min.'分钟';
                }else{
                    $res = $sec.'分钟';
                }
            }
            $search_log[$kel]['from_now']=$res;
            $search_log[$kel]['type']=$typeArr[$val['type']];
            $from_city=urlencode($val['from_city']."(".$val['from_code'].")");
            $to_city=urlencode($val['to_city']."(".$val['to_code'].")");
            $search_log[$kel]['url']="iflight/flightquery/?flightType={$val['type']}&tickType=ADT&personNum=1&childNum=0&directFlightsOnly=2&originCode={$val['from_code']}_0&desinationCode={$val['to_code']}_0&originDate={$val['from_date']}&origin_name={$from_city}&desination_name={$to_city}&returnDate={$val['to_date']}";
        }

        return $search_log;
    }
	
	
		
		

		
		
}