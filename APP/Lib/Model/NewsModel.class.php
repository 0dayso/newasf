<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pengfei
 * Date: 13-7-18
 * Time: 上午1:27
 * To change this template use File | Settings | File Templates.
 */
class NewsModel extends RelationModel {
    protected $_link = array(
        'Category'=> array(
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'Category',
            'foreign_key'=>'cid',
        //    'parent_key'=>'cid',
            'mapping_fields'=>'cid,name',
            // 定义更多的关联属性 relation(true)
        ),
        'User'=> array(
            'mapping_type'=>BELONGS_TO,
            'class_name'=>'User',
            'foreign_key'=>'aid',
            'mapping_fields'=>'id,name',
            // 定义更多的关联属性 relation(true)
        )

    );

    protected $_auto = array (
        array('create_time','time',1,'function'),
        array('update_time','time',2,'function'),
        array('user_id','getUid',1,'function'),
        array('update_uid','getUid',2,'function'),
    );

    public function listNews($firstRow = 0, $listRows = 20) {
        $M = M("news");
        $list = $M->field("`id`,`title`,`status`,`published`,`cid`,`user_id`")->order("`published` DESC")->limit("$firstRow , $listRows")->select();
        $statusArr = array("审核状态", "已发布状态");
        $aidArr = M("Admin")->field("`aid`,`email`,`nickname`")->select();
        foreach ($aidArr as $k => $v) {
            $aids[$v['user_id']] = $v;
        }
        unset($aidArr);
        $cidArr = M("Category")->field("`cid`,`name`")->select();
        foreach ($cidArr as $k => $v) {
            $cids[$v['cid']] = $v;
        }
        unset($cidArr);
        foreach ($list as $k => $v){
            $list[$k]['aidName'] =$aids[$v['user_id']]['nickname'] == '' ? $aids[$v['user_id']]['email'] : $aids[$v['user_id']]['nickname'];
            $list[$k]['status'] = $statusArr[$v['status']];
            $list[$k]['cidName'] = $cids[$v['cid']]['name'];
        }
        return $list;
    }


    public function category() {
        if ($_POST) {
            $act = $_POST[act];
            $data = $_POST['data'];
            $data['name'] = addslashes($data['name']);
            $M = M("Category");
            if ($act == "add") { //添加分类
                unset($data[cid]);
                if ($M->where($data)->count() == 0) {
                    return ($M->add($data)) ? array('status' => 1, 'info' => '分类 ' . $data['name'] . ' 已经成功添加到系统中', 'url' => U('News/category', array('time' => time()))) : array('status' => 0, 'info' => '分类 ' . $data['name'] . ' 添加失败');
                } else {
                    return array('status' => 0, 'info' => '系统中已经存在分类' . $data['name']);
                }
            } else if ($act == "edit") { //修改分类
                if (empty($data['name'])) {
                    unset($data['name']);
                }
                if ($data['pid'] == $data['cid']) {
                    unset($data['pid']);
                }
                return ($M->save($data)) ? array('status' => 1, 'info' => '分类 ' . $data['name'] . ' 已经成功更新', 'url' => U('News/category', array('time' => time()))) : array('status' => 0, 'info' => '分类 ' . $data['name'] . ' 更新失败');
            } else if ($act == "del") { //删除分类
                unset($data['pid'], $data['name']);
                return ($M->where($data)->delete()) ? array('status' => 1, 'info' => '分类 ' . $data['name'] . ' 已经成功删除', 'url' => U('News/category', array('time' => time()))) : array('status' => 0, 'info' => '分类 ' . $data['name'] . ' 删除失败');
            }
        } else {
            import('@.ORG.Category');
            $cat = new Category('Category', array('cid', 'pid', 'name', 'fullname'));
            return $cat->getList();               //获取分类结构
        }
    }

    public function addNews() {
        $M = M("News");
        $data = $_POST['info'];
        $data['published']=strtotime($data['published']);
        $data['aid'] = $_SESSION['uid'];
        if (empty($data['description'])) {
            $data['description'] = cutStr($data['content'], 200);
        }
        $M->create($data);
        if ($M->add()) {
            return array('status' => 1, 'info' => "已经发布", 'url' => U('News/index'));
        } else {
            return array('status' => 0, 'info' => "发布失败，请刷新页面尝试操作");
        }
    }

    public function edit() {
        $M = M("News");
        $data = $_POST['info'];
        $data['update_time'] = time();

        $data['published']=strtotime($data['published']);

        if ($M->save($data)) {
            return array('status' => 1, 'info' => "已经更新", 'url' => U('News/index'));
        } else {
            return array('status' => 0, 'info' => "更新失败，请刷新页面尝试操作");
        }
    }

    //新闻列表
    function getList($cid,$limit,$push=''){
        $category=M('category');
        $info=$category->find($cid);

        $w['alias']=I('alias');
        $tree=list_to_tree($category->select(),$cid);
        if(!$info){
            foreach($tree as $k=>$v){
                $info=$v;
            }
        }

        function getChildStr($tree,$str=''){
            if(is_array($tree)){
                foreach($tree as $k=>$v){
                    $str .=$v['cid'].',';
                    if(isset($v['_child']))
                        $str .=getChildStr($v['_child']);
                }
                return rtrim($str,',');
            }
        }
        $str_id=getChildStr($tree);

        $str_id=$str_id?$str_id.','.$cid:$cid;
        $where['cid']=array('in',"$str_id");
        import('ORG.Util.Page');// 导入分页类
        $count      = $this->where($where)->count();// 查询满足要求的总记录数
        $Page       = new Page($count,$limit);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        $push=$push?"is_push desc,":'';
        $list=$this->field('content',true)->where($where)->order("$push is_top desc,published desc")->limit($Page->firstRow.','.$Page->listRows)->select();
        $data['page']=$show;
        $data['list']=$list;
        $data['info']=$info;
        return $data;
    }

    function info(){
        $info=$this->find(I('id'));
        $info['published']=date("Y-m-d H:i:s",$info['published']);
        return $info;
    }

}
