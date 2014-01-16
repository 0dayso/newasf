<?php
// 积分商城控制器-新版
class JifenAction extends IniAction {
    public function index(){
		 $mall=D('Mall');
		//-----------
		//积分热兑专区	
		$where['status']=1;
		$where['type']=0;
		$this->jifen_re=$mall->field('id,title,img,jifen')->where($where)->order("sales DESC")->limit(7)->select();		
		//爱钻热兑专区
		$where['status']=1;
		$where['type']=1;
		$this->aizuan_re=$mall->field('id,title,img,jifen')->where($where)->order("sales DESC")->limit(7)->select();	
		
		//-----------
		//积分好礼推荐	
		$where['status']=1;
		$where['type']=0;
		$where['tj']=1;
		$this->jifen_tj=$mall->field('id,title,img,jifen')->where($where)->order("create_time DESC")->limit(6)->select();
		//爱钻兑好礼推荐
		$where['status']=1;
		$where['type']=1;
		$where['tj']=1;
		$this->aizuan_tj=$mall->field('id,title,img,jifen')->where($where)->order("create_time DESC")->limit(6)->select();	
        $this->title="积fen商城";
		$this->display();
    }
	
	function lists(){	
	    $mall=D('Mall');
		$cid=I('cid');
		$category=D('mall_category');
		$res=$category->find($cid);		
        if($res['status']==0 || empty($res)){
            $this->error("你搜索的页面不存在");
        }
		
        $clist1=list_to_tree($this->category,$cid);			
			
			
		if(empty($clist1)){
			 $this->one=1;
		 	 $where['cid']=$cid;
			 $where['status']=1;	 
			 $clist1=$mall->where($where)->limit(8)->select();			
		}else{
			foreach($clist1 as $key=>$val){	
			    $where=array();
				if($val['_child']){
					 $str = implode(",",array_keys($val['_child']));					 
					 $str=$str.",".$val['_child'];
				}else{
					$str=$val['cid'];
				}
				$where['cid']=array("in","$str");
				$where['status']=1;	 
				$clist1[$key]['list']=$mall->where($where)->limit(8)->select();			
			}
		}
		$this->assign('clist1',$clist1);
	
		
		 $title='';
         foreach($this->path as $v){			 	
             $this->title=$v['name'];
			 $title .= $v['name'].',';
			$this->arr=explode(',',$title);
         }
		 
		// print_r($this->arr);
		 
		$this->title="积fen商城";
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
    }
	
}
