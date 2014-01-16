<?php
/**
 * asms 业务员用户操作
 */
require_once "asmsBase.class.php";
class user extends asmsBase{

    /**获取asms 业务员 信息
     * @param string $bh
     * @param string $xm
     * @param int $ygzt
     * @return mixed
     */
    function get_user($bh='',$xm='',$ygzt=1){
        $post_arr=array();
        $post_arr['bh']=$bh;
        $post_arr['xm']=$xm;
        $post_arr['ygzt']=$ygzt;
        $data=curl_post(C('ASMS_HOST').'/asms/sysmanage/yhb_right.shtml?scompid=GZML&count=2000',$post_arr,COOKIE_FILE);

        $preg="/class=\"czimg\">\s+<\/td>\s+<td>(.*?)<\/td>\s+<td.*?>(.*?)<\/td>\s+<td>(.*?)<\/td>\s+<td>(.*?)<\/td>\s+<td>(.*?)<\/td>\s+<td>(.*?)<\/td>\s+<td>(.*?)<\/td>\s+<td>(.*?)<\/td>\s+<td>(.*?)<\/td>\s+<td>(.*?)<\/td>\s+<td>(.*?)<\/td>\s+<td>(.*?)<\/td>\s+<td>(.*?)<\/td>\s+<td>(.*?)<\/td><\/tr>/Ui";

        preg_match_all($preg,$data,$info);
        if(empty($info)) return false; //没有数据

        $keyName=array(1=>'ywyid',2=>'name',3=>'status',4=>'tel',5=>'phone',6=>'qxjb',7=>'yhlx',8=>'fjzt',9=>'wscx',10=>'email',11=>'address',12=>'department',13=>'company',14=>'create_date');

         foreach($info as $key=>$val){
             foreach($val as $k=>$v){
                 if($key==0) continue;
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
        return $arr;
    }

    /**
     * 全新数据插入
     */
    function insert_all(){
        $rs=$this->get_user('','','');
        $sql='';
        foreach($rs as $key=>$val){
            $sql.="('".implode("','",$val)."',".time()."),";
        }
        $sql=rtrim($sql,',');
        $query="insert into {$this->db_prefix}user values $sql";
      //  echo $query;
        mysql_query("TRUNCATE TABLE  {$this->db_prefix}user");
        $rs=mysql_query($query);
        if(!$rs) $this->setError(mysql_errno());
        return $rs;
    }

    /**更新数据
     * @param string $bh
     * @param string $xm
     */
    function update($bh='',$xm=''){
        $rs=$this->get_user($bh,$xm);
        $ywyidArr=array();
        $srs=mysql_query("select ywyid from {$this->db_prefix}user");
        while($row=mysql_fetch_assoc($srs)){
            $ywyidArr[]=$row['ywyid'];
        }
        foreach($rs as $key=>$val){
            if(in_array($val['ywyid'],$ywyidArr)){
                //更新
                $kk='';
                foreach($val as $k=>$v){
                    $kk.="`$k`='$v',";
                }
                $update_sql="update {$this->db_prefix}user set $kk update_time=".time()." where ywyid='$val[ywyid]';";
                mysql_query($update_sql);
            }else{
                //插入
                $insert_sql="insert into {$this->db_prefix}user values('".implode("','",$val)."',".time().");";
                echo $insert_sql;
                mysql_query($insert_sql);
            }
        }
        $this->setError(mysql_errno());
    }


}

?>