<?php
// 评论模块
class PlAction extends CommonAction {
	function index(){
        if(I('so')){
            $where['name'] = array('like',"%".I('so')."%");
            $where['from_city']  = array('like',"%".I('so')."%");
            $where['to_city']  = array('like',"%".I('so')."%");
             $where['_logic'] = 'or';
             $map['_complex'] = $where;
        }
        $map['id'] = array('egt',2);
        $this->map=$map;
        $this->relation=true;
        parent::index(D('Evaluat'));
        $this->display();

	}
    function _before_read(){
        $this->actionName='Evaluat';
    }
    function _before_edit(){
        $this->actionName='Evaluat';
    }

    function _before_update(){
        $this->actionName='Evaluat';
    }

   public function insert(){
       $model = D('Evaluat');
       //保存当前数据对象
       $list=$model->add($_POST);
       if ($list!==false){ //保存成功
           $this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
           $this->success ('新增成功!');
       }else{
           //失败提示
           $this->error ('新增失败!'. $model->getDbError());
       }
    }
}
?>