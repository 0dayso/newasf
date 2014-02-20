<?php
//会员模型
class AsmsUserModel extends RelationModel{
    protected $_link = array(
        'user'=> array(
        'mapping_type'=>HAS_ONE ,
        'class_name'=>'user',
        'foreign_key'=>'asms_user_id',
        'condition'=>'status=1',
        'mapping_fields'=>'id,username,name,status',
            // 定义更多的关联属性 relation(true)
        ),
    );

    protected $_auto = array (
        array('update_time','time',3,'function'),
    );


    //asms 检测登陆操作
    function check_login(){
        //  echo date('Y-m-d H:i:s',filemtime(COOKIE_FILE));
        if(filemtime(COOKIE_FILE)>(time()-500)) return true;
        $fields = "bh=".C('ASMS_ACCOUNT')."&method=checkLogin&kl=".C('ASMS_PWD')."&call=&callnum=&randtime"; //登陆post 数据
        $index=curl_post(C('ASMS_HOST').'/asms/','',COOKIE_FILE,0);
        if(!$index){
            return  curl_login(C('ASMS_HOST').'/sysmanage/login.shtml',$fields,COOKIE_FILE);
        }
    }

    //客服id 转换
    function asmsUserTo($ywyid){
        if(empty($ywyid)){return false;};
        $this->update($ywyid);
        return  D('User')->where("asms_user_id='$ywyid'")->getField('id');
    }

    //获取业务员信息
    function get_user($bh='',$xm='',$ygzt=1){
        $this->check_login();
        $post_arr=array();
        $post_arr['bh']=$bh;
        $post_arr['xm']=$xm;
        $post_arr['ygzt']=$ygzt;
        $data=curl_post(C('ASMS_HOST').'/asms/sysmanage/yhb_right.shtml?count=2000',$post_arr,COOKIE_FILE);
        if(!$data){return -1;}
        $preg="/class=\"czimg\">\s+<\/td>\s+<td>(.*?)<\/td>\s+<td.*?>(.*?)<\/td>\s+<td>(.*?)<\/td>\s+<td(.*?)<\/td>\s+<td>(.*?)<\/td>\s+<td>(.*?)<\/td>\s+<td>(.*?)<\/td>\s+<td>(.*?)<\/td>\s+<td>(.*?)<\/td>\s+<td>(.*?)<\/td>\s+<td>(.*?)<\/td>\s+<td>(.*?)<\/td>\s+<td>(.*?)<\/td>\s+<td>(.*?)<\/td><\/tr>/Ui";

        preg_match_all($preg,$data,$info);
        if(empty($info)) return false; //没有数据

        $keyName=array(1=>'ywyid',2=>'name',3=>'status',4=>'tel',5=>'phone',6=>'qxjb',7=>'yhlx',8=>'fjzt',9=>'wscx',10=>'email',11=>'address',12=>'department',13=>'company',14=>'create_date');

        foreach($info as $key=>$val){
            foreach($val as $k=>$v){
                if($key==0) continue;
                if($key==4){
                    if(strstr($v,'title')){
                        preg_match("/title=\"(.*?)\"/",$v,$vf);
                        $v=$vf[1];
                    }else{
                        $v=strip_tags($v);
                        $v=str_replace('>','',$v);
                    }
                }
                if($key==7){
                    $v = rtrim(str_replace('&nbsp;',',',$v),',');
                }elseif(in_array($v,array('正常','可以','启用'))){// 替换
                    $v=1;
                }elseif(in_array($v,array('离职','不可以','不启用'))){
                    $v=0;
                }
                $arr[$k][$keyName[$key]]=$v;
            }
        }
     //   print_r($arr);
        return $arr;
    }

    /**
     * 全新数据插入
     */
    function insert_all(){
        $rs=$this->get_user('','','');
        if(empty($rs)) return false;
        foreach($rs as $val){
            if($this->field('ywyid')->find($val['ywyid'])){
                $save= $this->create($val);
                $this->save($save);
            }else{
                $this->create($val);
                $this->add();
            }
            echo $this->getDBError();
        }
        return true;
    }

    /**更新数据
     * @param string $bh
     * @param string $xm
     */
    function update($bh='',$xm=''){
        $rs=$this->get_user($bh,$xm);
        $where['ywyid']=$bh;
        $yw=$this->field('ywyid')->where($where)->select();

        foreach($rs as $val){
            if($yw){
                //更新
                $save= $this->create($val);
                $this->save($save);
            }else{
                //插入
                $this->create($val);
                $this->add();
            }
        }
    }
		
}

?>