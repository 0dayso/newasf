<?php

class NewsAction extends Action{

    Public function index(){
		$this->title="新闻";
        $_GET['alias']=0;
        R('News/lists');
		$this->display('content');
    }
	
	Public function company(){	
		$this->title="公司新闻";
        $rs=D('News')->getlist(33,20);
        $this->page=$rs['page'];
        $this->list=$rs['list'];
		$this->display();
    }

    Public function Lists(){
        $alias=I('alias');
        if(is_numeric($alias)){
            $cid=$alias;
        }else{
            $where['alias']=$alias;
            $cid=D('Category')->where($where)->getField('cid');
            if(!$cid) R('Empty/_empty');
        }
        $rs=D('News')->getlist($cid,20);
        $this->info=$rs['info'];
        $this->page=$rs['page'];
        $this->list=$rs['list'];
        import('@.ORG.Category');
        $cat = new Category('category', array('cid','pid','name'));
        $this->path=$cat->getPath($cid);

        $this->title=$this->info['name'];
        $this->display('lists');
    }


    //新闻内容
    function content(){
        $this->info=D('News')->info();
        $this->title=$this->info['title'];
        import('@.ORG.Category');
        $cat = new Category('category', array('cid','pid','name'));
        $this->path=$cat->getPath($this->info['cid']);
        $this->display();
    }


}