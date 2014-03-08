<?php
// 首页控制器
class IndexAction extends IniAction {
    public function index(){
        if(cookie('ts_refer')!='mobile' && is_mobile()){
                redirect(U('/@m'));
        }
        D('Message')->message_action('reg_success',$this);//发送信息
        $this->title="爱尚飞国际机票官网_爱尚飞让你爱上飞";
     //   $this->order=D("Booking")->nearOrder(); //最近订单

        $this->evaluat=D('Evaluat')->getList('',9,"total desc");

        R('Common/cheap','arr'); //特价机票数据

        //大家正在查
        $this->while_search=D('searchRecord')->whileSearch(5);

        $nothing=D('Evaluat')->getList('',5);

        $nothingA=array(
           " 圣诞&元旦双节狂欢出国度假  活动",
            "留学生机票实惠季 活动",
            " 国际机票预订金秋惠 活动",
            "年假出国特价机票精选 活动",
            "  国际机票预订金秋惠 活动",
             " 爱尚飞携手斯航带您玩转马尔代夫 活动",
            "  国泰航空出发美澳温情特选 活动",
       );

        $nothingUrl=array(
            "zt/sdyd2013",
            "zt/lxs",
            "zt/gqcf",
            "zt/njcg",
            "zt/gqcf",
            "zt/srilankan_sales",
            "zt/cathaypacific_sales",
        );

        foreach($nothing as $kel=>$val){
            $m= $val['id']%7;
            $nothing[$kel]['hd']=$nothingA[$m];
            $nothing[$kel]['hd_url']=$nothingUrl[$m];
            $sec=time()-$val['create_time'];
            $sec = round($sec/60);
            if ($sec >= 60){
                $hour = floor($sec/60);
            //    if($hour>24){
             //       $res=(floor($hour/24))%2 .'天';
            //    }else{
                $min = $sec%60;
                $res = $hour.'小时 ';
                $min != 0  &&  $res .= $min.'分钟';
                $res = $min.'分钟';
           //     }
            }else{
                $res = $sec.'分钟';
            }
            $nothing[$kel]['from_now']=$res;
        }
        $this->nothing=$nothing;

    //    rand(15,30);
     //   print_r($nothing);
    //    exit;
		$this->display();
    }

    function feiji(){  //查询显示升级页面
           $this->title="机票查询";
        $this->display();
    }
	
	public function newindex(){
		$this->display();
        }
	
}