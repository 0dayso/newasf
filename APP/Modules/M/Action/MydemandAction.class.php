<?php
// 我的订票需求控制器
class MydemandAction extends IniAction {
    public function index(){
        if(IS_POST){
            $rs=preg_match('/^([0-9]){11,12}$/i',I('phone'));
            if(!$rs){
                echo "手机号格式不正确";
                exit;
            }
            $_POST['source']='mobile';
            $rs=D('RequireOrder')->insert();
            if($rs){
                $this->success('提交成功,稍后我们客服将联系您',U('/'));
            }else{
                $this->error('抱歉！ 提交失败,或您已提交过此需求单, 建议直接给我们来电');
            }
        }else{
             $this->display();
        }
    }
	
	
}