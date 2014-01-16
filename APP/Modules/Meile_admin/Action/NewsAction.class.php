<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-9-2
 * Time: 下午5:16
 * To change this template use File | Settings | File Templates.
 */
//需求模型
class NewsAction extends CommonAction{
    function _before_index(){
        $this->relation=true;
    }

    function category(){
          unset($_POST);
          $this->assign("list", D("News")->category());
          $this->display();
    }


    function insert(){
        $rs=D("News")->category();
        if($rs['status']==1){
            $this->success($rs['info']);
        }else{
            $this->error($rs['info']);
        }
    }

    function addCategory(){
            $this->assign("list", D("News")->category());
            $this->display();
    }

    function editCategory(){
        if(IS_POST){
            $rs=D("News")->category();
            if($rs['status']==1){
                $this->success($rs['info']);
            }else{
                $this->error($rs['info']);
            }
        }
        $this->assign("list", D("News")->category());
        $this->actionName='Category';
        $this->display='addCategory';
        $this->edit();
    }

    function category_foreverdelete(){
           $this->actionName='Category';
           $this->foreverdelete();
    }

    function add(){
        if (IS_POST) {
            $rs=D("News")->addNews();
            if($rs['status']==1){
                $this->success($rs['info']);
            }else{
                $this->error($rs['info']);
            }
        } else {
            $this->assign("list", D("News")->category());
            $this->display();
        }
    }

    function _before_edit(){
        $this->assign("list", D("News")->category());
    }

    function _before_update(){
        $_POST['published']=strtotime($_POST['published']);
    }
}