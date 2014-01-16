<?php

class ActionsAction extends CommonAction{

	public function index(){
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);

        $this->meta_title = '用户行为';
        $this->order="id desc";
        parent::index(D('Action'));
        $this->display();
	}

    /*
     * 禁用 恢复
     */
    function resume(){
        $this->actionName="Action";
        parent::resume();
    }

    /*
     * add添加
     */
    function add(){
        $this->display('add');
    }

    /*
     * edit编辑
     */
    function edit(){
        $this->actionName="Action";
        parent::edit();
    }
    function insert(){
        $this->actionName="Action";
        parent::insert();
    }
    function update(){
        $this->actionName="Action";
        parent::update();
    }

    function foreverdelete(){
        $this->actionName="Action";
        parent::foreverdelete();
    }

   function actionLog(){
        $this->meta_title = '用户行为';

        $this->order="id desc";
        parent::index(D('ActionLog'));
        $this->display();
    }

}


