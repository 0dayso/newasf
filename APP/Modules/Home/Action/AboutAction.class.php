<?php
//关于我们
class AboutAction extends IniAction{

    Public function index(){	
		$this->title="关于爱尚飞";
		$this->display();
    }
	
	Public function intro(){
		if(I('view')=='brand'){
			$this->title="品牌故事";
			$this->display('About/Intro/brand');
		}
		elseif(I('view')=='style'){
			$this->title="员工风采";
			$this->display('About/Intro/style');
		}
		else{
			$this->title="品牌故事";
			$this->display('About/index');
		}
    }
	
	Public function service(){
        $this->title="会员服务";
        $this->display();
    }
	
	Public function qualifications(){

        $this->title="公司资质";
        $this->display();
    }
	
	Public function honour(){

        $this->title="公司荣誉";
        $this->display();
    }
	
    Public function events(){
        $this->title="公司发展历程";
        $this->display();
    }
	
	Public function partner(){
        $this->title="合作伙伴";
        $this->display();
    }
	
	Public function job(){
        $this->title="人才招聘";
        $this->display();
    }
	
    function privacy(){
        $this->title="隐私保护 ";
        $this->display();
    }
	
    Public function contact(){
        $this->title="联系我们";
        $this->display();
    }
	
	Public function toubu(){
	$this->title="十大理由";
    $this->display();

    }
	

}