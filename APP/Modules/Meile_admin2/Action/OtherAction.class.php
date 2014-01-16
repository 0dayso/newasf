<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-7-23
 * Time: 下午3:33
 * To change this template use File | Settings | File Templates.
 */
class OtherAction extends IniAction{
    function index(){
        $this->complaint();
    }


    function complaint(){
        $complaint=D("Complaint");
        if(I('id')){
            $this->info=$complaint->getInfo();
            $this->display("complaint_info");
            exit;
        }
        import("ORG.Util.Page");       //载入分页类

        $where='';
        if(I('where')){
            foreach(I('where') as $k=>$v){
                if($v!=''){
                    $where[$k]=$v;
                }
            }
        }
        if(I('search')!=''){
            $search=I('search');
            $where['_string'] = "(order_id like '%{$search}%')  OR ( email like '%{$search}%') OR ( cell like '%{$search}%') OR ( price_detail like '%{$search}%') ";
        }

        $count = $complaint->where($where)->count();
        $page = new Page($count, 20,'','',1);
        $showPage = $page->show();
        $this->assign("page", $showPage);

        $list=$complaint->getList($where,"$page->firstRow,$page->listRows");
        $this->list=$list;

        if(IS_AJAX){
            $data['list']=$list;
            $data['page']=$showPage;
            $this->ajaxReturn($data);exit;
        }
        $this->title="订单管理";
        $this->display('complaint');

    }

}