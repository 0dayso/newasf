<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-7-11
 * Time: 上午10:19
 * To change this template use File | Settings | File Templates.
 */
class PointsModel extends RelationModel{
    protected $_validate = array(
        array('member_id', 'checkMemberId', '没有这个用户', 1,'callback',3),
    );
    protected $_auto = array (
        array('create_time','time',1,'function'),
    );
    protected $_link = array(
        'user'=> array(
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'member',
            'member_id'=>'id',
            'mapping_fields'=>'id,username,name',
            // 定义更多的关联属性 relation(true)
        ),
    );
    protected  function checkMemberId($uid){
        $rs=D('Member')->CheckId($uid);
        if($rs){
            return true;
        }else{
            return false;
        }
    }

     //增加积分
    function addPoints($uid,$points,$description,$type2=0){  
	   $mpoints=$this->where("member_id=$uid and type2=$type2" )->order('id DESC')->getField('last_points');  	
       $points= (int)$points;		   
        $arr['member_id']=$uid;
		$arr['type2']=$type2;
        $arr['points']="+".$points;
		$arr['type']=0;		
        $arr['description']=$description;
        $arr['last_points']=$mpoints+$points;
		$arr['create_time']=time();
        D('Member')->where("id=".$uid)->setField('points',$mpoints+$points);
	   if($this->create($arr)){
	   	 $rs=$this->add();
		 if($rs){
			return true;			   
		 }else{
	  	   return false;
	     }
	   }
	    return  false;
    }
	
	 //减少积分
    function cutPoints($uid,$points,$description,$type2=0){
	   $mpoints=$this->where("member_id=$uid and type2=$type2")->order('id DESC')->getField('last_points'); 
       $points= (int)$points;
		 if($mpoints<$points){
            return "积分不够";
         }
        $arr['member_id']=$uid;
		$arr['type2']=$type2;
        $arr['points']="-".$points;
		$arr['type']=1;
        $arr['description']=$description;
        $arr['last_points']=$mpoints-$points;
		$arr['create_time']=time();
	    D('Member')->where("id=".$uid)->setField('points',$mpoints-$points);
       if($this->create($arr)){         
          $rs=$this->add();
		  if($rs){
			return true;
		  }else{
			return false;
		  }		   
    	}
		return  false;
	}

    /*
     *  我的积分
     */
    function myPoints($uid,$type=0){
        $where['member_id']=$uid;
        $where['type2']=$type;
        $where['type']=0;
        $where['create_time']=array('gt',time()-3600*24*360);
        $ys=$this->where($where)->sum('points');//

        $where['type']=1;
        $xs=$this->where($where)->sum('points');//
    }

}