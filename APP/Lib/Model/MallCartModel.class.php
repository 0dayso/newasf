<?php

//购物车模型
class MallCartModel extends RelationModel{
	
    protected $_link = array(
        'mall'=> array(
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'mall',
            'mall_id'=>'id',
            'mapping_fields'=>'id,title,jifen,img',
            'as_fields'=>"type,title,jifen,img,status",
            // 定义更多的关联属性 relation(true)
        ),
    );
	
    protected $_auto = array (
        array('create_time','time',1,'function'),
    );
	
	
    function addCart(){
        $mall=M('mall');
        if($mallRs=$mall->field('id,title,type')->find(I('id'))){
            $num=isset($_GET['num'])?(int)I('num'):1;
            $where['mall_id']=I('id');
            $where['member_id']=getUid();
            $rs=$this->where($where)->find();
            if($rs){
                $where['id']=$rs['id'];
                $data['type']=$mallRs['type'];
                $data['num']=$rs['num']+$num;
                if($this->where($where)->save($data)){
                    return true;
                } else{
                    return false;
                }
            }else{
                $data['mall_id']=I('id');
                $data['type']=$mallRs['type'];
                $data['num']=$num;
                $data['member_id']=getUid();
                $data['create_time']=time();;
                if(!$this->create($data)){
                        exit($this->getError());
                }
                $rs=$this->add($data);
                if($rs){
                    return true;
                }
            }
        }
        return false;
    }
	
    //删除
    function delCart($id=''){
        if(is_array($id)){
            $where['id']=array('in',$id);
        }else{
            $where['id']=$id?$id:I('id');
        }

        $where['member_id']=getUid();
        if ($this->where($where)->delete()) {
            return true;
        } else {
            return false;
			
        }
    }

    //清空购物车
    function delAllCart($id=''){
        $where['member_id']=getUid();
        if ($this->where($where)->delete()) {
            return true;
        } else {
            return false;
        }
    }

    function exchange($array=array()){
        $member=M("member");
        $uid=getUid();
        $points=$member->where("id=$uid")->getField('points');
        if($array['total']>$points){
            return false;
        }

    }


    function confirm(){
        session('mall_cart_confirm','');
        $where['member_id']=getUid();
        $num=I("num");
        foreach($num as $k=>$v){
            $where['mall_id']=$k;
            $data['num']=$v;
            $this->where($where)->save($data);
        }

        $array= array_keys(I("id"));
        $mall_id=implode(",",$array);
        $where['mall_id']=array("in",$mall_id);
        $list=$this->where($where)->relation(true)->select();
        foreach($list as $key=>$val){
                $arr[$key]['id']=$val['id'];//商品放到购物车里面的id号
                $arr[$key]['mall_id']=$val['mall_id'];//商品id
                $arr[$key]['num']=$val['num']; //数量
                $arr[$key]['jifen']=$val['jifen'];//积分
                $arr[$key]['title']=$val['title'];//标题
				$arr[$key]['order_num']=$val['order_num'];//订单号
				$arr[$key]['statue']=$val['statue'];//发货状态：0未发 1已发
        }
        session('mall_cart_confirm',$arr);

        return $list;

    }


}