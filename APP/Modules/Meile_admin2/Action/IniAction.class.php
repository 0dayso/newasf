<?php
class IniAction extends PublicAction{
	private $userinfo;
    public function _initialize(){
     //   $this->initAuth();
        if(!session('uid')) $this->error('你还没有登陆',U('/meile_admin2/login'));
        $this->assign("menu", $this->show_menu());
        $this->assign("sub_menu", $this->show_sub_menu());

	}

    protected function initAuth(){
        Import ( 'ORG.Util.RBAC' );
        if (!RBAC::AccessDecision())//未通过认证
        {
        // 登录检查
        RBAC::checkLogin();
        // 提示错误信息 无权限
        $this->error ( L ( '_VALID_ACCESS_' ) );
        }
    }

    /**
    +----------------------------------------------------------
     * 显示一级菜单
    +----------------------------------------------------------
     */
    private function show_menu() {
        $cache = C('admin_big_menu');
        $count = count($cache);
        $i = 1;
        $menu = "";
        foreach ($cache as $url => $name) {
            if ($i == 1) {
                $css = $url == MODULE_NAME || !$cache[MODULE_NAME] ? "fisrt_current" : "fisrt";
                $menu.='<li class="' . $css . '"><span><a href="' . U($url . '/index') . '">' . $name . '</a></span></li>';
            } else if ($i == $count) {
                $css = $url == MODULE_NAME ? "end_current" : "end";
                $menu.='<li class="' . $css . '"><span><a href="' . U($url . '/index') . '">' . $name . '</a></span></li>';
            } else {
                $css = $url == MODULE_NAME ? "current" : "";
                $menu.='<li class="' . $css . '"><span><a href="' . U($url . '/index') . '">' . $name . '</a></span></li>';
            }
            $i++;
        }
        return $menu;
    }

    /**
    +----------------------------------------------------------
     * 显示二级菜单
    +----------------------------------------------------------
     */
    private function show_sub_menu() {
        $big = MODULE_NAME == "Index" ? "Common" : MODULE_NAME;
        $cache = C('admin_sub_menu');
        $sub_menu = array();
        if ($cache[$big]) {
            $cache = $cache[$big];
            foreach ($cache as $url => $title) {
                $url = $big == "Common" ? $url : "$big/$url";
                $sub_menu[] = array('url' => U("$url"), 'title' => $title);
            }
            return $sub_menu;
        } else {
            return $sub_menu[] = array('url' => '#', 'title' => "该菜单组不存在");
        }
    }
}	
?>