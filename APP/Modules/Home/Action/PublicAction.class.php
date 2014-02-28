<?php
class PublicAction extends IniAction{
    //顶部菜单
	function topMenu(){
        $content = $this->fetch();
        header("Content-type: text/javascript; charset=utf-8");
        echo htmltojs($content);
	}

    //设值客服
    function setKf(){
        $content = $this->fetch();
        header("Content-type: text/javascript; charset=utf-8");
        echo htmltojs($content);
    }

    //底部js
    function footerJs(){
        $content = $this->fetch();
        header("Content-type: text/javascript; charset=utf-8");
        echo htmltojs($content);
    }


}
?>