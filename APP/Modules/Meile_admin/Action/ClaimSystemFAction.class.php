<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-9-5
 * Time: 下午5:43
 * To change this template use File | Settings | File Templates.
 */

class ClaimSystemFAction extends CommonAction{
    function _before_index(){
        $this->actionName='ClaimBooks';
        $this->relation=true;
        $where['company_id']=array(array('eq',$this->userInfo['company_id']),array('eq',0),'or');
        $where['user_id']=getUid();
        $where['view']=1;
        $where['_logic'] = 'or';
        $map['_complex']=$where;
        if(!auth_check()){
            $this->map=$map;
        }
        $this->order="id desc";

        $this->map['type']=0;
        $this->index(D('ClaimBooks'));
        //  dump($this->list);
    }

    //添加帐本
    function addBook(){
        if($_POST){
            $claimBooks=D('ClaimBooks');
            $data=$_POST;
            $data['user_id']=getUid();
            $data['type']=I('get.type');
            $data['create_time']=time();
            $data['bank']=M('company')->where('id='.$_POST['company_id'])->getField('bank');
            if(!$claimBooks->create($data)){
                $this->error($claimBooks->getError());
            }
            $rs=$claimBooks->add();
            if($rs){
                $this->success('成功');
            }else{
                $this->error('失败');
            }
        }else{
            $access=D('Access');
            if(auth_check()){
                $info['companyOption']=$access->getOption('company',array('id'=>I('company_id')));
            }else{
               $company= M('company')->find($this->userInfo['company_id']);
                $info['companyOption']['option']="<option value='{$this->userInfo['company_id']}'>--{$company['name']}--</option>";
            }
            $this->bank=M('company')->where('id='.$this->userInfo['company_id'])->getField('bank');

            $this->info=$info;
            $this->display();
        }
    }

    //认证权限
    function claimAuthCheck(){
        $book_id=isset($_GET['book_id'])?$_GET['book_id']:$_POST['book_id'];
        if(!$book_id){
            $id=isset($_GET['id'])?$_GET['id']:$_POST['id'];
            if(!$id) return;
            $book_id=D('ClaimData')->where("id=".$id)->getField('book_id');
        }
        if(!auth_check()){ //权限
            $wheres['company_id']=array(array('eq',$this->userInfo['company_id']),array('eq',0),'or');
            $wheres['view']=1;
            $wheres['_logic'] = 'or';
            $wheres2['_complex']=$wheres;
            unset($wheres);
            $wheres=$wheres2;
        }
        $wheres['id']=$book_id;

        $this->bookInfo=D('ClaimBooks')->where($wheres)->find();
        if(!$this->bookInfo && !auth_check()){
            $this->error('没有权限');
        }
        $this->book_id=$book_id;
    }

    //帐本详情列表
    function claimList(){
        $book_id=isset($_GET['book_id'])?$_GET['book_id']:$_POST['book_id'];
        if(!auth_check()){ //权限
            $wheres['company_id']=array(array('eq',$this->userInfo['company_id']),array('eq',0),'or');
            $wheres['view']=1;
            $wheres['_logic'] = 'or';
            $wheres2['_complex']=$wheres;
            unset($wheres);
            $wheres=$wheres2;
        }
        $wheres['id']=$book_id;
        $this->bookInfo=D('ClaimBooks')->where($wheres)->find();
        if(!$this->bookInfo){
            $this->error('没有权限');
        }

        $this->book_id=$book_id;
        if(I('so')){
            $where['ClaimData.id']  = array('like',"%".$_POST['so']."%");
            $where['ClaimData.arrival_amount']  = array('like',"%".I('so')."%");
            $where['ClaimData.bank']  = array('like',"%".I('so')."%");
            $where['ClaimData.claim_name']  = array('like',"%".I('so')."%");
            $where['_logic'] = 'or';
            $map['_complex']=$where;
        }
      //  print_r( D('ClaimDataView')->field('bank,arrival_amount')->where($where)->limit(1)->select() );

        if(I('so_date1')&& I('so_date2')){
            $map['arrival_date']=array(array('egt',strtotime(I('so_date1'))),array('elt',strtotime(I('so_date2'))));
        }

        $this->order='id desc';
        if(!empty($map))
            $this->map = $map;
        $this->index(D('ClaimDataView'));
        $list=$this->list;
        $amount=$poundage=0;
        foreach($list as $k=>$v){
            if(!$v['create_username']){
                $list[$k]['create_username']=$v['create_username2'];
            }
            if(!$v['claim_username']){
                $list[$k]['claim_username']=$v['claim_username2'];
            }
            if(!$v['update_username']){
                $list[$k]['update_username']=$v['update_username2'];
            }
            if(!$v['audit_username']){
                $list[$k]['audit_username']=$v['audit_username2'];
            }

            if($v['edit_order_id']){
                $list[$k]['order_id']=$v['edit_order_id'];
            }
            if($v['edit_ticket_date']){
                $list[$k]['ticket_date']=$v['edit_ticket_date'];
            }
            if($v['edit_claim_remark']){
                $list[$k]['claim_remark']=$v['edit_claim_remark'];
            }
            if($v['edit_claim_remitter']){
                $list[$k]['claim_remitter']=$v['edit_claim_remitter'];
            }
            $amount+=$v['arrival_amount']; //计算当前页金额
            $poundage+=$v['poundage'];
        }

        $this->totalAmount=D('ClaimData')->where($this->map)->sum('arrival_amount');//计算总额
        $this->totalPoundage=D('ClaimData')->where($this->map)->sum('poundage');
        $this->amount=$amount;
        $this->poundage=$poundage;
        $this->list=$list;
        $this->display();
    }

    // 查看详情
    function _before_read(){
        $this->relation=true;
        $this->actionName='ClaimData';
    }

    //添加认帐
    function addClaim(){
        if($_POST){
            $ClaimData=M('ClaimData');
            $data=$_POST;
            $data['create_uid']=getUid();
            $data['arrival_date']=strtotime(I('arrival_date'));
            $data['create_time']=time();
            if(!$ClaimData->create($data)){
                $this->error($ClaimData->getDbError());
            }
            $rs=$ClaimData->add($data);
            if($rs){
                cookie('old_input_arrival_date',I('arrival_date'));
                $this->success('成功');
            }else{
                $this->error('失败');
            }
        }else{
            $this->book_id=I('id');
            $where['id']=I('id');
            $bank=M('ClaimBooks')->where($where)->getField('bank');//银行

            if(stristr($bank,'|')){
                $bank=explode('|',$bank);
            }elseif(stristr($bank,'、')){
                $bank=explode('、',$bank);
            }elseif(stristr($bank,',')){
                $bank=explode(',',$bank);
            }else{
                $bank=explode(' ',$bank);
            }

            foreach($bank as $v){
                $v=trim($v);
                if($v) $bankArr[]=$v;
            }
            $this->bankArr=$bankArr;
            $this->display();
        }
    }



    //编辑认帐
    function editClaim(){
        $this->claimAuthCheck();
        if($_POST){
            $ClaimData=D('ClaimData');
            $where['id']=I('id');
            $da=$ClaimData->where($where)->getField('status');
            if($da==2){
                $this->error('该账单已审核不能再修改');
            }
            $data=$_POST;
            $data['update_uid']=getUid();
            $data['arrival_date']=strtotime(I('arrival_date'));
            $data['update_time']=time();
            if(!$ClaimData->create($data)){
                $this->error($ClaimData->getError());
            }
            $rs=$ClaimData->save();
            if($rs)
                $this->success('成功');
            else
                $this->error('失败');
        }else{
            $ClaimData=D('ClaimData');
            $where['id']=I('id');
            $this->vo=$ClaimData->where($where)->find();
            $this->display();
        }
    }

    //编辑帐本
    function editBook(){
        $claimBooks=D('ClaimBooks');
        $where['id']=I('id');
        $where['user_id']=getUid();
        $uer_id=$claimBooks->where($where)->getField('user_id');

        if(!$uer_id && !auth_check()){
            $this->error('没有权限 ');
        }

        if($_POST){
            $data=$_POST;
            $data['update_uid']=getUid();
            $data['update_time']=time();
            if(!$claimBooks->create($data)){
                $this->error($claimBooks->getError());
            }
            $rs=$claimBooks->save();
            if($rs)
                $this->success('成功');
            else
                $this->error('失败');
        }else{
            $wheres['id']=I('id');
            $this->vo=$claimBooks->where($wheres)->find();
            $access=D('Access');
            if(auth_check()){
                $company_id=$this->vo['company_id'];
                $info['companyOption']=$access->getOption('company',array('id'=>$company_id));
            }else{
                $company= M('company')->find($this->userInfo['company_id']);
                $info['companyOption']['option']="<option value='{$this->userInfo['company_id']}'>--{$company['name']}--</option>";
            }
            $this->info=$info;
            $this->display();
        }
    }

    //审核操作
    function audit(){
        $this->claimAuthCheck();
        if(IS_POST){
            $ClaimData=D('ClaimData');
            if($_GET['status']==2){
                $data1['id']=$_GET['id'];
                $data1['status']=2;
                $data1['audit_uid']=getUid();
                $data1['update_time']=time();
                $rs= $ClaimData->save($data1);
                if($rs)
                    $this->success('成功');
                else
                    $this->error('失败');
                exit;
            }
            $data=$_POST;
            $data['edit_ticket_date']=strtotime($data['edit_ticket_date']);
            if($data['status']==2){
                $rs=$ClaimData->find(I('id'));
                if($rs['order_id']==$data['edit_order_id']){
                    unset($data['edit_order_id']);
                }
                if($rs['claim_remitter']==$data['edit_claim_remitter']){
                    unset($data['edit_claim_remitter']);
                }

                if($rs['ticket_date']==$data['edit_ticket_date']){
                    unset($data['edit_ticket_date']);
                }
                if($rs['claim_remark']==$data['edit_claim_remark']){
                    unset($data['edit_claim_remark']);
                }
            }elseif($data['status']==0){
                $data['order_id']='';
                $data['claim_remitter']='';
                $data['ticket_date']='';
                $data['claim_remark']='';
                $data['edit_order_id']='';
                $data['edit_claim_remitter']='';
                $data['edit_ticket_date']='';
                $data['edit_claim_remark']='';
            }

            $data['audit_uid']=getUid();
            $data['update_time']=time();
            if(!$ClaimData->create($data)){
                $this->error($ClaimData->getError());
            }
            $rs=$ClaimData->save();
            if($rs)
                $this->success('成功');
            else
                $this->error('失败');
        }else{
            $ClaimData=D('ClaimData');
            $where['id']=I('id');
            $info=$ClaimData->relation(true)->where($where)->find();
      //      print_r($info);
            $access=D('Access');
            $info['departmentOption']=$access->getOption('department',array('id'=>$info['department_id']));
            $this->vo=$info;
            $this->display();
        }
    }

    function applyBook(){
        $this->actionName='ClaimBooks';
        $this->map['type']=1;
        $this->relation=true;
        $this->index(D('ClaimBooks'));
        $this->display();
    }

    //申请付款
    function applyPay(){
        $this->relation=true;
        if(I('so')){ //搜索
            $where['id']  = array('like',"%".$_POST['so']."%");
            $where['apply_amount']  = array('like',"%".I('so')."%");
            $where['apply_name']  = array('like',"%".I('so')."%");
            $where['account_name']  = array('like',"%".I('so')."%");
            $where['apply_remark']  = array('like',"%".I('so')."%");
            $where['_logic'] = 'or';
            $map['_complex']=$where;
        }

        if($_POST['so_date1'] && $_POST['so_date2']){ //日期搜索
            $map['create_time']=array(array('egt',strtotime(I('so_date1'))),array('elt',strtotime(I('so_date2'))));
        }
        if(!empty($map))
            $this->map = $map;
        $this->map['book_id']=I('book_id');
        $this->order='id desc';//默认倒序
        $this->index(D('ClaimPayment'));
        //    print_r($this->list);
        $amount=0;
        foreach($this->list as $v){
            $amount+=$v['apply_amount']; //计算当前页金额
        }
        $this->amount=$amount;
        $this->bookInfo=D('ClaimBooks')->find(I('book_id'));
        $this->list=D('ClaimPayment')->format($this->list);
        $this->totalAmount=D('ClaimPayment')->where($this->map)->sum('apply_amount');//计算总额
        $this->display();
    }

    //审核处理支付申请
    function auditPay(){
        $ClaimPayment=D('ClaimPayment');
        $where['id']=I('id');
        $info=$ClaimPayment->relation(true)->find(I('id'));
        if($info['status']!=0){
            if($info['payment_uid']!=getUid()){
                $this->error('当前状态不可操作,请刷新页面');
            }
        }
        if($_POST){ //数据提交
            $data=$_POST;
            $data['id']=I('id');
            $data['status']=2;
            $data['payment_time']=time();
            if(!$ClaimPayment->create($data)){
                $this->error($ClaimPayment->getError());
            }
            $rs=$ClaimPayment->save();
            if($rs)
                $this->success('成功');
            else
                $this->error('失败');
        }else{
            $data['id']=I('get.id');
            if(isset($_GET['cancel']) && $_GET['cancel']==1){
                $data['status']=0;
                $data['payment_date']='';
                $data['pocket_amount']=0;
                $ClaimPayment->save($data);
                $this->success('已取消');exit;
            }else{
                if($info['status']==0)
                    $data['status']=1;
                $data['payment_uid']=getUid();
                $ClaimPayment->save($data);
            }
            $info=$ClaimPayment->format(array(0=>$info));
            $this->vo=$info[0];
            $this->display();
        }
    }

    //财务复核
    function reviewPay(){
        $ClaimPayment=D('ClaimPayment');
        $where['id']=I('id');
        $info=$ClaimPayment->find(I('id'));
        if($info['status']!=2){
            $this->error('当前不可操作');
        }
        $infos=$ClaimPayment->format(array(0=>$info));
        $info=$infos[0];
        if($_POST){
            $data=$_POST;
            $data['id']=I('id');
            $data['status']=I('status');
            $data['review_time']=time();
            $data['review_uid']=getUid();
            if(!$ClaimPayment->create($data)){
                $this->error($ClaimPayment->getError());
            }
            $rs=$ClaimPayment->save();
            if($rs)
                $this->success('成功');
            else
                $this->error('失败');
        }else{
            $access=D('Access');
            $info['departmentOption']=$access->getOption('department',array('id'=>$info['department_id']));
            $this->vo=$info;
            $this->display();
        }
    }


    //编辑支付申请
    function editPay(){
        $ClaimPayment=D('ClaimPayment');
        $where['id']=I('id');
        $info=$ClaimPayment->find(I('id'));
        if($info['status']!=0 && !auth_check()){
            $this->error('当前状态不可操作');
        }

        if($_POST){
            $data=$_POST;
            $data['id']=I('id');
            $data['status']=0;
            if(!$ClaimPayment->create($data)){
                $this->error($ClaimPayment->getError());
            }
            $rs=$ClaimPayment->save();
            if($rs)
                $this->success('成功');
            else
                $this->error('失败');
        }else{
            $access=D('Access');
            $info['departmentOption']=$access->getOption('department',array('id'=>$info['department_id']));
            $this->vo=$info;
            $this->display();
        }
    }

    // 查看支付申请详情信息
    function readPay(){
        $ClaimPayment=D('ClaimPayment');
        $info=$ClaimPayment->relation(true)->find(I('id'));
        $info=$ClaimPayment->format(array(0=>$info));
        $this->vo=$info[0];
        $this->display();
    }


    //导出帐本
    function exportExcel(){
        $book_id= $_GET['book_id'];
        if(!$book_id)
            return false;

        $map=array();
        if(I('map'))
            $map = unserialize(base64_decode(I('map')));

        $bookInfo=D('ClaimBooks')->find($book_id);
        //权限过滤
        if($bookInfo['company_id']!=0){
            if($bookInfo['company_id']!=$this->userInfo['company_id'] && !auth_check()){
                $this->error('没有权限');
            }
        }

        $map['book_id']=$book_id;
        $list=D('ClaimDataView')->where($map)->select();
        foreach($list as $k=>$v){
            if($v['edit_order_id']){
                $list[$k]['order_id']=$v['edit_order_id'];
            }
            if($v['edit_ticket_date']){
                $list[$k]['ticket_date']=$v['edit_ticket_date'];
            }
            if($v['edit_claim_remark']){
                $list[$k]['claim_remark']=$v['edit_claim_remark'];
            }
            if($v['edit_claim_remitter']){
                $list[$k]['claim_remitter']=$v['edit_claim_remitter'];
            }
            if(!$v['claim_name']){
                $list[$k]['claim_name']=$v['claim_username'];
            }
            if(!$v['department']){
                $list[$k]['department']=$v['user_department'];
            }

              switch($v['status']){
                  case 0:
                      $list[$k]['status']='未认帐';
                      break;
                  case 1:
                      $list[$k]['status']='审核中';
                      break;
                  case 2:
                      $list[$k]['status']='审核完成';
                      break;
              }
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
        $objPHPExcel->getActiveSheet(0)->setTitle($bookInfo['name']);
        //$objActSheet = $objExcel->getActiveSheet();

        //设置宽度  默认大小  字体
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(15);

        $style=$objPHPExcel->getActiveSheet()->getRowDimension(1);
        $style->setRowHeight(20); // 行高
  //      $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID) // 填充模式
 //           ->getStartColor()->setARGB('CCCCCC'); // 背景颜色
        $objPHPExcel->getActiveSheet()->getStyle('A1:N1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial');
        $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);
        $objPHPExcel->getActiveSheet()->setCellValue('A1','序号');
        $objPHPExcel->getActiveSheet()->setCellValue('B1','到账日期');
        $objPHPExcel->getActiveSheet()->setCellValue('C1','银行');
        $objPHPExcel->getActiveSheet()->setCellValue('D1','到账金额');
        $objPHPExcel->getActiveSheet()->setCellValue('E1','财务审核人');
        $objPHPExcel->getActiveSheet()->setCellValue('F1','备注');
        $objPHPExcel->getActiveSheet()->setCellValue('G1','状态');
        $objPHPExcel->getActiveSheet()->setCellValue('H1','认账人');
        $objPHPExcel->getActiveSheet()->setCellValue('I1','认账人部门');
        $objPHPExcel->getActiveSheet()->setCellValue('J1','认账日期');
        $objPHPExcel->getActiveSheet()->setCellValue('K1','订单号');
        $objPHPExcel->getActiveSheet()->setCellValue('L1','出票日期');
        $objPHPExcel->getActiveSheet()->setCellValue('M1','汇款人');
        $objPHPExcel->getActiveSheet()->setCellValue('N1','认账备注');
        //    spl_autoload_register(array('Think','autoload'));
        $num=0;
        foreach($list as $key => $v){
            static $i = 2;
            $num++;
            $objPHPExcel->getActiveSheet(0)->setCellValue('A' . $i, $num);
            $objPHPExcel->getActiveSheet(0)->setCellValue('B' . $i, date("Y-m-d",$v['arrival_date']));
            $objPHPExcel->getActiveSheet(0)->setCellValue('C' . $i, $v['bank']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('D' . $i, $v['arrival_amount']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('E' . $i, $v['audit_username']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('F' . $i, $v['audit_remark']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('G' . $i, $v['status']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('H' . $i, $v['claim_name']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('I' . $i, $v['department']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('J' . $i, date("Y-m-d",$v['claim_time']));
            $objPHPExcel->getActiveSheet(0)->setCellValue('K' . $i, $v['order_id']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('L' . $i, date("Y-m-d",$v['ticket_date']));
            $objPHPExcel->getActiveSheet(0)->setCellValue('M' . $i, $v['claim_remitter']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('N' . $i, $v['claim_remark']);
            $i++;
        }
    //    $objPHPExcel->getActiveSheet(0)->setCellValue('A' . $i+1, $v['user']['name']);
     //   $objPHPExcel->getActiveSheet(0)->setCellValue('A' . $i+1, "=SUM(A2:A($i+1))");

        /*
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save(str_replace('.php', '.xls', __FILE__));
        echo '导出成功';
         */
        $filename = $bookInfo['name'].date('Y-m',time());
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$filename.'.xls');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');  //这里生成excel后会弹出下载

        exit;
    }
}