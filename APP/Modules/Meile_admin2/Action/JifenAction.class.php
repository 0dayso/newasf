<?php
class JifenAction extends IniAction{
    public function index() {
        $M = M("mall");
        import("ORG.Util.Page");       //载入分页类
        $this->assign("category", D("Mall")->category());
        $where="";
        if(I('where')){
            foreach(I('where') as $k=>$v){
                if($v!=''){
                    $where[$k]=$v;
                }
            }
        }
        if(I('search')!=''){
            $search=I('search');
            $where['_string'] = "(title like '%{$search}%')  OR ( description like '%{$search}%') OR ( content like '%{$search}%') OR ( jifen like '%{$search}%') OR (sales like '%{$search}%')";
        }
        $count = $M->where($where)->count();
        $page = new Page($count, 20,'','',1);
        $showPage = $page->show();
        $this->assign("page", $showPage);
        $list=D("Mall")->listNews($page->firstRow, $page->listRows,$where);
        $this->assign("list",$list);

        if(I('ss')==1){
            $datalis['list']=$list;
            $datalis['page']=$showPage;
            $this->ajaxReturn($datalis);exit;
        }

        $this->display();


    }

    public function category() {
        if (IS_POST) {
            echo json_encode(D("Mall")->category());
        } else {

            $this->assign("list", D("Mall")->category());
            $this->display();
        }
    }

    public function add() {
        if (IS_POST) {
            echo json_encode(D("Mall")->addNews());
        } else {
            if(IS_AJAX){
                import('@.ORG.Category');
                $cat = new Category('mall_category', array('cid','pid','name'));
                $category= $cat->getList('status=1',I('cid'),'sort desc');
                $this->success($category);
            }

            $this->assign("list", D("Mall")->category());
            $this->display();
        }
    }

    //检查标题
    public function checkNewsTitle() {
        $M = M("mall");
        $where = "title='" . $this->_get('title') . "'";
        if (!empty($_GET['id'])) {
            $where.=" And id !=" . (int) $_GET['id'];
        }
        if ($M->where($where)->count() > 0) {
            echo json_encode(array("status" => 0, "info" => "已经存在，请修改标题"));
        } else {
            echo json_encode(array("status" => 1, "info" => "可以使用"));
        }
    }

    public function edit(){
        $M = M("mall");
        if (IS_POST) {
            echo json_encode(D("Mall")->edit());
        } else {
            if(IS_AJAX){
                import('@.ORG.Category');
                $cat = new Category('mall_category', array('cid','pid','name'));
                $category= $cat->getList('status=1',I('cid'),'sort desc');
                $this->success($category);
            }
            $info = $M->where("id=" . (int) $_GET['id'])->find();
            if ($info['id'] == '') {
                $this->error("不存在该记录");
            }
            $this->assign("info", $info);
            $this->assign("list", D("Mall")->category());
            $this->display("add");
        }
    }

    public function del() {
        if (M("mall")->where("id=" . (int) $_GET['id'])->delete()) {
            $this->success("成功删除");
//            echo json_encode(array("status"=>1,"info"=>""));
        } else {
            $this->error("删除失败，可能是不存在该ID的记录");
        }
    }
}