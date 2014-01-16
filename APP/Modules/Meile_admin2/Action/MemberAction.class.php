<?php

class MemberAction extends IniAction {

    public function index(){
        if($_GET['act']){
            if(I('act')=='view'){
                $this->ajaxReturn( D('Member')->userInfo(I('id')));
            }elseif($_GET['act']=='edit'){
                if(IS_POST){
                    echo  json_encode(D('AdminMember')->edit());
                    exit;
                }
                $this->info=D('Member')->userInfo(I('id'));
                $this->display('member_edit');
            }
        }else{
            import('ORG.Util.Page');// 导入分页类
            $user=D('Member');
            $where="";
            if(I('where')){
                foreach(I('where') as $k=>$v){
                    if($v!='')
                        $where[$k]=$v;
                }
            }
            if(I('search')!=''){
                $search=I('search');
                $where['_string'] = "(username like '%{$search}%')  OR ( name like '%{$search}%')  OR (mobile like '%{$search}%')";
            }
            $count      = $user->where($where)->count();// 查询满足要求的总记录数
            $Page       = new Page($count,30,'','');// 实例化分页类 传入总记录数和每页显示的记录数
            $show       = $Page->show();// 分页显示输出

            $list=$user->where($where)->order("create_time desc")->relation(true)->limit($Page->firstRow.','.$Page->listRows)->select();

            foreach ($list as $k => $v){
                $list[$k]['statusTxt'] = $v['status'] == 1 ? "启用" : "禁用";
            }
            $access=D('Access');
            $info['companyOption']=$access->getOption('company',array('id'=>0));
            $info['departmentOption']=$access->getOption('department',array('id'=>0));
            $info['positionOption']=$access->getOption('position',array('id'=>0));
            $this->info=$info;

            $this->list=$list;
            if(I('ss')==1){
                $datalis['list']=$list;
                $datalis['page']=$show;
                $this->ajaxReturn($datalis);exit;
            }
            $this->assign('page',$show);// 赋值分页输出
            $this->display();
        }
    }


    //导出用户表
    function exportExcel(){
        $datalist= D('Member')->where("")->order("user_id,create_time desc")->relation(true)->select();
        //公司员工手机号
        $filterArr=array(13544558117,13828478554,15113562549,15999978495,13544558117,18673800250,1867380000);
        foreach($datalist as $k=>$v){
            $datalist[$k]['create_date']=date("Y-m-d",$v['create_time']);
            $datalist[$k]['name']=(in_array($v['mobile'],$filterArr) or in_array($v['telephone'],$filterArr))?$v['name']."(公司员工)":$v['name'];

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
        $objPHPExcel->getActiveSheet(0)->setTitle('所有用户');
        //$objActSheet = $objExcel->getActiveSheet();

        //设置宽度  默认大小  字体
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);

        $style=$objPHPExcel->getActiveSheet()->getRowDimension(1);
        $style->setRowHeight(20); // 行高
        $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID) // 填充模式
            ->getStartColor()->setARGB('CCCCCC'); // 背景颜色
        $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial');
        $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);
        $objPHPExcel->getActiveSheet()->setCellValue('A1','序号');
        $objPHPExcel->getActiveSheet()->setCellValue('B1','id');
        $objPHPExcel->getActiveSheet()->setCellValue('C1','帐号');
        $objPHPExcel->getActiveSheet()->setCellValue('D1','姓名');
        $objPHPExcel->getActiveSheet()->setCellValue('E1','手机');
        $objPHPExcel->getActiveSheet()->setCellValue('F1','电话');
        $objPHPExcel->getActiveSheet()->setCellValue('G1','QQ');
        $objPHPExcel->getActiveSheet()->setCellValue('H1','注册时间');
        $objPHPExcel->getActiveSheet()->setCellValue('I1','专属顾问');
        //    spl_autoload_register(array('Think','autoload'));
        $num=0;
        foreach($datalist as $key => $v){
            static $i = 2;
            if(isset($user_id) && $user_id!=$v['user']['id'])
                $i++;

            $user_id=$v['user']['id'];
            $num++;
            $objPHPExcel->getActiveSheet(0)->setCellValue('A' . $i, $num);
            $objPHPExcel->getActiveSheet(0)->setCellValue('B' . $i, $v['id']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('C' . $i, $v['username']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('D' . $i, $v['name']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('E' . $i, $v['mobile']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('F' . $i, $v['telephone']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('G' . $i, $v['qq']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('H' . $i, $v['create_date']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('I' . $i, $v['user']['name']);
            $i++;
        }
        $objPHPExcel->getActiveSheet(0)->setCellValue('A' . $i+1, $v['user']['name']);
        $objPHPExcel->getActiveSheet(0)->setCellValue('A' . $i+1, "=SUM(A2:A($i+1))");

        //添加一个新的worksheet
        $objPHPExcel->createSheet();
        $objPHPExcel->getSheet(1)->setTitle('改版后注册用户');
        $objPHPExcel->setActiveSheetIndex(1);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);

        $style=$objPHPExcel->getActiveSheet()->getRowDimension(1);
        $style->setRowHeight(20); // 行高
        $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID) // 填充模式
            ->getStartColor()->setARGB('CCCCCC'); // 背景颜色
        $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setCellValue('A1','序号');
        $objPHPExcel->getActiveSheet()->setCellValue('B1','id');
        $objPHPExcel->getActiveSheet()->setCellValue('C1','帐号');
        $objPHPExcel->getActiveSheet()->setCellValue('D1','姓名');
        $objPHPExcel->getActiveSheet()->setCellValue('E1','手机');
        $objPHPExcel->getActiveSheet()->setCellValue('F1','电话');
        $objPHPExcel->getActiveSheet()->setCellValue('G1','QQ');
        $objPHPExcel->getActiveSheet()->setCellValue('H1','注册时间');
        $objPHPExcel->getActiveSheet()->setCellValue('I1','专属顾问');
        $num=0;
        foreach($datalist as $key => $v){
            static $s = 2;
            if($v['create_time']<strtotime("20130713")){
                continue;
            }
            if(isset($user_id) && $user_id!=$v['user']['id'])
                $s++;
            $num++;
            $user_id=$v['user']['id'];
            $objPHPExcel->getActiveSheet(1)->setCellValue('A' . $s, $num);
            $objPHPExcel->getActiveSheet(1)->setCellValue('B' . $s, $v['id']);
            $objPHPExcel->getActiveSheet(1)->setCellValue('C' . $s, $v['username']);
            $objPHPExcel->getActiveSheet(1)->setCellValue('D' . $s, $v['name']);
            $objPHPExcel->getActiveSheet(1)->setCellValue('E' . $s, $v['mobile']);
            $objPHPExcel->getActiveSheet(1)->setCellValue('F' . $s, $v['telephone']);
            $objPHPExcel->getActiveSheet(1)->setCellValue('G' . $s, $v['qq']);
            $objPHPExcel->getActiveSheet(1)->setCellValue('H' . $s, $v['create_date']);
            $objPHPExcel->getActiveSheet(1)->setCellValue('I' . $s, $v['user']['name']);
            $s++;
        }
        /*
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save(str_replace('.php', '.xls', __FILE__));
        echo '导出成功';
         */
        $filename = "用户表".date('Y-m',time());
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$filename.'.xls');
        header('Cache-Control: max-age=0');


        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');  //这里生成excel后会弹出下载

        exit;
    }


}