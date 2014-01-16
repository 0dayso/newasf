<?php
class AirportModel extends RelationModel{
	protected $_link = array(
		'city'=> array(
			'mapping_type'=>BELONGS_TO,
            'class_name'=>'city',
         //   'foreign_key'=>'id',
			'City_iata'=>'iata',
			'mapping_fields'=>'id,name,name_en',
           // 定义更多的关联属性
      ),
	);


    function getAirportCity($iata){
        $name=$this->where("iata='$iata'")->find();
        return $name ? $name['name'] : '未知';
    }

		
		

		
		
}