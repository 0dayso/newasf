<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-8-12
 * Time: 下午6:05
 * To change this template use File | Settings | File Templates.
 */

class ModulesAction extends  IniAction {
    function index(){
        $this->display();
    }

    function message(){

    }

    //评价管理
    function pl(){
       $evaluat=D('Evaluat');
        if($_GET['act']=='edit'){
            if(IS_POST){
                echo  json_encode($evaluat->edit());
                exit;
            }
            $this->info=$evaluat->getInfo(I('id'));
            $this->display('pl_edit');
        }elseif(I('act')=='del'){
            if($evaluat->delete(I('id'))){
                $this->success('成功');
            }else{
                $this->error('删除失败');
            }
        }else{
            $this->title="评论管理";
            import("ORG.Util.Page");       //载入分页类
            $where['status']=1;
            if(I('where')){
                foreach(I('where') as $k=>$v){
                    if($v!='')
                        $where[$k]=$v;
                }
            }

            if(I('search')!=''){
                $search=I('search');
                if($rs=D('User')->getUserId($search)){
                   $or= "OR ( user_id in ($rs))";
                }
                $where['_string'] = "(name like '%{$search}%')  OR ( from_city like '%{$search}%') OR ( from_city like '%{$search}%') OR ( contents like '%{$search}%') $or";
            }
            $count = $evaluat->where($where)->count();
            $page = new Page($count, 20,'','',1);
            $showPage = $page->show();
            $this->assign("page", $showPage);
            $this->list=$evaluat->getList($where,"$page->firstRow,$page->listRows");

            if(IS_AJAX){
                $datalis['list']=$this->list;
                $datalis['page']=$showPage;
                $this->ajaxReturn($datalis);exit;
            }
            $this->display();
        }
    }


    function cheap(){
        if($_GET['act']){
            if($_GET['act']=='edit'){
                if(IS_POST){
                    echo  json_encode(D('Cheap')->edit());
                    exit;
                }
                $this->info=M('Cheap')->find(I('id'));
                $this->display('cheap_edit');
            }elseif(I('act')=='delete'){
                D('Cheap')->delete();
            }elseif(I('act')=='del_img'){
                $this->ajaxReturn( D('Cheap')->delImg());exit;
            }
        }else{
            $this->title="特价机票";
            $data=D('Cheap')->getAdminList();
            $this->zhou=$data['zhou'];
            $this->list=$data['list'];
            if(IS_AJAX){
                $this->ajaxReturn($this->list);exit;
            }
            $this->display();
        }
    }
}