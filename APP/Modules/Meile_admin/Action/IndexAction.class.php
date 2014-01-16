<?php
class IndexAction extends CommonAction {
	
	// 框架首页
	public function index() {

		if (isset ( $_SESSION [C ( 'USER_AUTH_KEY' )] )) {
			//luz start
			$vo=M("GroupClass")->where(array('status'=>1))->order("sort desc")->select();
            foreach($vo as $k=>$v){
                $volist[$v['id']]=$v;
            }
         //   $volistuser=M("GroupClassUser")->where("uid=".getUid())->select();
         //   print_r($vo);
		//	$this->volist=$volist;
			//luz end
			//显示菜单项
			$menu = array ();
			
			//读取数据库模块列表生成菜单项
			$node = M ( "Node" );
			$id = $node->getField ( "id" );
	//		$where ['level'] = 2;
			$where ['status'] = 1;
	//		$where ['pid'] = $id;
			$list = $node->where ( $where )->field ( 'id,name,group_id,pid,title,level' )->order ( 'sort asc' )->select ();
            if(C('USER_AUTH_TYPE')==2) {
                import('@.ORG.Util.RBAC');
                //加强验证和即时验证模式 更加安全 后台权限修改可以即时生效
                $accessList = RBAC::getAccessList($_SESSION[C('USER_AUTH_KEY')]);
            }else{
                $accessList = $_SESSION['_ACCESS_LIST'];
            }
            foreach($list as $k=>$v){
                $lists[$v['id']]=$v;
            }
            $this->assign('list',$lists);
         //   print_r($lists);

            foreach($lists as $key=>$module){
                if($module['level']==2){
                    if(isset($accessList[strtoupper(GROUP_NAME)][strtoupper($module['name'])]) || $_SESSION['administrator']){
                        //设置模块访问权限
                        $module['access'] = 1;
                        $menu[$module['group_id']][$key]  = $module;
                    }
                }elseif($module['level']==3){
                //    print_R($module);;
                    if(isset($accessList[strtoupper(GROUP_NAME)][strtoupper($lists[$module['pid']]['name'])][strtoupper($module['name'])]) || $_SESSION['administrator']) {
                        //设置模块访问权限
                        $module['access'] =  1;
                        $menu[$module['group_id']][$key]  = $module;
                    }
                }
            }
         // print_r($menu);
			
			if (! empty ( $_GET ['tag'] )) {
				$this->assign ( 'menuTag', $_GET ['tag'] );
			}

			//luz start
			$group=M("Group")->where(array('status'=>"1"))->order("sort desc,id desc")->select();
         //  print_r($group);
            foreach($group as $k=>$v){
                $groups[$v['id']]=$v;
            }

            foreach($menu as $k=>$v){
                if($groups[$k]['group_menu']){
                    foreach($volist as $lk=>$lv){
                        if($lv['menu']==$groups[$k]['group_menu']){
                            $menus[$lk]=$lv;
                        }
                    }
                }
            }

            foreach($volist as $k=>$v){
                    if($menus[$k]){
                    $menuss[$k]= $menus[$k];
                    }
            }

          //  print_r($menuss);
            $this->volist=$_SESSION[C('ADMIN_AUTH_KEY')]?$volist:$menuss;
            $groups=M("Group")->where(array('group_menu'=>"{$vo[0]['menu']}",'status'=>"1"))->order("sort desc,id desc")->select();

			$this->assign("groups",$groups);
			//luz end

			$this->assign ( 'menu', $menu );
		}
	//	C ( 'SHOW_RUN_TIME', false ); // 运行时间显示
	//	C ( 'SHOW_PAGE_TRACE', false );
		$this->display ('Index/index');
	}

    function main(){
       // dump($this->userInfo);
    }

}
?>