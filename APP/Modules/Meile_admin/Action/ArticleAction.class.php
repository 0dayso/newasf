<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: luxingzhan
// +----------------------------------------------------------------------
// $Id: ArticleAction.class.php 1 2011-11-17 02:14:12Z luofei614@126.com $

class ArticleAction extends CommonAction{

	public function index(){
		$this->cate=C('NEWS_CATE');
		parent::index();
	}

	public function add(){
		$node=M("Node")->select();
		$nodeData=list_to_tree($node);
		$this->node=$nodeData;

		$groupId=$_GET['groupId'];
		$Access=M("Access")->where(array('role_id'=>"{$groupId}"))->select();

		$array=array();
		foreach($Access as $val){
			$array[$val['level']][]=$val['node_id'];
		}
		$this->selectdNode=$array;
		$this->display();
	}

	public function search(){
		$this->display();
	}
}


