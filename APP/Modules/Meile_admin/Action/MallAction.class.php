<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-9-2
 * Time: 下午5:16
 * To change this template use File | Settings | File Templates.
 */
//需求模型
class MallAction extends CommonAction{
    function _before_index(){
        $this->relation=true;
    }

    function category(){
          unset($_POST);
          $this->assign("list", D("Mall")->category());
          $this->display();
    }


    function insert(){
        $rs=D("Mall")->category();
        if($rs['status']==1){
            $this->success($rs['info']);
        }else{
            $this->error($rs['info']);
        }
    }

    function addCategory(){
            $this->assign("list", D("Mall")->category());
            $this->display();
    }

    function editCategory(){
        if(IS_POST){
            $rs=D("Mall")->category();
            if($rs['status']==1){
                $this->success($rs['info']);
            }else{
                $this->error($rs['info']);
            }
        }
        $this->assign("list", D("Mall")->category());
        $this->actionName='MallCategory';
        $this->display='addCategory';
        $this->edit();
    }

    function category_foreverdelete(){
           $this->actionName='MallCategory';
           $this->foreverdelete();
    }

    function add(){
        if (IS_POST) {
            $rs=D("Mall")->addNews();
            if($rs['status']==1){
                $this->success($rs['info']);
            }else{
                $this->error($rs['info']);
            }
        } else {
            $this->assign("list", D("Mall")->category());
            $this->display();
        }
    }

    function _before_edit(){
        $this->assign("list", D("Mall")->category());
    }

    function _before_update(){
        $_POST['published']=strtotime($_POST['published']);
    }

    /*
     * 兑换列表
     */
    function exchange(){
        $this->relation=true;
        parent::index(D('MallExchange'));
     //   $this->list= D('MallExchange')->format($this->list);
        $this->display();
    }


    function exchangeView(){
        $MallExchange= D('MallExchange');
        $rs=$MallExchange->relation(true)->find(I('id'));
        $rs['info']=object_to_array(json_decode($rs['info']));
        $this->vo= $MallExchange->format($rs);
     // dump($this->vo);
        $this->display();
    }

    function exchangeEdit(){
        if($_POST){
            $data['status']=I('status');
            $data['update_uid']=getUid();
            $data['remark']=I('remark');
            $rs= D('MallExchange')->save($data);
            $rs && $this->success('成功');
            $this->error('失败');
        }else{
            $MallExchange= D('MallExchange');
            $rs=$MallExchange->relation(true)->find(I('id'));
            $rs['info']=object_to_array(json_decode($rs['info']));
            $this->vo= $MallExchange->format($rs);
            $this->display();
        }

    }
}