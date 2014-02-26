<?php
//asms会员模型
class AsmsOrderModel extends RelationModel{
    protected $_link = array(
        'member'=> array( //关联会员表
        'mapping_type'=>HAS_ONE ,
        'class_name'=>'member',
        'foreign_key'=>'asms_member_id',
        'condition'=>'status=1',
        'mapping_fields'=>'id,username,name,status',
        // 定义更多的关联属性 relation(true)
        ),
    );

    //类型
    Public  $lx=array('','单程','往返','联程','缺口','本地始发','异地到异地','异地到本地');
    //状态
    Public  $status=array('申请中','已订座','已调度','已出票','配送中','部分出票','','客户消','已取消','完成');

    //cjrlx
    Public $cjrlx=array('','成人','儿童','婴儿');

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


    //Order 获取订单信息
    function getOrderInfo($ddbh,$update=0){
        if(empty($ddbh)) return false;
        if(is_array($ddbh)){
            $ddbh=$ddbh['ddbh'];
        }
        $orderRs=$this->find($ddbh);
        if(!C('ASMS_ONLINE')) return $orderRs; //没连asms 直接返回缓存数据
        //缓存更新
        if(!$update && isset($orderRs['info_update_time']) && $orderRs['info_update_time']>(time()-C('CACHE_TIME'))){
            return $orderRs;
        }

        $this->check_login();
        $url= C('ASMS_HOST')."/asms/ydzx/ddgl/kh_khdd_xq.shtml?ddbh=".$ddbh;
     //   echo $url;
        $data=curl_post($url,'',COOKIE_FILE,0);
        if(!$data){return -1;}
        $preg="/<div class=\"nav_junior_con\".*?>(.*?)<script.*?<tr id=\"hctr0\">(.*?)<\/table>.*?<tbody id=\"tb\">(.*?)<\/tbody>/si";

        preg_match($preg,$data,$info);

        if(empty($info[0])){
            $this->error="GETORDERINFO 未找到";
            return false;
        }
        $data1=$info[1];
        $data2=$info[2];
        $data3=$info[3];//从html页面上取需要的
    //    print_r($data1);

        $preg1="/<input .*?name=\"(.*?)\" .*?value=\"(.*?)\".*?>/";
        preg_match_all($preg1,$data1,$info1);

        foreach($info1 as $key=>$val){
            foreach($val as $k=>$v){
                if($key==0) continue;
                if($key==1)
                    $kk[$k]=$v;
                if($key==2)
                    $arr[$kk[$k]]=$v;
            }
        }
        $arr['hyid']=$arr['ct_hyid'];
        $preg="/<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<input .*?value=\"(.*?)\"\/>.*?<input .*?value=\"(.*?)\"\/>.*?<input .*?value=\"(.*?)\"\/>.*?<input .*?value=\"(.*?)\"\/>.*?<input .*?value=\"(.*?)\"\/>.*?<input .*?value=\"(.*?)\"\/>.*?<input .*?value=\"(.*?)\"\/>.*?<input .*?value=\"(.*?)\"\/>.*?<input .*?value=\"(.*?)\"\/>.*?<input .*?value=\"(.*?)\"\/>.*?<input .*?value=\"(.*?)\"\/>.*?<input .*?value=\"(.*?)\"\/>.*?<input .*?value=\"(.*?)\"\/>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>/si";

        preg_match_all($preg,$data2,$info2);
      //  print_R($info2);
        $preg2="/<input .*?name=\"(.*?)\" .*?value=\"(.*?)\".*?>/";
        preg_match_all($preg2,$data3,$info3);
        $hdArr=array('','hbh','hc','hd_id','hd_hbh','hd_cfcity','hd_ddcity','hd_cfsj','hd_cfsj_p','hd_bzbz','hd_bzbz_p','hd_cw','hd_fjjx','hd_hzl','hd_cityname','hd_ddcityname','cfsj','ddsj');
        foreach($info2 as $key=>$val){
            foreach($val as $k=>$v){
                if($key==0) continue;
                $arr1[$k][$hdArr[$key]]=$v;
            }
        }

     //   print_r($arr1);
     //   exit;
        $index=0;
        foreach($info3 as $key=>$val){
            foreach($val as $k=>$v){
                if($key==0) continue;
                if($v=='cjr_index'){
                    $index=$info3[2][$k];
                }
                if($key==1){
                   $kk[$k]=$v;
                   $arr2[$index][$v]=$info3[2][$k];
                }
            }
        }
        unset($data);
        $data=$arr;
        $data['ddbh']=$ddbh;
        $data['info_update_time']=time();
        $data['hd_info']=json_encode($arr1);
        $data['cjr_info']=json_encode($arr2);
     //   $rs=$this->find($ddbh);
        if($orderRs){ //存在则保存
            $save=$this->create($data);
            $this->save($save);
        }else{
            $this->create($data);
            $this->add();
        }
    //    echo ($this->getDbError());
        $arr['hd']=$arr1;
        $arr['cjr']=$arr2;
        $rs=$this->find($ddbh);
        $rs['hd_info']=$arr1;
        $rs['cjr_info']=$arr2;
        return $rs;
    }

    /*
     * 订单查找
     * data
     * cjr  乘机人
     * nxdh 联系电话
     * pnr  P N R
     * userid 订票员
     * shc  航  程
     * tkno 票号
     * hbh 航班号
     * ddzt 订单状态
     * zf_fkf 付款状态
     * zkfx 客户类型
     *
     */
    function orderFindAll($data,$is_info=0){
        if(is_array($data)){
            $arr_post=$data;
        }else{
            $this->error='第一参数只能是数组';
            return false;
        }
        $hyid=$data['khid'];
        $page_r=I('numPerPage')?I('numPerPage'):100;
        $page_p=I('pageNum')?I('pageNum'):1;
        $start=$page_r*$page_p-$page_r;
        $page_start=$start;

        $arr_post['ksrq']=isset($data['ksrq'])?$data['ksrq']:"2013-09-01"; //开始日期
        $arr_post['jsrq']=isset($data['jsrq'])?$data['jsrq']:date("Y-m-d",time()); //结束日期
        $arr_post['old_ssddlx']=1;
        $arr_post['ssddlx']=1;
        $arr_post['checkdate']=2; //预定日期
        $arr_post['pnr_hcglgj']=isset($data['pnr_hcglgj'])?$data['pnr_hcglgj']:0; //国际
        $this->check_login();
        $url=C('ASMS_HOST')."/asms/ydzx/ddgl/kh_khdd_ddgl.shtml?cs=5&count=$page_r&start=$page_start&";

        $str= http_build_query($arr_post);
     //   echo $url.$str;
        $data=curl_post($url,$str,COOKIE_FILE);
        if(!$data){return -1;}
        if(empty($data)){
            $this->error="连接失败";
            return false;
        }

        $data_preg="/<form name=\"batchForm\" .*?>(.*?)<\/form>/s";
       // print_r($data);
        preg_match($data_preg,$data,$data2);
        //print_r($data[1]);
        //正则匹配数据
        //                                                                                      1  确定出票时间                   2订票员                     预订时间 3               4订单状态              5订单类型          6退改           7 采购状态       8   供应状态                 9 当前营业部                            10   订票营业部                                11  pnr.                              12    PNR 状态  .                     13 大编码          14 客户类型           15 会员卡号 /单位编号       16 客户名称         17 类型          18 航程          19航班号.           20舱 位           21起飞时间          22乘机人                 23人数              24采购价            25  账单价           26留款            27 加价 /让利           28销售价           29机建                30税费            31小计            32保险                            33接车                                34其他                35应收金额               36支付          37已付金额            38支付科目            39 OFFICE            40电子邮件         41可用积分        42旅客联系人        43联系电话             44配送方式 .      45配送时间             46订票公司          47 新PNR             48客户订单号     49 订单编号         50 订单来源
        $preg="/<tr class=.*?>.*?<td>.*?<\/td>\s<td>.*?<\/td>\s<td>.*?delRecord\(\'\d+\',\'\d+\',\'(\d+)\'.*?\).*?<\/td>\s<td>.*?<\/td>\s<td>(.*?)<\/td>\s<td>.*?<span .*?>(.*?)<\/span>.*?<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>.*?<span .*?>(.*?)<\/span>.*?<\/td>\s<td>.*?<span .*?>(.*?)<\/span>.*?<\/td>\s<td.*?>.*?<font .*?>(.*?)<\/font>.*?<\/td>\s<td.*?>.*?<font.*?>(.*?)<\/font>.*?<\/td>\s<td>(.*?)<\/td>\s<td><font.*?>(.*?)<\/font><\/td>\s<td>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td>(.*?)<\/td>\s<td(.*?)<\/td>\s<td>(.*?)<\/td>\s<td>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?><font .*?>(.*?)<\/font><\/td>\s<td.*?><font .*?>(.*?)<\/font><\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td(.*?)<\/td>\s<td(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>\s<td.*?>(.*?)<\/td>.*?<\/tr>/si";

        preg_match_all($preg,$data2[1],$info);
        if(empty($info[0])){
            $this->error=" memberFindAll 未找到";
            return false;
        }

     //   print_r($info);exit;

         $pregs="/<table .*?class=\"turnPlan\">.*?<tr>.*?<b>(\d+)<\/b>.*?<input .*?value=\'(\d+)\'>.*?<\/table>/si";
          preg_match($pregs,$data2[1],$infos);
        if(!empty($infos[0])){
            $_GET['totalCount']=$infos[1];
            $_GET['pageNum']=$infos[2];
            $_GET['numPerPage']=$page_r;
            $_GET['totalPages']=Ceil($infos[1]/$page_r);
        }
        //设置 key值
        $kayName=array('','version','cpsj','userid','dprq','ddzt','ddlx','gt','cgzt','gyzt','yyb','dpyyb','pnr','pnr_zt','hkgs_pnr','zkfx','hykh','xm','lx','hc','hbh','cw','qfsj','cjr','rs','cgj','zdj','lk','jjrl','xsj','jj','sf','xj','bx','jc','qt','ysje','zf_fkf','yf','zf_fkkm','office','email','kyjf','nklxr','lxdh','ps_lx','tyqsj','dpgs','xpnr','khddh','ddbh','ddly');        //  print_r($info);


        //格式化数组
        foreach($info as $key=>$val){
            foreach($val as $k=>$v){
                if($key==0) continue;
                if(in_array($key,array('19','23','43','44'))){
                    if(strstr($v,'title')){
                        preg_match("/title=\"(.*?)\"/",$v,$vf);
                        $v=$vf[1];
                    }else{
                        $v=strip_tags($v);
                        $v=str_replace('>','',$v);
                    }
                }
                if($key==7){
                    $v='';
                }
                if($key==2 || $key==5 || $key==7){
                    $v=strip_tags($v); //去html
                }
                if($key==5){ //订单状态
                   foreach($this->status as $kk=>$kv){
                       if($v==$kv){ $v=$kk;break;}
                   }
                }
                if($key==18){//类型
                    foreach($this->lx as $kk=>$vv){
                        if($vv==$v){
                            $v=$kk;
                            break;
                        }
                    }
                }
                if($key==37){
                    $v=$v=='未付'?0:1; //付款类型
                }
                $arr[$k][$kayName[$key]]=$v;
            }
        }

     //   print_R($kayName);
      //  print_r($arr);
        //更新保存到数据库
        foreach($arr as $key=>$val){
            if($is_info){ //详细
                $info=$this->getOrderInfo($val['ddbh']);
                $arr[$key]['hd_info']=$info['hd_info'];
                $arr[$key]['cjr_info']=$info['cjr_info'];
            }else{
                $rs=$this->where("ddbh=".$val['ddbh'])->select();
                $val['hyid']=$hyid;
                if($rs){
                    $save= $this->create($val);
                    $this->save($save);
                }else{
                    $this->create($val);
                    $this->add();
                }
            }
        }
        return $arr;
    }


    /**
     * 查找会员订单
     * @param $hyid
     * @param string $hykh
     * @param int $update
     * @return bool|int
     */
    function orderFind($hyid,$update=0){
        $where=array();
        if(is_array($hyid)){
            $where=$hyid;
        //    $hyid=$hyid['hyid'];
        }else{
            $hyid && $where['hyid']=$hyid;
        }

        if(empty($where)){
            $this->error="参数输入有误";
            return false;
        };
        $dbRs=$this->where($where)->select();
        if(!C('ASMS_ONLINE')) return $dbRs; //没连asms 直接返回缓存数据
        if($dbRs){
            if(!$update){
                if($dbRs[0]['update_time']>(time()-C('CACHE_TIME'))){// 缓存更新
                    return $dbRs;
                }
            }
        }
        $where='';
        if(is_array($hyid)){
            $where['khid']=$hyid['hyid'];
            $where['khmc']=$hyid['hykh'];
            $where['pnr']=$hyid['pnr'];
            $where['tkno']=$hyid['tkno'];
        }else{
            $hyid && $where['khid']=$hyid;
        }
        if(empty($where))  return false;
        $rs= $this->orderFindAll($where);
        if($rs){
            return $rs;
        }else{
            return false;
        }
    }

    /*
     * 订单编辑
     */
    function editOrder($newData=array()){
        if(!isset($newData['ddbh'])) return false;
        $this->check_login();
        $url= C('ASMS_HOST')."/asms/ydzx/ddgl/kh_khdd_xq.shtml?ddbh=".$newData['ddbh'];
        $data=curl_post($url,'',COOKIE_FILE,0);
        if(!$data){return -1;}

        $preg="/<div class=\"nav_junior_con\".*?>(.*?)<script.*?<tr id=\"hctr0\">(.*?)<\/table>.*?<tbody id=\"tb\">(.*?)<\/tbody>(.*?)<\/body>/si";

        preg_match($preg,$data,$info);

        if(empty($info[0])){
            $this->error="GETORDERINFO 未找到";
            return false;
        }
        $data1=$info[1];
        $data2=$info[2];
        $data3=$info[3];//从html页面上取需要的
        $data4=$info[4];
        //   print_r($data4);

        $preg1="/<input .*?name=\"(.*?)\" .*?value=\"(.*?)\".*?>/";
        preg_match_all($preg1,$data1,$info1);
        preg_match_all($preg1,$data4,$info4);
        foreach($info1 as $key=>$val){
            foreach($val as $k=>$v){
                if($key==0) continue;
                if($key==1)
                    $kk[$k]=$v;
                if($key==2)
                    $arr[$kk[$k]]=$v;
            }
        }
        foreach($info4 as $key=>$val){
            foreach($val as $k=>$v){
                if($key==0) continue;
                if($key==1)
                    $kkk[$k]=$v;
                if($key==2)
                    $arr3[$kkk[$k]]=$v;
            }
        }

        $post_str='';
        $post_str .="sfqrtx=&ssfbm=1&ds_memo=&";
        $arr=array_merge($arr,$arr3);
        $newData['ddbz']=$newData['ddbz']?$arr3['ddbz']." | ".$newData['ddbz']:$arr3['ddbz'];
        $arr=array_merge($arr,$newData);
        $post_str.= http_build_query($arr)."&";
      //  $arr['hyid']=$arr['ct_hyid'];

        if(isset($newData['hd_info'])){
            $preg="/<td.*?>.*?<\/td>.*?<td.*?>.*?<\/td>.*?<input .*?value=\"(.*?)\"\/>.*?<input .*?value=\"(.*?)\"\/>.*?<input .*?value=\"(.*?)\"\/>.*?<input .*?value=\"(.*?)\"\/>.*?<input .*?value=\"(.*?)\"\/>.*?<input .*?value=\"(.*?)\"\/>.*?<input .*?value=\"(.*?)\"\/>.*?<input .*?value=\"(.*?)\"\/>.*?<input .*?value=\"(.*?)\"\/>.*?<input .*?value=\"(.*?)\"\/>.*?<input .*?value=\"(.*?)\"\/>.*?<input .*?value=\"(.*?)\"\/>.*?<input .*?value=\"(.*?)\"\/>.*?<td.*?>.*?<\/td>.*?<td.*?>.*?<\/td>.*?<\/tr>/si";

            preg_match_all($preg,$data2,$info2);

            $hdArr=array('','hd_id','hd_hbh','hd_cfcity','hd_ddcity','hd_cfsj','hd_cfsj_p','hd_bzbz','hd_bzbz_p','hd_cw','hd_fjjx','hd_hzl','hd_cityname','hd_ddcityname','cfsj','ddsj');
            foreach($info2 as $key=>$val){
                foreach($val as $k=>$v){
                    if($key==0) continue;
                    $arr1[$k][$hdArr[$key]]=$v;
                }
            }
            $arr1=array_merge_recursive($arr1,$newData['hd_info']);
            foreach($arr1 as $val){
                $post_str .= http_build_query($val)."&";
            }
        }

        if(isset($newData['cjr_info'])){
            preg_match_all($preg1,$data3,$info3);
            $index=0;
        //    dump($newData['cjr_info']);
            foreach($info3 as $key=>$val){
                foreach($val as $k=>$v){
                    if($key==0) continue;
                    if($v=='cjr_index'){
                        $index=$info3[2][$k];
                    }
                    if($key==1){
                        $kk[$k]=$v;
                        $arr2[$index][$v]=$info3[2][$k];
                    }
                }
            }

            $arr2=array_merge_multi($arr2,$newData['cjr_info']);
        //    print_r($arr2);//exit;
            foreach($arr2 as $val){
                $post_str .= http_build_query($val)."&";
            }
        }

      //  print_r($post_str);
      //  $post_str="sfqrtx=0&ds_memo=&p=ddXqSave&openlx=0&ddxgtype=ddxq&ddbh=6565656565651402212&ddzt=1&pnr_hkgstldatetime=2014-02-23+1645&zkfx=0&ps_pszt=0&longrangegaveiick=0&pnr_adultno=2&ps_yqrqsj=2014-02-24+1634-1734&xg_userid=6000&ct_hyid=131106162247096671&pnr_cfrqsj=2014-03-16+12%3A15&zcfw=0&version=42&zf_fkf=0&sjjlfs=0&xjjlfs=0&sfbm=0&tss_yn=0&sfmp=0&sfzsp=0&sfsgd=1&ddlx=1&zcid=&ifxf=1&ptzcbs_zc=0&czfrom=ASMS%E8%AE%A2%E5%8D%95%E8%AF%A6&ps_compid_dd=&ps_deptid_dd=&ddtzbz=&ds_compid_dd=GZML&ds_deptid_dd=GZMLDZSW&dp_compmc=%E5%B9%BF%E5%B7%9E%E7%BE%8E%E4%B9%90&dp_deptmc=%E7%94%B5%E5%AD%90%E5%95%86%E5%8A%A1%E9%83%A8&dp_dpyid=6000&jsfl=0&bxjsj=0&ddlx_dd=0&ddtzfs=0&ifxf_dd=1&refun=&xglyflag=1&hyxgflag=0&gjpjgs=0&wjlpjsfjs=1&cs2830=0&hd_id=140222121430691000000000100416&hd_hbh=CZ392&hd_cfcity=DAC&hd_ddcity=CAN&hd_cfsj=2014-03-16&hd_cfsj_p=1215&hd_bzbz=2014-03-16&hd_bzbz_p=1735&hd_cw=T&hd_fjjx=738&hd_hzl=6&hd_cityname=%E8%BE%BE%E5%8D%A1&hd_ddcityname=%E5%B9%BF%E5%B7%9E&mblx=%2Fasms%2Fydzx%2Fddgl%2Fdetail%2Fkh_khdd_cjrxx_mb_gj_tbody.jsp&cjr_index=1&cjr_cjrid=AAAAAA1402211635495842&cjr_cjrlx=1&cjr_cjrxm=ZHAN%2FYUAN&cjr_clkid=2014022116024964874&cjr_jfhyid=&cjr_cgj=30&cjr_pjsjjsfl=0&cjr_pjsjjsj=50&cjr_hidediscount=0&cjr_khlk=0&cjr_jj=-20&cjr_xsj=10&cjr_jsf=0&cjr_tax=0&cjr_qt2=0&cjr_jec=0&cjr_qt1=0&cjr_bxfs=0&cjr_bxdj=0&cjr_zsbx=0&cjr_bxjl=0&cjr_pjxjjsfl=0&cjr_lwyj=0&cjr_fpj_hj=10&cjr_bxxjjsj=0&cjr_bx=0&cjr_pjxjjsj=10&cjr_returntoinvalidate=0&cjr_pj_mj=0.01&cjr_khfx=0&cjr_hf_hkgs=0&cjr_spa=0&cjr_pj_sjjsfl_xs=0&cjr_zz_hkgs=0&cjr_cplx=&cjr_zjhm=E10495309&cjr_sjhm=18673800250&cjr_gj=%E4%B8%AD%E5%9B%BD&cjr_xb=M&cjr_zjyxq=&cjr_sfmp=0&cjr_sfzsp=0&temp_pjxjjsj=10.00&temp_pjsjjsj=50.00&temp_pjsjjsfl=0.00&temp_cgj=30.00&temp_khlk=0.00&temp_jj=0.00&temp_pj_mj=0.01&temp_returntoinvalidate=0.00&temp_xsj=10.00&temp_hf_hkgs=0.00&temp_spa=0.00&temp_pj_sjjsfl_xs=0.00&cjr_index=2&cjr_cjrid=AAAAAA1402221317356055&cjr_cjrlx=1&cjr_cjrxm=SDS%2FWA&cjr_clkid=2014010816014525795&cjr_jfhyid=&cjr_cgj=30&cjr_pjsjjsfl=0&cjr_hidediscount=0&cjr_khlk=0&cjr_xsj=20&cjr_jsf=0&cjr_tax=0&cjr_qt2=0&cjr_jec=0&cjr_qt1=0&cjr_bxfs=0&cjr_bxdj=0&cjr_zsbx=0&cjr_bxjl=0&cjr_pjxjjsfl=0&cjr_lwyj=0&cjr_fpj_hj=20&cjr_bxxjjsj=0&cjr_bx=0&cjr_pjxjjsj=20&cjr_returntoinvalidate=0&cjr_pj_mj=0.01&cjr_khfx=0&cjr_hf_hkgs=0&cjr_spa=0&cjr_pj_sjjsfl_xs=0&cjr_zz_hkgs=0&cjr_cplx=&cjr_pjsjjsj=30&cjr_jj=-10&cjr_zjhm=425022&cjr_sjhm=&cjr_gj=%E4%B8%AD%E5%9B%BD&cjr_xb=M&cjr_zjyxq=&cjr_sfmp=0&cjr_sfzsp=0&temp_pjxjjsj=10.00&temp_pjsjjsj=50.00&temp_pjsjjsfl=0.00&temp_cgj=30.00&temp_khlk=0.00&temp_jj=0.00&temp_pj_mj=0.01&temp_returntoinvalidate=0.00&temp_xsj=10.00&temp_hf_hkgs=0.00&temp_spa=0.00&temp_pj_sjjsfl_xs=0.00&ct_cpfs=1&cplx=BPET&confirmDate=2014-02-23&confirmTime=1645&bxlx=131101234004998165&zf_fklx=1006433&ct_lxrkh=18673800250&ct_hyxm=yin&cu_ifljjf=0&ct_nxr=yin&ct_nxrdh=18673800250&ct_smsmobilno=18673800250&ct_email=&clyy=&vipxmmc=&xmdh=&vip_jsbmName=&vip_jsbm=&vipusermc=&vip_dp_userid=0&vip_bmdh=&vip_ddbh=&vip_kmm=&vipusermc=&vip_spr=&vip_spgzid=&ps_lx=2&ps_city=96&qpbm=&ct_xcd=1&ct_sjr=&ct_yzbm=&ps_dz=%E8%81%94%E7%B3%BB%E5%9C%B0%E5%9D%80&yqdate=2014-02-24&sj=1634-1734&ct_sjdz=&ps_bz=&gjftnr=&ddbz=ddddss&qtdd_len=1&ywdh=6565656565651402212&ywlx=01&qz_nr=test";

        $url= C('ASMS_HOST')."/asms/ydzx/ddgl/kh_khdd_detail_save.shtml?submitForm=listForm";

       $data=curl_post($url,$post_str,COOKIE_FILE);
      //  print_r($data);
        if(strpos($data,'正在处理')){
            return true;
        }else{
            return false;
        }
    }


    //取消订单
    function orderCancel($ddbh){
        if(!$ddbh) return false;
        $this->check_login();
        $url= C('ASMS_HOST')."/asms/ydzx/ddgl/kh_khdd_cancle.shtml";
        $post['p']='cancleDd';
        $post['ddbh']=$ddbh;
        $post['qxyybh']='';
        $post['qxyynr']='';
        $post['sffc']='';
        $rs=$this->find($ddbh);
        $post['version']=$rs['version'];
        $post= http_build_query($post);
        $data=curl_post($url,$post,COOKIE_FILE);

        if(!$data) {
            return true;
        }
        $this->error=$data;
        return false;
    }

    //取消全部订单
    function orderCancelAll($ddbh){
        if(!$ddbh) return false;
        if(is_array($ddbh)){
            foreach($ddbh as $val){
                $rs=$this->orderCancel($val);
                if(!$rs) return $rs;
            }
        }else{
            $rs= $this->orderCancel($ddbh);
        }
        return $rs;
    }

    /*
     * 订单支付
     * $ddbh  订单编号
     * $hyid  会员id
     * $zf_je  支付金额
     * $pay_id 支付流水
     * $zfkm  支付方式、渠道
     * $xjj 现金劵
     * $bzbz 备注
     */
    function orderPay($ddbh,$hyid,$zf_je,$xjj=0,$pay_id,$zfkm,$bzbz=''){
        if($xjj){
            $order_rs=$this->format($this->find($ddbh));
            $cjr_info= $order_rs['cjr_info'];
            $cjr_xsj=$cjr_info[1]['cjr_xsj']-$xjj;//使用网站 现金劵 $xjj";
        //    dump($cjr_xsj);exit;
            $this->editPrice($ddbh,$cjr_xsj) ;
         }
        $this->getOrderInfo($ddbh,1);
        $zfArr=array(1=>'1006416',2=>'1006417',3=>'1006419'); //支付宝\财富通、易宝
        $zfkm=is_int($zfkm) && isset($zfArr[$zfkm])?$zfArr[$zfkm]:$zfArr[1];
        $this->check_login();
        $url= C('ASMS_HOST')."/asms/ydzx/ddgl/kh_khdd_pay.shtml";
        $post['p']='save';
        $post['hyid']=$hyid;
        $post['ddbh']=$ddbh;
        $post['ddbh_o']=$ddbh;
        $post['zf_je']=$zf_je;
        $post['zf_zfzh']=$pay_id;
     //   $post['fpj_hj']='0.01';
        $post['zfkm']=$zfkm;
        $post['zffs'.$zfkm]='1006309';
        $post['ywType']='1';
        $post['bzbz']=$bzbz;
        $rs=$this->find($ddbh);
        $post['version']=$rs['version'];

        $post['cj_compid']='GZML';
        $post['cj_deptid']='GZMLDZSW';
        $post['cj_userid']='6000';
        $post['cjrname']='DSFS/MS';
        $post['qbook']='';
        $post['operate']='create';
        $post['b2gzh']='';
        $post['zfgsid']='';
        $post['ipsvalue']='';
        $post['epos_zhivr']='';
        $post['hkgs']='AA';
     //   dump($post);
        $post= http_build_query($post);
        $data=curl_post($url,$post,COOKIE_FILE);

        if(strpos($data,'正在处理')){
            $xjj && D('Points')->cutPoints(getUid(),$xjj,"订单:$ddbh 消费",2);
            return true;
        }else{
            return false;
        }
    }

   /*
    * 编辑价格
    */
    function  editPrice($ddbh,$xsj){
        $ors= $this->field('ysje','xjj','cjr_info')->find($ddbh);
        if(!$ors){
            $this->error="订单未找到";
            return false;
        }
        $editData['ddbh']=$ddbh;
        $editData['ddbz']=" 网站编辑价格 $xsj";
        $editData['cjr_info']=array(1=>array('cjr_xsj'=>$xsj));//更改 胜意价格
        $rs=$this->editOrder($editData);
      //  dump($rs);
        return $rs;
    }
    /*
     * 我的订单
     * 适应于会员查看自己的订单
     */
    function myOrder(){
        if(!ASMSUID){
           $this->error="未登陆";
           return false;
        }
        return $this->orderFind(ASMSUID);
    }

    /*
     * 订单删除
     * 用于 删除本地同步出错的
     */
    function orderDel($ddbh){
        if($ddbh==''){
            $this->error="";
            return false;
        }
        return $this->delete($ddbh);
    }



    /*
    * 数据 格式化
    */
    function format($data){
        if(!isset($data[0])){
            $data=array(0=>$data);
            $on=1;
        }
        foreach($data as $key=>$val){
            !is_array($val['hd_info']) &&  $data[$key]['hd_info']=object_to_array(json_decode($val['hd_info']));
            !is_array($val['cjr_info']) && $data[$key]['cjr_info']=object_to_array(json_decode($val['cjr_info']));
            $data[$key]['ddzt_n']=$this->status[$val['ddzt']];
            $data[$key]['lx_n']=$this->lx[$val['lx']];
            $data[$key]['zf_zt']=$val['zf_fkf']==1?"已付款":'待支付';
        }

        if($on){
            return $data[0];
        }
        return $data;
    }

    //订单支列表
    function orderPayList($data){
        $where['member_id']=session('asmsUid');
        foreach($data as $k=>$v){
            $booking[$k]=$this->getOrderInfo($v);
        }

        $where['ddbh']=array("in",$data);
        $where['zf_zt']=0;
        $list=$this->where($where)->select();

        $array='';
        $this->total_price=0;
        foreach($list as $key=>$val){
            $list[$key]['hd_info']=object_to_array(json_decode($val['hd_info']));
            $list[$key]['cjr_info']=object_to_array(json_decode($val['cjr_info']));
            foreach( $list[$key]['cjr_info'] as $k=>$v){
                $list[$key]['cjr_info'][$k]['lx']=$this->cjrlx[$v['cjr_cjrlx']];
            }
            $array['total_price']+=(float)$val['xj'];
        }
        $array['list']=$list;
        return $array;
    }

		
}

?>