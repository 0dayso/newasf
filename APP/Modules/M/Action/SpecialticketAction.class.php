<?php
// 特价机票控制器
class SpecialticketAction extends IniAction {
	
    public function index(){

        $this->title="特价机票";
        $cheap=D('Cheap');
        $where['zhou']='美洲';
        $rowNum=5;
        $rs=$cheap->where($where)->order('id desc')->limit(20)->select();
        $arr=array();
        foreach($rs as $k=>$v){
            $time=explode('-',$v['time']);
            if(is_array($time)){
                $times=isset($time[1])?$time[1]:$time[0];
                if(stristr($times,'.')){
                    $times=str_replace('.','-',$times);
                }
                $strtotime=strtotime($times);
                $v['time']=date("Y-m-d",$strtotime);
                if($strtotime<time()){
                    continue;
                }
            }
            if(count($arr)>$rowNum){
                continue;
            }

            $arr[]=$v;
        }

        $this->list=$arr;
        $this->display();
    }

    //按出发地调用
	public function airticket(){
            $rowNum=10;
            $cheap=D('Cheap');
            $from=I('from')?I('from'):'bj';
            if($from=='gz'){
                $from_name='广州';
            }
            elseif($from=='bj'){
                $from_name='北京';
            }
            elseif($from=='sh'){
                $from_name='上海';
            }
            elseif($from=='sz'){
                $from_name='深圳';
            }
            elseif($from=='xg'){
                $from_name='香港';
            }

            $from_name=I('from');
            $where['from_city']=$from_name;
            $zhou=$cheap->field('zhou')->group('zhou')->select();
            $rs=$cheap->where($where)->select();
            $arr=array();

            foreach($zhou as $zk=>$zv){
                foreach($rs as $k=>$v){
                    $time=explode('-',$v['time']);
                    if(is_array($time)){
                        $times=isset($time[1])?$time[1]:$time[0];
                        if(stristr($times,'.')){
                            $times=str_replace('.','-',$times);
                        }
                        $strtotime=strtotime($times);
                        $v['time']=date("Y-m-d",$strtotime);
                        if($strtotime<time()){
                            continue;
                        }
                    }

                    if($zv['zhou']==$v['zhou']){
                        $arr[$zk]['zhou']=$zv['zhou'];
                        if(count($arr[$zk]['child'])<$rowNum)
                        $arr[$zk]['child'][$k]=$v;
                    }
                }
            }
           $this->from_name=$from_name;
           $this->list=$arr;
        $this->title="从 $from_name 出发的 特价机票";
        $this->display();
    }
	
	
}