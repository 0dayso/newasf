<?php
class AilehuiAction extends IniAction {
    public function index(){
		$this->title='爱乐汇';
        $this->display();
    }

    function zqgq(){
        $this->title='';
        $this->display();
    }

}
