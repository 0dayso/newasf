<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-9-2
 * Time: 下午4:22
 * To change this template use File | Settings | File Templates.
 */
class RequireAction extends IniAction{
    function index(){
        echo "index";
    }

    function sidebar(){
          if(IS_POST){
              if(!I('phone')){
                $this->error('提交的数据的误');
              }

              $rs=D('RequireOrder')->insert();
              echo $rs;
          }else{
          $html=$this->fetch('sidebar');
          header("Content-type: text/javascript; charset=utf-8");
          echo htmltojs($html);
          }
    }

    function insert(){

    }

    function ajaxSend(){
        echo 11;
        $this->send=D('RequireOrder')->ajaxList();
        print_r( $this->send);
        $this->display('sidebar');
    }

    function ajaxInfo(){
        $require=D('RequireOrder');
        $this->info=$require->ajaxInfo();
        dump($this->info);
     //   $this->display('sidebar');
    }


}