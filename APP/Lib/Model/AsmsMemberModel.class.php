<?php
//asms会员模型
class AsmsMemberModel extends RelationModel{

    protected $_link = array(
        'member'=> array(//关联用户表
            'mapping_type'=>HAS_ONE ,
            'class_name'=>'member',
            'foreign_key'=>'asms_member_id',
            'condition'=>'status=1',
            // 'mapping_fields'=>'id,username,name,status',
            // 定义更多的关联属性 relation(true)
        ),
        'user'=> array(//关联用户表
            'mapping_type'=>BELONGS_TO ,
            'class_name'=>'asms_user',
            'foreign_key'=>'ywyid',
            // 'mapping_fields'=>'id,username,name,status',
            // 定义更多的关联属性 relation(true)
        ),
    );

    protected $_auto = array (
        array('update_time','time',3,'function'),
    );

    //asms 检测登陆操作
    function check_login(){
        if(filemtime(COOKIE_FILE)>(time()-500)) return true;
        $fields = "bh=".C('ASMS_ACCOUNT')."&method=checkLogin&kl=".C('ASMS_PWD')."&call=&callnum=&randtime"; //登陆post 数据
        $index=curl_post(C('ASMS_HOST').'/asms/','',COOKIE_FILE,0);
        if(!$index){
            return  curl_login(C('ASMS_HOST').'/sysmanage/login.shtml',$fields,COOKIE_FILE);
        }
    }

    /**会员登陆
     * asms会员登陆
     * @param $hyzcm 用户名或 手机
     * @param $pwd 密码
     * @return bool|int|mixed
     */
    function login($hyzcm,$pwd){
        C('VERIFY_CODE',0);//临时关闭注册码
        $memberDB=D("Member");
        $_POST['name']=$hyzcm;
        $_POST['password']=$pwd;
        $MInfo=$memberDB->where("username='$hyzcm' or mobile='$hyzcm'")->find();
     //   print_r($MInfo);
        if($MInfo){ //本地 (本地数据库已有帐号)
            $mrs=$memberDB->login();
            if($mrs==true && getUid()){ //登陆成功
               $memberInfo = $memberDB->field('id,username,name,mobile,asms_member_id')->find(getUid());
               if($memberInfo['asms_member_id']){ //有asms关联id
                   $this->memberInfo($memberInfo['asms_member_id']);//更新
                   return true;
               }else{ // 没有asms关联id   查询asms里是否有相同电话
                   return  $this->relationReg($memberInfo);
               }
            }else{
                $this->error=$mrs;
                return false;
            }
        }else{ //asms 帐号 第一次登陆
            if(!C('ASMS_ONLINE')){ $this->error="ASMS 网络错误";return false;}
            $rs=preg_match('/^([0-9]){11}$/i',$hyzcm);
        //    dump($rs);exit;
            if($rs){
                $this->memberFind('','',$hyzcm);
                $rs=$this->checkMember(array('sj'=>$hyzcm));
                if(!is_numeric($rs)){
                    $this->error="手机号不存在";
                    return false;
                }
            }else{
                $this->memberFind($hyzcm);
                $rs=$this->checkMember($hyzcm);
                if(!is_numeric($rs)){
                    $this->error="用户不存在";
                    return false;
                }
            }

            $memberInfo=$this->relation(true)->find($rs);
        //    dump($memberInfo);
            if($pwd!=$memberInfo['mm']){
                $this->error="密码不正确";
                return false;
            }

            //添加到数据表
            $data['username']=$memberInfo['hyzcm'];
            $data['name']=$memberInfo['name']?$memberInfo['name']:$memberInfo['hyzcm'];
            $data['email']=$memberInfo['email'];
            $data['mobile']=$memberInfo['sj'];
            $data['address']=$memberInfo['lxdz'];
            $data['sex']==$memberInfo['xb'];

            $data['zip_code']==$memberInfo['yzbm'];

            $data['salt']=generateSalt();
            $data['password']=hashPassword($memberInfo['mm'],$data['salt']);

            $data['user_id']=D('AsmsUser')->asmsUserTo($memberInfo['ywyid']);
            $data['source']='Asms';
            $data['asms_member_id']=$rs;
            if(!$memberDB->create($data)){
                $this->error=$memberDB->getError();
                return false;
            }

            $rs=$memberDB->add();//asma 帐号添加到本地
            if($rs){ //
                return $memberDB->login();
            }
            return false;
         }

    }


  /*
   * 已登陆状态下关联帐号/注册asms
   */
  function relationReg($memberInfo){
      $memberDB=D("Member");
      if($memberInfo['mobile']){ //有电话
          $ars=$this->checkMember(array('sj'=>$memberInfo['mobile']));
          if(is_numeric($ars)){ //asms 电话存在
              $member_id=$memberDB->where("asms_member_id=$ars")->getField('id');
              if(!$member_id){ //asms id 没有被占用
                  $data['id']=$memberInfo['id'];
                  $data['asms_member_id']=$ars;
                  $rs=$memberDB->save($data);//关联asms帐号
                  return $rs;
              }
          }
      }

      //查询asms里是否有相同注册名
      $ars=$this->checkMember($memberInfo['username']);
      if(is_numeric($ars)){
          $member_id=$memberDB->where("asms_member_id=$ars")->getField('id');
          if(!$member_id){ //asms id 没有被占用
              $data['id']=$memberInfo['id'];
              $data['asms_member_id']=$ars;
              $rs=$memberDB->save($data);//关联asms帐号
              return $rs;
          }
      }
      $data=array();
      //无重复 注册到asms
      $data['hyzcm']=$memberInfo['username'];
      $data['sj']=$memberInfo['mobile'];
      $data['xm']=isset($memberInfo['name'])?$memberInfo['name']:$memberInfo['username'];
      $data['email']=$memberInfo['email'];
      $data['lxdz']=$memberInfo['address'];
      $ywyyid=D('User')->where("id='$memberInfo[user_id]'")->getField('ywyid');
      $data['ywyid']=$ywyyid?$ywyyid:C('ASMS_ACCOUNT');
      $this->register($data);//注册到asms
  }

    //获取用户信息
    function getMemberInfo($memberId=''){
        if(!$memberId)
            $memberId=isset($_GET['uid'])?$_GET['uid']:'';
        if(!$memberId)
            return false;
        $url= C('ASMS_HOST')."/asms/member/t_member_add.shtml?action=edit&compid=GZML&hylx=P&hyid=$memberId";
     //   echo $url;
        $data=curl_post($url,'',COOKIE_FILE,0);
        if(!$data){return -1;}
        if(empty($data)){
            $this->error="连接失败";
            return false;
        }
      //  print_r($data);
        $preg="/<input .*?name=\"(.*?)\" .*?value=\"(.*?)\".*?>/";
        preg_match_all($preg,$data,$info);

        $preg2="/<select .*?name=\"(.*?)\" .*?>.*?<\/select>/si";
        preg_match_all($preg2,$data,$info2);
        if(isset($info2[0])){
            foreach($info2[0] as $key=>$val){
                preg_match('/value=\"(.*?)\".*?selected/',$val,$f);
                $arr[$info2[1][$key]]=$f[1];
            }
        }
      //  print_r($info2);
        $preg3="/<textarea .*?name=\"(.*?)\".*?>(.*?)<\/textarea>/s";
        preg_match_all($preg3,$data,$info3);
     //    print_r($info3);
        if(empty($info[0])){
            $this->error='未找到相关数据';
            return false;
        }
    /*   $keyName = array(
            'hyzcm'=>'会员注册名',
            'xm'=>'真实姓名',
            'hyly'=>'会员来源',//M 男 "F" 女
            'xb'=>'性别',
            'sshy'=>'',//"NI" >身份证 "PP" >护照 "ID" >其它
            'zjhm'=>'证件号码',
            'zjlx2'=>'护照类型',
            'zjhm2'=>'护照号码',
            'zjyxq2'=>'护照有效期',
            'csrq'=>'出生日期',
            'hykh'=>'会员卡号',
            'hydj'=>'会员等级',
            'ywymc'=>'拓展业务员',
            'ywyid'=>'拓展业务员id',
            'bzbz1'=>'会员QQ',
            'bzbz2'=>'会员MSN',
            'bzbz3'=>'昵称',
            'ywmc'=>'英文名称',
            'pyjsm'=>'拼音检索码',
            'sjhm'=>'手机号码',
            'lxdz'=>'联系地址',
            'yzbm'=>'邮政编码',
            'jtdh'=>'住址电话',
            'email'=>'电子邮箱',
            'gzdw'=>'公司名称',
            'swzh'=>'工作职务',
            'gsdh'=>'公司电话',
            'gscz'=>'公司传真',
            'szsf'=>'所在国家或省份',
            'szdqmc'=>'所在地区',
            'www'=>'公司网址',
            'gsdz'=>'公司网址',
            'gsdz'=>'公司地址',
            'zj_khbh'=>'凭证代码',
            'jthmc'=>'集团名',
            'tsyq'=>'特殊要求',
            'bzbz'=>'备注',

        );
    */

        foreach($info as $key=>$val){
            foreach($val as $k=>$v){
                if($key==0) continue;
                if($key==1)
                    $kk[$k]=$v;
                if($key==2)
                    $arr[$kk[$k]]=$v;
            }
        }

        foreach($info3 as $key=>$val){
            foreach($val as $k=>$v){
                if($key==0) continue;
                if($key==1)
                    $kk[$k]=$v;
                if($key==2)
                    $arr[$kk[$k]]=$v;
            }
        }
        return($arr);
    }

    /**
     * 查找用户 可查多条
     * @param $shyzcm //会员注册名
     * @param string $is_info  详细信息
     * $arr_post['hyzcm']=$hyzcm;//会员注册名
     *$arr_post['shykh']=$shykh;//会员卡号
     *$arr_post['ssj']=$ssj;//会员手机
     *$arr_post['sjc']=$sjc;//会员姓名
     *$arr_post['wywid']=$wywid; //
     */
    function memberFindAll($hyzcm,$is_info=0){
        if(is_array($hyzcm)){
            $arr_post=$hyzcm;
        }else{
            $this->error='第一参数只能是数组';
            return false;
        }

        $this->check_login();
        $page_r=isset($_GET['page_r'])?$_GET['page_r']:20;
        $page_p=isset($_GET['page_p'])?$_GET['page_p']:1;
        $page_start=($page_r*$page_p)-$page_r;
        $date=isset($_GET['createdate1'])?"createdate1=".$_GET['createdate1']:'';
        $url=C('ASMS_HOST')."/asms/member/t_member_p_info.shtml?count=$page_r&start=$page_start&$date";

        $str= http_build_query($arr_post);
        $data=curl_post($url.$str,'',COOKIE_FILE,0);
        if(!$data){return -1;}
       //  echo $data;

        //正则匹配数据
        $preg="/<tr .*?>.*?<td.*?><a href=\"#\" onclick=\".*?(\d+).*?(\w+).*?\" .*?>(.*?)<\/a>.*?<\/td>\s<td (title=\"(.*?)\" style=.*?>.*?|style=.*?>(.*?))<\/td>\s<td .*?>(.*?)<\/td>\s<td .*?>(.*?)<\/td>\s<td .*?><a .*?>(.*?)<\/a>.*?<\/td>\s<td .*?><a .*?>(.*?)<\/a>.*?<\/td>\s<td .*?>(.*?)<\/td>\s<td .*?>\s<a .*?>(.*?)<\/a><\/td>\s<td .*?>(.*?)<\/td>\s<td>(.*?)<\/td>\s<td>(.*?)<\/td>\s<td>(.*?)<\/td>\s<td title=\"(.*?)\" .*?>.*?<td .*?<\/tr>/si";

        preg_match_all($preg,$data,$info);

        $pregs="/&nbsp;<b>(\d+)<\/b>.*? class=\"paginator_currentPage\"><input .*? value=\'(.*?)\'>/si";
        preg_match($pregs,$data,$infos);
      //  print_r($infos);
        if(!empty($infos[0])){
            $_GET['page_n']=$infos[1];
            $_GET['page_p']=$infos[2];
            $_GET['page_r']=$page_r;
            $_GET['page_tr']=Ceil($infos[1]/$page_r);
        }
        //设置 key值
        $keyName=array('','hyid','hylx','hyzcm','4','5','xm','hykh','zjhm','sj','lxdz','dqjf','kyjf','jfyxrq','zjlx2','zhcprq','cjrq','ywy');

        if(empty($info[0])){
            $this->error=" memberFindAll 未找到";
            return false;
        }
      //  print_r($info);
        //格式化数组
        foreach($info as $key=>$val){
            foreach($val as $k=>$v){
                if($key==0) continue;
                $v && $arr[$k][$keyName[$key]]=$v;
                if($key==5 || $key==6){
                    $v && $arr[$k]['xm']=$v;
                }
                unset($arr[$k][4]);
            }
        }

        //更新保存到数据库
        foreach($arr as $key=>$val){
            //业务员id
            preg_match('/([0-9A-Z]+)/',$val['ywy'],$ywy);
            $val['ywyid']=$ywy[1];
            $arr[$key]['ywyid']=$ywy[1];
            if($is_info){ //详细
                $arr[$key]=$this->memberInfo($val['hyid']);
            }else{
                $rs=$this->find($val['hyid']);
                if($rs){
                    $save = $this->create($val);
                    $this->save($save);
                }else{
                    $this->create($val);
                    $this->add();
                    //  echo ($this->getDbError());
                }
            }
        }
        return $arr;
    }

    //会员详情
    function memberInfo($hyid){
        if(empty($hyid)) return false;
        if(is_array($hyid)){
            $hyid=$hyid['hyid'];
        }
        $rs=$this->find($hyid);
        $arr=$this->getMemberInfo($hyid);
    //    print_r($arr);
        if(is_array($arr)){
            if($rs){
                $save= $this->create($arr);
                $this->save($save);
            }else{
                $this->create($arr);
                $this->add();
            }
        }
       return $arr;
    }

    /**
     * 查找用户
     * @param $shyzcm 会员注册名
     * @param string $shykh   卡号
     * @param string $ssj  手机
     * @return update  更新
     */
    function memberFind($shyzcm,$shykh='',$ssj='',$update=1){
        $where=array();
        $shyzcm && $where['hyzcm']=$shyzcm;
        $shykh && $where['hykh']=$shykh;
        $ssj && $where['sj']=$ssj;
        if(empty($where)) return false;
        $dbRs=$this->where($where)->find();
        if($dbRs){
            if($update){// 不更新
                if($dbRs['update_time']>(time()-10)){
                    return $dbRs;
                }
            }else{
                return $dbRs;
            }
        }
        $shykh && $where['shykh']=$shykh;
        $ssj && $where['ssj']=$ssj;
        $rs= $this->memberFindAll($where,1);
        if($rs){
            return $rs[0];
        }else{
            return false;
        }
    }


    /**
     * @param $name 会员注册名重复验证
     * @return bool|string
     */
    function checkName($name){
        if($rs=$this->memberFind($name,'','',0)){
            $this->error="会员名已存在";//会员注册名
            return $rs['wyid'];
        }elseif($rs=$this->memberFind('',$name,'',0)){
            $this->error="会员号已存在";//会员会员卡号
            return $rs['wyid'];
        }elseif($rs=$this->memberFind('','',$name,0)){
            $this->error="手机号已存在";//手机号已存在
            return $rs['wyid'];
        }else{
            return true;
        }
    }

    /**
     * 检测手机号
     * @param $name
     * @return bool
     */
    function checkPhone($hone){
        if($rs=$this->memberFind('','',$hone,0)){
            $this->error="手机号已存在";//手机号已存在
            return $rs['wyid'];
        }else{
            return true;
        }
    }


    /**卡号检查
     * @param $kh
     * @return bool
     * return true 可用
     */
    function checkhykhformember($kh){
        $this->check_login();
        $rs=curl_post(C('ASMS_HOST')."/asms/member/checkhykhformember.shtml?hykh=$kh",'',COOKIE_FILE);
        if($rs){
            $this->error=$rs;
            return false;
        }else{
            $this->error='卡号不存在';
            return true;
        }
    }

    /**用户检查
     * @param $name
     * @return bool 成功 并存在 返回id
     * 成功 可用返回true
     */
    function checkMember($name){
        if(is_array($name)){
            $names=$name;
            $name= http_build_query($name);
            $errorN="号码";
        }else{
            $names=$name="hyzcm='$name'";
            $errorN="用户名";
        }

        $dbRs=$this->where($names)->find();
        if($dbRs){
            $this->error="$errorN 已存在";
            return $dbRs['hyid'];
        }
        $this->check_login();
        $rs=curl_post(C('ASMS_HOST')."/asms/member/checkmember.shtml?$name",$name,COOKIE_FILE);
        if($rs){
            $json=json_decode($rs,true);
            $rs=$json['rows'][0];
            if($rs['HYID']){
                $arr['hyid']=$rs['HYID'];
                $arr['hyzcm']=$rs['HYZCM'];
                $arr['hykh']=$rs['HYKH'];
                if(!$dbRs){
                    $save = $this->create($arr);
                    $this->save($save);
                }else{
                    $this->create($arr);
                    $this->add();
                }
                $this->error=" $name 已存在";
                return $rs['HYID'];
            }else{
                $this->error="$name 未找到";
                return true;
            }
        }
        return false;
    }


    /**会员注册
     * @param $data （array） hyzcm、sj、ywyid 、xm  为必须
     * @return bool|mixed
     */
    function register($data){
        if(!C('ASMS_ONLINE')) return false;
        $this->check_login();
        if(!is_array($data)){
            $this->error="参数类似不正确";
            return false;
        }
        if(!isset($data['hyzcm']) || !isset($data['sj']) || !isset($data['ywyid']) || !isset($data['xm'])){
            $this->error="参数不正确";
            return false;
        }
        if(!isset($data['hykh'])) $data['hykh']=$data['hyzcm'];
        if($rs=$this->checkMember($data['hyzcm'])!==true){
            return false;
        };

        if($data['hykh']!=$data['hyzcm']){
            if($rs=$this->checkhykhformember($data['hykh'])!==true){
                return false;
            };
        }

        if($rs=$this->checkMember(array('sj'=>$data['sj']))!==true){
            return false;
        };

        $arr_post=Array(
        'action'=>'create',
        'hyid'=>'',
        'checksj'=>0,
        'hyzcm'=>$data['hyzcm'],
        'mm'=>isset($data['password'])?$data['password']:'123456',
        'xm'=>$data['xm'],
        'wt'=>'',
        'da'=>'',
        'hyzk'=>'',
        'csrq'=>'',
        'xb'=>'M',
        'sshy'=>'NI',
        'zjhm'=>'',
        'hykh'=>$data['hykh'],
        'zjlx2'=>'P',
        'zjhm2'=>'',
        'zjyxq2'=>'',
        'hyly'=>2,
        'ywymc'=>'',
        'ywyid'=>$data['ywyid'],
        'by01'=>'',
        'bzbz1'=>'',
        'bzbz2'=>'',
        'hydj'=>'1007201',
        'bzbz3'=>'',
        'ywmc'=>'',
        'hhkjhrq'=>'',
        'xqah'=>'',
        'zjxy'=>'',
        'zzmmz'=>'',
        'sj'=>'',
        'lxdz'=>'',
        'yzbm'=>'',
        'jtdh'=>'',
        'email'=>'',
        'gzdw'=>'',
        'swzh'=>'',
        'gsdh'=>'',
        'gscz'=>'',
        'szsf'=>'',
        'asas'=>'',
        'asasa'=>'',
        'szsfmc'=>'',
        'szdqmc'=>'',
        'szdq'=>'',
        'www'=>'',
        'gsdz'=>'',
        'zj_khbh'=>'',
        'jthmc'=>'',
        'jth'=>'',
        'tsyq'=>'',
        'bzbz'=>'',
        'helpdx'=>'on',
        'khyh'=>'',
        'yhzh'=>'',
        'khlx'=>'Z',
        'dqjf'=>0,
        'ljxfe'=>0,
        'kyjf'=>0,
        'xyed'=>0,
        'ifxlkh'=>1,
        'ecjsfl'=>0,
        'ecjsflag'=>0,
        'jsfs'=>0,
        'fkfs'=>1006301,
        'sfxykmz'=>0,
        'kmye'=>0,
        'xxsfgk'=>1,
        'hylx'=>'P',
        'cz'=>'',
     //   'jc'=>$data['hyzcm'],
        'lxr'=>'',
        'frdb'=>'',
        'yyzz'=>'',
        'isvalid'=>1,
        'compid'=>'GZML',
        'deptid'=>'GZMLDZSW',
        'userid'=>C('ASMS_ACCOUNT'),
        'cjrq'=>date('Y-m-d H:i:s'),
        'sh'=>1,
        'zt'=>1,
        'dlcj'=>0,
        'zhdlsj'=>'',
        'dlip'=>'',
        'yzbm2'=>'',
        'dlid'=>'',
        'by02'=>1,
        'by03'=>'',
        'byvalue1'=>0,
        'byvalue2'=>0,
        'dlwz'=>'',
        'xguserid'=>'',
    );
        $post = array_merge($arr_post,$data);
     //   $post_arr['helpdx']=1; //短信提醒
     //   print_r($post_arr);
        $array=array(
            'HTTP_ACCEPT:image/gif, image/bmp, image/x-xbitmap, image/jpeg, image/pjpeg, application/x-shockwave-flash, application/vnd.ms-excel, application/vnd.ms-powerpoint, application/msword, */*',
            'Referer:http://121.8.201.163:8150/asms/member/t_member_add.shtml?action=create&hylx=P&compid=GZML',
            'Origin:http://121.8.201.163:8150',
            'Host:121.8.201.163:8150',
        );

         curl_post(C('ASMS_HOST').'/asms/member/t_member_save.shtml',$post,COOKIE_FILE);
      //  print_r($rs);
        if($rs=$this->checkMember($data['hyzcm'])!==false){
           //注册成功
            return $rs;
        }else{
            $this->error=$this->error."注册失败";
            return false;
        }
    }

    /*
     * 编辑修改信息
     */
    function memberEdit($data){
        if(is_array($data) && isset($data['hyid'])){
            $memberInfo=$this->memberInfo($data['hyid']);
            $arr_post=array_merge($memberInfo,$data);
            $arr_post['action']='edit';
         //   print_r($arr_post);
            $rs=curl_post(C('ASMS_HOST').'/asms/member/t_member_save.shtml',$arr_post,COOKIE_FILE);
            if($rs)
            return true;
        }else{
            $this->error="参数不正确";
            return false;
        }
    }
		
}

?>