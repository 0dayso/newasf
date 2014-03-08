<?php
class CommonAction extends Action {
	public $userInfo;
    Public $map;
    Public $display;
    public $actionName;
	function _initialize(){
        import('@.ORG.Util.Cookie');
		// 用户权限检查
		if (C ( 'USER_AUTH_ON' ) && !in_array(MODULE_NAME,explode(',',C('NOT_AUTH_MODULE')))){
            import('@.ORG.Util.RBAC');
			if (! RBAC::AccessDecision(GROUP_NAME)){
				//检查认证识别号
				if (! $_SESSION [C( 'USER_AUTH_KEY' )]) {
					if ($this->isAjax()){
						$this->ajaxReturn(true, "", 301);
					}else{
						//跳转到认证网关
						redirect ( PHP_FILE . C ( 'USER_AUTH_GATEWAY' ) );
					}
				}

				// 没有权限 抛出错误
				if (C ( 'RBAC_ERROR_PAGE' )) {
					// 定义权限错误页面
					redirect ( C ( 'RBAC_ERROR_PAGE') );
				} else {
					if (C ( 'GUEST_AUTH_ON' )) {
						$this->assign ( 'jumpUrl', PHP_FILE . C ( 'USER_AUTH_GATEWAY' ) );
					}
					// 提示错误信息
					$this->error (L( '_VALID_ACCESS_' ));
				}
			}
		}

        if(getUid()){
            $this->userInfo= D('User')->userInfo(getUid());
            $this->assign('userInfo', $this->userInfo);
        }

        if(I('navTabId')){
            $this->navTabId=I('navTabId');
        }
	}

	//ajax赋值扩展
	protected function ajaxAssign(&$result){
		$result['statusCode']  =  $result['status'];
		$result['navTabId']  =  $_REQUEST['navTabId'];
		$result['message']=$result['info'];
	}


	public function index($name = '') {
        if (empty( $name )){
            $name= isset($this->actionName)?$this->actionName:$this->getActionName();
        }
        if(!is_object($name))
            $model = CM( $name );
        else
            $model = $name;

        //列表过滤器，生成查询Map对象
        $map =$this->map?array_merge($this->_search($model),$this->map):$this->_search($model);
        $this->map=$map;
        $this->maps=base64_encode(serialize($map));

        if (method_exists( $this, '_filter' )) {
            $this->_filter ( $map );
        }


		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
     //   dump($name);
        if(!is_object($name)){
            $this->display ();
        }

		return;
	}
	/**
     +----------------------------------------------------------
	 * 取得操作成功后要返回的URL地址
	 * 默认返回当前模块的默认操作
	 * 可以在action控制器中重载
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return string
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	function getReturnUrl() {
		return __URL__ . '?' . C ( 'VAR_MODULE' ) . '=' . MODULE_NAME . '&' . C ( 'VAR_ACTION' ) . '=' . C ( 'DEFAULT_ACTION' );
	}

	/**
     +----------------------------------------------------------
	 * 根据表单生成查询条件
	 * 进行列表过滤
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @param string $name 数据对象名称
     +----------------------------------------------------------
	 * @return HashMap
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	public function _search($name = '') {
		//生成查询条件
		if (empty( $name )) {
            $name= isset($this->actionName)?$this->actionName:$this->getActionName();
		}
        if(!is_object($name))
            $model = CM( $name );
        else
            $model=$name;

        $dbArray=$model->getdbFields();
        if(!$dbArray && stristr(get_class($model),'ViewModel')){
            $rs=str_replace('ViewModel','',get_class($model));
            if($rs){
                $dbArray=M($rs)->getDbFields();
            }
        }
		$map = array ();
		foreach ( $dbArray as $key => $val ) {
			if (isset ( $_REQUEST [$val] ) && $_REQUEST [$val] != '') {
				$map [$val] = $_REQUEST [$val];
			}
		}
		return $map;

	}

	/**
     +----------------------------------------------------------
	 * 根据表单生成查询条件
	 * 进行列表过滤
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @param Model $model 数据对象
	 * @param HashMap $map 过滤条件
	 * @param string $sortBy 排序
	 * @param boolean $asc 是否正序
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	public function _list($model, $map, $sortBy = '', $asc = true){
    //    print_r($map);
		$pk=$model->getPk ();
		$dbArray=$model->getDbFields();

        //如果是视图模型
        if(!$dbArray && stristr(get_class($model),'ViewModel')){
            $rs=str_replace('ViewModel','',get_class($model));
            if($rs){
                $dbArray=M($rs)->getDbFields();
            }
        }
        $attributeLabels=method_exists($model,'attributeLabels');
        if($attributeLabels){
            $this->attributeLabels=$model->attributeLabels();
        }
		unset($dbArray['_autoinc']);		// _autoinc 表示主键是否自动增长类型
		unset($dbArray['_pk']);			//_pk 表示主键字段名称 
		$order="";

        if(in_array(I("_order"), $dbArray)){
            $order.=I("_order").' '.I("_sort");
        }

        if(!$order && $this->order){
            $order=$this->order;
        }

		//取得满足条件的记录数
		$count = $model->where ($map)->count();
      //  dump($count);
		if ($count > 0) {
			import ( "@.ORG.Util.Page" );
			//创建分页对象
			if (! empty ( $_REQUEST ['listRows'] )){
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $count, $listRows );
			 $pageNum =empty($_REQUEST['numPerPage']) ? C('PAGE_LISTROWS') : $_REQUEST['numPerPage'];
			//分页查询数据
            if($this->relation){
                 $voList = $model->where($map)->relation($this->relation)->order($order)->limit($pageNum)->page($_REQUEST[C('VAR_PAGE')])->select();
            }else{
	    		 $voList = $model->where($map)->order($order)->limit($pageNum)->page($_REQUEST[C('VAR_PAGE')])->select();
            }
        //    echo $order;
			//分页跳转的时候保证查询条件
			foreach ( $map as $key => $val ){
				if (!is_array ( $val )){
					$p->parameter .= "$key=" . urlencode ( $val ) . "&";
				}
			}
        //    print_r($voList);
			//分页显示
			$page = $p->show();
			//模板赋值显示
			$this->list=$voList ;
			$this->sort=$sort ;
			$this->order=$order ;
			$this->sortImg=$sortImg ;
			$this->sortType=$sortAlt ;
			$this->page=$page;
		}

		$this->assign ( 'totalCount', $count );
        $this->totalPages = ceil($count/$pageNum);
		//lxz
		 $pageNum =empty($_REQUEST['numPerPage']) ? C('PAGE_LISTROWS') : $_REQUEST['numPerPage'];
		 $this->assign ( 'numPerPage', $pageNum ); //每页显示多少条
		$this->assign ( 'currentPage', !empty($_REQUEST[C('VAR_PAGE')])?$_REQUEST[C('VAR_PAGE')]:1);
			
		Cookie::set ( '_currentUrl_', __SELF__ );
		return;
	}

	function insert(){
		//B('FilterString');
        $name= isset($this->actionName)?$this->actionName:$this->getActionName();
		$model = CM($name);
		if (false === $model->create()){
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		$list=$model->add ();
		if ($list!==false){ //保存成功
			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success ('新增成功!');
		}else{
			//失败提示
			$this->error ('新增失败!'. $model->getDbError());
		}
	}

	public function add(){
		$this->display ();
	}

    public	function read(){
		$this->edit ();
	}

	function edit(){
        $name= isset($this->actionName)?$this->actionName:$this->getActionName();
		$model = D($name);
        $attributeLabels=method_exists($model,'attributeLabels');
        if($attributeLabels){
            $this->attributeLabels=$model->attributeLabels();
        }
		$id = $_REQUEST [$model->getPk()];
        if($this->relation){
            $vo = $model->relation(true)->find($id);
        }else{
		    $vo = $model->find ($id);
        }
		$this->assign ( 'vo', $vo );
        $this->display ($this->display);
	}


	function update(){
		//B('FilterString');
        $name= isset($this->actionName)?$this->actionName:$this->getActionName();
		$model = CM( $name );
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}

		// 更新数据
		$list=$model->save ();
		if (false !== $list) {
			//成功提示
			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success ('编辑成功!');
		} else {
			//错误提示
			$this->error ('编辑失败!'.$model->getDbError());
		}
	}
	/**
     +----------------------------------------------------------
	 * 默认删除操作
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return string
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	public function delete() {
		//删除指定记录
        $name= isset($this->actionName)?$this->actionName:$this->getActionName();
		$model = M ($name);
		if (! empty ( $model )){
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				$list=$model->where ( $condition )->setField ( 'status', - 1 );
				if ($list!==false) {
					$this->success ('删除成功！' );
				} else {
					$this->error ('删除失败！');
				}
			} else {
				$this->error ( '非法操作' );
			}
		}
	}
	public function foreverdelete() {
		//删除指定记录
        $name= isset($this->actionName)?$this->actionName:$this->getActionName();
		$model = CM($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				if (false !== $model->where ( $condition )->delete ()) {
					//echo $model->getlastsql();
					$this->success ('删除成功！');
				} else {
					$this->error ('删除失败！');
				}
			} else {
				$this->error ( '非法操作' );
			}
		}
		$this->forward ();
	}


	/**
	+----------------------------------------------------------
	* 添加删除操作  (多个删除)
	+----------------------------------------------------------
	* @access public
	+----------------------------------------------------------
	* @return string
	+----------------------------------------------------------
	* @throws ThinkExecption
	+----------------------------------------------------------
	*/

    public function delAll(){
        $name= isset($this->actionName)?$this->actionName:$this->getActionName();
		$model = CM ($name);
    	$pk=$model->getPk ();  
		$data[$pk]=array('in', $_POST['ids']);
		$model->where($data)->delete();
		$this->success('批量删除成功');
	}

	public function clear() {
		//删除指定记录
        $name= isset($this->actionName)?$this->actionName:$this->getActionName();
		$model = CM($name);
		if (! empty ( $model )) {
			if (false !== $model->where ( 'status=-1' )->delete ()) { // zhanghuihua@msn.com change status=1 to status=-1
				$this->assign ( "jumpUrl", $this->getReturnUrl () );
				$this->success ( L ( '_DELETE_SUCCESS_' ) );
			} else {
				$this->error ( L ( '_DELETE_FAIL_' ) );
			}
		}
		$this->forward ();
	}
	/**
     +----------------------------------------------------------
	 * 默认禁用操作
	 *
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return string
     +----------------------------------------------------------
	 * @throws FcsException
     +----------------------------------------------------------
	 */
	public function forbid($field='status') {
        $name= isset($this->actionName)?$this->actionName:$this->getActionName();
		$model = CM($name);
		$pk = $model->getPk ();
		$id = $_REQUEST [$pk];
		$condition = array ($pk => array ('in', $id ) );
		$list=$model->where($condition)->setField($field,0);
		if ($list!==false) {
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( '状态禁用成功' );
		} else {
			$this->error  (  '状态禁用失败！' );
		}
	}

	public function checkPass() {
        $name= isset($this->actionName)?$this->actionName:$this->getActionName();
		$model = CM($name);
		$pk = $model->getPk ();
		$id = $_GET [$pk];
		$condition = array ($pk => array ('in', $id ) );
		if (false !== $model->checkPass( $condition )) {
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( '状态批准成功！' );
		} else {
			$this->error  (  '状态批准失败！' );
		}
	}

	public function recycle() {
        $name= isset($this->actionName)?$this->actionName:$this->getActionName();
		$model = CM($name);
		$pk = $model->getPk ();
		$id = $_GET [$pk];
		$condition = array ($pk => array ('in', $id ) );
		if (false !== $model->recycle ( $condition )) {

			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( '状态还原成功！' );

		} else {
			$this->error   (  '状态还原失败！' );
		}
	}

	public function recycleBin() {
		$map = $this->_search ();
		$map ['status'] = - 1;
        $name= isset($this->actionName)?$this->actionName:$this->getActionName();
		$model = CM($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
	}

	/**
     +----------------------------------------------------------
	 * 默认恢复操作
	 *
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return string
     +----------------------------------------------------------
	 * @throws FcsException
     +----------------------------------------------------------
	 */
public	function resume($field='status'){
		//恢复指定记录
      $name= isset($this->actionName)?$this->actionName:$this->getActionName();
		$model = CM($name);
		$pk = $model->getPk ();
		$id = $_GET[$pk];
		$condition = array ($pk =>array('in', $id ));
		if (false !== $model->where($condition)->setField($field,1)) {
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( '状态恢复成功！' );
		} else {
			$this->error ( '状态恢复失败！' );
		}
	}


function saveSort() {
		$seqNoList = $_POST ['seqNoList'];
		if (! empty ( $seqNoList )) {
			//更新数据对象
        $name= isset($this->actionName)?$this->actionName:$this->getActionName();
		$model = CM($name);
			$col = explode ( ',', $seqNoList );
			//启动事务
			$model->startTrans ();
			foreach ( $col as $val ) {
				$val = explode ( ':', $val );
				$model->id = $val [0];
				$model->sort = $val [1];
				$result = $model->save ();
				if (! $result) {
					break;
				}
			}
			//提交事务
			$model->commit ();
			if ($result!==false) {
				//采用普通方式跳转刷新页面
				$this->success ( '更新成功' );
			} else {
				$this->error ( $model->getError () );
			}
		}
	}

    /*
     * 上传
     */
    function  upload($ext,$size,$path,$thumb=false,$thumbMaxWidth='100',$thumbMaxHeight='100'){
        $ext=$ext?$ext:array('jpg', 'gif', 'png', 'jpeg');
        $size=$size?$size:3145728;
        $path=$path?$path:'image';
        if (IS_POST){
            import('ORG.Net.UploadFile');
            $upload = new UploadFile();// 实例化上传类
            $upload->maxSize  = $size ;// 设置附件上传大小
            $upload->allowExts  = $ext;// 设置附件上传类型

            $path= $path?"/".$path .'/'.date("Ym",time())."/":date("Ym",time())."/";
            $upload->savePath =  './Public/uploads/'.$path."/";// 设置附件上传目录
            if(!is_dir($upload->savePath)){
                @mkdir($upload->savePath, 0777,true);
            }
            //设置需要生成缩略图，仅对图像文件有效
            $upload->thumb = $thumb;
            // 设置引用图片类库包路径
            $upload->imageClassPath = 'ORG.Util.Image';
            //设置需要生成缩略图的文件后缀
            $upload->thumbPrefix = 'm_,s_';  //生产2张缩略图
            $upload->thumbMaxWidth = $thumbMaxWidth;
            //设置缩略图最大高度
            $upload->thumbMaxHeight = $thumbMaxHeight;
            //设置上传文件规则
            $upload->saveRule = uniqid;
            //删除原图
            //   $upload->thumbRemoveOrigin = true;
            if(!$upload->upload()) {// 上传错误提示错误信息
                echo $upload->getErrorMsg();
            }else{// 上传成功 获取上传文件信息
                $info =  $upload->getUploadFileInfo();
            }
            $data=array(
                "fileName"=>$path.$info[0]['savename'],
                'thumbnail'=>$path.$info[0]['savename'],
            );
            if($info){
                $this->ajaxReturn($data);
            }else{
                $this->error("提交失败");
            }
        }else{
            $this->display('Public/upload');
        }
    }



    //导出用户表
    function exportExcel($name='',$head='',$data=''){
        $book_id= $_GET['book_id'];
        if(!$book_id){
            return false;
        }
        $map=array();
        if(I('map')){
            $map = unserialize(base64_decode(I('map')));
        }


        vendor('PHPExcel.PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Da")
            ->setLastModifiedBy("Da")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated                                                                                  using  PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet(0)->setTitle($name.date('Y-m-d'));
        //$objActSheet = $objExcel->getActiveSheet();

        //设置宽度  默认大小  字体
     //   $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
     //   $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
     //   $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
/*        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(15);*/

        $style=$objPHPExcel->getActiveSheet()->getRowDimension(1);
        $style->setRowHeight(20);
         // 行高
        //      $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID) // 填充模式
        //           ->getStartColor()->setARGB('CCCCCC'); // 背景颜色
        $objPHPExcel->getActiveSheet()->getStyle('A1:M1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial');
        $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);

        $objPHPExcel->getActiveSheet()->setCellValue('A1','序号');

        //    spl_autoload_register(array('Think','autoload'));
        $num=0;
        foreach($data as $key => $v){
            static $i = 2;
            $num++;
            $objPHPExcel->getActiveSheet(0)->setCellValue('A' . $i, $num);
            $objPHPExcel->getActiveSheet(0)->setCellValue('B' . $i, date("Y-m-d",$v['arrival_date']));
            $objPHPExcel->getActiveSheet(0)->setCellValue('C' . $i, $v['bank']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('D' . $i, $v['arrival_amount']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('E' . $i, $v['audit_name']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('F' . $i, $v['audit_remark']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('G' . $i, $v['status']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('H' . $i, $v['username']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('I' . $i, date("Y-m-d",$v['claim_time']));
            $objPHPExcel->getActiveSheet(0)->setCellValue('J' . $i, $v['order_id']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('K' . $i, date("Y-m-d",$v['ticket_date']));
            $objPHPExcel->getActiveSheet(0)->setCellValue('L' . $i, $v['claim_remitter']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('M' . $i, $v['claim_remark']);
            $i++;
        }
        //    $objPHPExcel->getActiveSheet(0)->setCellValue('A' . $i+1, $v['user']['name']);
        //   $objPHPExcel->getActiveSheet(0)->setCellValue('A' . $i+1, "=SUM(A2:A($i+1))");

        /*
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save(str_replace('.php', '.xls', __FILE__));
        echo '导出成功';
         */
        $filename = $name.date('Y-m',time());
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$filename.'.xls');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');  //这里生成excel后会弹出下载

        exit;
    }
}
?>