<?php

class DeliverAddressModel extends Model {

    //添加收货地址
    function addressAdd(){
        $data=$_POST;
        $data['create_time']=time();
        $data['member_id']=getUid();
        if(I("is_default")){
            $where['member_id']=getUid();
            $d['is_default']=0;
            $this->where($where)->save($d);
        }
        if($this->add($data)){
            return true;
        }
        return false;
    }

    //编辑收货地址
    function addressEdit(){
        $data=$_POST;
        $data['is_default']=I("is_default")?I("is_default"):0;
        $data['update_time']=time();
        $data['id']=I("id");
         if(!$this->create()){
              return false;
          }
        if(I("is_default")){
            $where['member_id']=getUid();
            $d['is_default']=0;
            $this->where($where)->save($d);
        }

        if($this->save($data)){
            return true;
        }
        return false;
    }

    //收货地址列表
    function addressList(){
        $where["member_id"]=getUid();
        $list= $this->where($where)->select();
        $where["is_default"]=1;
        $is_default= $this->where($where)->count();
        if($list && !$is_default){
            $list[count($list)-1]['is_default']=1;
        }
        foreach($list as $k=>$v){
            $address=$this->xml_address($v['province'],$v['city']);
            $list[$k]['province']=$address['province'];
            $list[$k]['city']=$address['city'];

        }
        return $list;
    }



    //解析xml城市地址
    function xml_address($province=0,$city=0){
        $this->cityList=$this->cityList?$this->cityList:simplexml_load_file(APP_PATH."Common/cityList.xml");
        if($this->cityList){
            $rs= $this->cityList->CountryRegion->State[(int)$province];
            $address['province']=(string)$rs->attributes()->Name;
            $address['city']=(string)$rs->City[(int)$city]->attributes()->Name;
            return $address;
        }
    }

}