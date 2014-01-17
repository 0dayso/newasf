<?php
/** "{{user}}"表对应的模型类.
 *
 * '{{user}}'表中的列字段:
 * @property integer $id
* @property string $username
* @property string $password
* @property string $salt
* @property string $name
* @property integer $sex
* @property string $qq
* @property string $msn
* @property string $skype
* @property string $email
* @property string $telephone
* @property string $public_mobile
* @property string $private_mobile
* @property string $signature
* @property string $profile
* @property string $avatar
* @property string $last_login_time
* @property string $last_login_ip
* @property integer $login_count
* @property string $create_time
* @property integer $create_user_id
* @property integer $company_id
* @property integer $status
* @property integer $good_review
* @property integer $ordinary_review
* @property integer $bad_review
 * */

class UserModel extends RelationModel{
	private $userid;
	private $username;
	private $avatar;
    protected $_link = array(
        'position'=> array(
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'position',
            'foreign_key'=>'position_id',
            'mapping_fields'=>'id,name',
            // 定义更多的关联属性 relation(true)
        ),
        //department
        'department'=> array(
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'department',
            'foreign_key'=>'department_id',
            'mapping_fields'=>'id,name',
            // 定义更多的关联属性 relation(true)
        ),
        //company_id
        'company'=> array(
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'company',
            'foreign_key'=>'company_id',
            'mapping_fields'=>'id,name',
            // 定义更多的关联属性 relation(true)
        ),
        //asms_user   asms 同步
        'asms_user'=> array(
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'asms_user',
            'foreign_key'=>'asms_user_id',
        //    'condition'=>'wscx',
            'mapping_fields'=>'ywyid,name,status',
            // 定义更多的关联属性 relation(true)
        ),
    );
//	protected $serializeField = array(
//          'info' => array('name', 'email', 'address'),
//    );

	protected $_validate = array(
		array('verify','require','验证码必须！'), //默认情况下用正则进行验证
		array('username','','帐号名称已经存在！',0,'unique',3), // 在新增的时候验证name字段是否唯一
    //    array('name','','名称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
		array('repassword','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
	 );
    protected $_auto = array (
        array('create_time','time',1,'function'),
        array('create_user_id','getUid',1,'function'),
        array('update_user_id','getUid',2,'function'),
    );

    //会员详细信息
	function userInfo($userid){
		return $this->relation(true)->find($userid);
	}

    //获得用户id  支持模糊查找 返回多个id  1，2，3
    function getUserId($name){
        if(!is_array($name))
            $where['name']=array('like',"%$name%");
        $rs=$this->field('id')->where($where)->select();
        if($rs){
            $arr=array();
            foreach($rs as $v){
                $arr[]=$v['id'];
            }
            return implode(',',$arr);
        }
        return false;


    }

    //获得某个用户salt
	function getSalt($username){
        return $this->where("id='$username' or username='$username'")->getField('salt');
    }

    //验证客服
    function checkKf($name){
        if(is_numeric($name)){
            $where['id']=$name;
            $field='name';
        }else{
            $where['name']=$name;
            $field='id';
        }
        $where['view']=1;  //
        $where['status']=1;
        $rs=$this->where($where)->getField($field);
       if($rs)
           return $rs;
        return false;

    }

    //分配客服 轮询分配 返回id
    function assignUserid($mid=''){
        $member_db=D('Member');
        if(I('id') && $this->checkKf(I('id'))){
               $user_id=I('id');
        }else{
            $PREFIX=C('DB_PREFIX');//表前缀
            $_GET['company']=isset($_GET['company'])?$_GET['company']:'1';//默认分配公司
            $company=I('company')?" and {$PREFIX}user.company_id=".I('company')." ":'';
            $department=I('department')?"and  {$PREFIX}user.department_id=".I('department')." ":'';//部门
            $map="$company $department ";//组合
         //   echo $map;
            $sql="SELECT {$PREFIX}member.auto_user FROM {$PREFIX}member  LEFT JOIN {$PREFIX}user on {$PREFIX}member.auto_user={$PREFIX}user.id where {$PREFIX}member.auto_user!=0 $map ORDER BY {$PREFIX}member.create_time desc limit 1";
            $mrs= $member_db->query($sql);//得出符合条件的最后分配的 user_id

            $auto_user=isset($mrs[0]['auto_user'])?$mrs[0]['auto_user']:0;
            $user_id=$this->where("status=1 and view=1 and id>$auto_user ".$map)->getField('id');
            if(empty($user_id)){
                $user_id=$this->where("status=1 and view=1 ".$map)->order('id')->getField('id');//第一个
            }
         //   echo $user_id;
            $auto=1;

       //     $where="company_id=1 and status=1 and public_mobile!='' and view=1 and avatar!=''";
        //    $sql="select u.id from (select id from {$PREFIX}user where {$where} order by id) as u where u.id>(select param_value from {$PREFIX}config where param_name='assignUserid') limit 1";
        //    $arr=M()->query($sql);
         //   if($arr[0]['id']){
         //       $user_id=$arr[0]['id'];
         //   }else{
         //       $user_id=$this->where($where)->order('id')->getField('id');//第一个
          //  }
        }
        $data['user_id']=$user_id;
        $wheres['id']=$mid?$mid:getUid();
        if($auto){ $data['auto_user']=$user_id;}
        $rs=$member_db->where($wheres)->save($data);  //设置会员对印客服
        if($rs){
            return $user_id;
        }
        return false;
    }

    //随机分配客服
    function AutoUserid(){
         return  $this->where("company_id=1 and status=1 and view=1 and avatar!=''")->order('rand()')->getField('id');
    }

    //用户权限级别条件
    function userLevelWhere($uid){
        $uid=$uid?$uid:getUid();
        $role_level=$this->userInfo['role_level'];
        if($role_level==0){
            $map['user_id']=$uid;
        }elseif($role_level==1){
            $user_id_arr = D('User')->field('id')->where("department_id=".$this->userInfo['department_id'])->select();
            $map['user_id'] = array('in',$user_id_arr);
        }
    }

	 
	
		
}

?>