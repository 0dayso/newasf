<?php
// 后台用户模块
class MessageAction extends CommonAction {
    public function index(){
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->title = '消息模板';
        $this->order="id desc,name";
        parent::index(D('MessageTpl'));
        $this->display();
    }

    /*
      * 禁用 恢复
      */
    function resume(){
        $this->actionName="MessageTpl";
        parent::resume();
    }

    /*
   * 禁用 恢复
   */
    function forbid(){
        $this->actionName="MessageTpl";
        parent::forbid();
    }

    /*
     * add添加
     */
    function tplAdd(){
        $this->display('tplAdd');
    }

    /*
     * edit编辑
     */
    function tplEdit(){
        $this->actionName="MessageTpl";
        parent::edit();
    }

    function insert(){
        $this->actionName="MessageTpl";
        parent::insert();
    }

    function update(){
        $this->actionName="MessageTpl";
        parent::update();
    }

    function foreverdelete(){
        $this->actionName="MessageTpl";
        parent::foreverdelete();
    }

    /*
     *站内信
     */
    function mesList(){
        $this->title = '站内信';
        $this->order="id desc";
        parent::index(D('Message'));
        $this->display();
    }

    /*
     *站内信
     */
    function mesEdit(){
        $this->actionName="Message";
        parent::edit();
    }


    /*
   * 禁用 恢复
   */
    function mesResume(){
        $this->actionName="Message";
        parent::resume();
    }

    /*
   * 禁用 恢复
   */
    function mesForbid(){
        $this->actionName="Message";
        parent::forbid();
    }
}
?>