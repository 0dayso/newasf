<?php
class ContinentModel extends CommonModel{
		
		function __construct(){ //³õÊ¼»¯ÖÞ
			$this->diyid=intval($_GET['diyid']);
			$this->db=M('toplace');
			$rs=$this->db->where("type=1 and diyid=".$this->diyid)->find();			
		//	if(!$rs) die();
			$this->p_id=$rs['ID'];
			
			$this->p_name=$rs["Name"];			
			$db=M('diqv');
			$rs1=$db->find($this->p_id);			
		//	if(!$rs1) die();
			$this->p_title=titlereplace($rs1['Title'],$this->p_name);
			$this->p_keywords=titlereplace($rs1['keywords'],$this->p_name);
			$this->p_description=titlereplace($rs1['Description'],$this->p_name);			
		}
	
		
		function getstate($zhou){
			$zhou=$zhou?$zhou:$this->p_id;
			$sql="select name,code,diyid,zhou from ml_toplace where type=2 and zhou='$zhou' order by view desc,diyid";
			$rs=$this->db->query($sql);
			return $rs;
			
		}
		
		
		function  hot_city($zhou){
			$zhou=$zhou?$zhou:$this->p_id;
			$rs=$this->db->query("select name,code,diyid from ml_toplace where type=3 and guojia<>43 and zhou='$zhou' order by view desc,id limit 50");
			return $rs;
			
		}
		
		function uuss(){
			echo 111;
			print_r($_GET);
		}


		
		
}

?>