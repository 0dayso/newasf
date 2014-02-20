<?php
class ToolsAction extends IniAction {
    public function index(){
		$this->title="机票工具箱";
		$this->display();
    }

    function AirportService(){
        $this->title='机场服务';
        $this->display();
    }
    function InternationalAirport(){
        $this->title='国际机场内容页';
        $this->display();
	}
	function iAirportContent(){
        $this->title='国际机场内容页';
        $this->display();
    }
	function ServiceContent(){
        $this->title='机场服务内容';
        $this->display();
    }
	

}
