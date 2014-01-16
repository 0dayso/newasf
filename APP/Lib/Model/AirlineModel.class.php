<?php
class AirlineModel extends RelationModel{
	protected $_link = array(
		'city'=> array(  
			'mapping_type'=>BELONGS_TO,
			'city'=>'cityIata',
           // 定义更多的关联属性
      ),
	);
	
    public function getAirlineName($iata){	
        $airline=$this->where("iata='$iata'")->find();
        
        return $airline ? $airline['name'] : '未知';
    }
		
		

		
		
}