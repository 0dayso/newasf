<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-9-2
 * Time: 下午5:16
 * To change this template use File | Settings | File Templates.
 */
//需求模型
class RequireOrderAction extends CommonAction{
    //需求单列表
    function _before_index(){
        $this->relation=true;
        //    $this->map=array('status'=>1);
        if(!I('_order')){
            $this->order="id desc";
        }
        $this->index(D('RequireOrder'));
        $list= D('RequireOrder')->format($this->list);
        $this->list=$list;
        $this->display();
        exit;
    }

    //查看、详情
    function viewInfo(){
        $where['id']=I('get.id');
        $data= D('RequireOrder')->getInfo($where);
        $this->vo=$data;
        $this->display();
    }

    //我的需求单
    function myRequireOrder(){
        $this->relation=true;
        $maps['user_id']=getUid();
        if(I('my_id')=='2'){
             unset($maps['user_id']);
            if(!I('_order')){
                $this->order="id desc";
            }
            $maps=D('RequireOrder')->getMyWhere();
        }else{
            if(!I('_order')){
                $this->order="update_time desc,id desc";
            }
        }

        $this->map=$maps;

        if(I('so')){
            unset($maps['user_id']);
            $where['name']  = array('like',"%".I('so')."%");
            $where['phone']  = array('like',"%".I('so')."%");
            $where['_logic'] = 'or';
            $map['_complex']=$where;
            $this->map = $map;
        }

        $RequireOrder=D('RequireOrder');
        $this->index($RequireOrder);

        $last_time=D('RequireOrder')->where('user_id='.getUid())->order('update_time desc')->getField('update_time');
        $list= $RequireOrder->format($this->list);
        if(($last_time+60)<time()){
            $this->new=$RequireOrder->ajaxList(1);
        }
        $this->list=$list;

     //   dump($this->new);
        $this->uid=getUid();
        $this->display();
    }

    //查看我的需求单详情
    function myViewInfo(){
        $where['user_id']=getUid();
        $where['id']=I('id');
        $RequireOrder= D('RequireOrder');
        $data=$RequireOrder->getInfo($where);
        //   print_r($data);
        if(!$data){
            $this->error('你没有权限');
        }
        $RequireOrder->id = I('id');
        $RequireOrder->status =1; // 修改数据对象
        $RequireOrder->save(); // 保存当前数据对象

        $this->vo=$data;
        $this->display('viewInfo');
    }

     //获得最新的 需求单
    function getNew(){
        if(IS_AJAX){
            $last_time=D('RequireOrder')->where('user_id='.getUid())->order('update_time desc')->getField('update_time');
            if(($last_time+60)<time()){ //60秒后才能再次认领
                $new=D('RequireOrder')->ajaxList(1);
                if($new){
                    $data['info']=$new;
                    $data['status']=1;
                    $this->ajaxReturn($data);
                }else{
                    $this->error('暂无新的须求信息');
                }
            }
        }
    }

    //认领需求单
    function getNews(){
        if(!I('get.id'))  return $this->error('获取失败');
        $RequireOrder=D('RequireOrder');
        $rs=$RequireOrder->find(I('get.id'));

        if(empty($rs))  return $this->error('没有记录');
        if($rs['user_id']>1){
            $this->error('链接已失效');
        }
        $rss=$RequireOrder->getNews($rs['phone']);
        if($rss){
            $this->success('获取成功 '.$rss."条记录");
        }else{
            $this->error('获取失败 或已认领'.$RequireOrder->getDbError());
        }

    }

}