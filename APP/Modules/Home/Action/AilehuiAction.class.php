<?php
class AilehuiAction extends IniAction {
    public function index(){
		$this->title='爱乐汇';
        $this->display();
    }

    function myprivilege(){
        $this->title='';
        $this->display();
    }

}
