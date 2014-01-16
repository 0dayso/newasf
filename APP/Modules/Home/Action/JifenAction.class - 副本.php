<?php
// 积分商城控制器
class JifenAction extends IniAction {
    public function index(){
        $this->title="积分商城";
        $mall=D('Mall');
        $category=M('mall_category')->order('sort desc')->select();
        $clist=list_to_tree($category);
        $this->clist=$clist;
        foreach($clist as $k=>$v){
            if($v['_child']){
              $str= implode(",",array_keys($v['_child']));
              $str =$str.",".$k."<br>";
            }else{
              $str=$v['cid'];
            }
            $where['cid']=array("in","$str");
            $where['status']=1;
            $listarr[$k]=$mall->where($where)->order('id desc')->limit(8)->select();
        }
        $this->list=$listarr;
		$this->display();
    }

	 function category(){
        $cid=I('cid');
        $mall=D('Mall');
         import('@.ORG.Category');
         $cat = new Category('mall_category', array('cid','pid','name'));
         $this->path=$cat->getPath($cid);

         $category=M('mall_category')->select();

         $clist=list_to_tree($category,$cid);
         $this->clist=$clist;
         $str = implode(",",array_keys($clist));
         $str=$str.",".$cid;
         $where['cid']=array("in","$str");
         $where['status']=1;
         $listarr=$mall->where($where)->select();
         $this->list=$listarr;
         $title='';
         foreach($this->path as $v){
             $title.=$v['name']." - " ;
         }
         $this->title="$title 积分商城(分类)";
         $this->display();
    }
	
	function lists(){
         $this->display();
     }

	
	function info(){
        $id=I('id');
        $mall=D('Mall');
        $info=$mall->find($id);
        if($info['status']==0 || empty($info)){
            $this->error("该商品不存在或已下架");
        }
        $this->info=$info;
        import('@.ORG.Category');
        $cat = new Category('mall_category', array('cid','pid','name'));
        $this->path=$cat->getPath($info['cid']);
        $this->title="$info[title] - 积分商城";
        $this->display();
<<<<<<< .mine
    }	
=======
    }
>>>>>>> .r5104
	
}