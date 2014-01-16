<?php
class CityAction extends CommonAction{   
     function cityData(){
        if(I('so')){
            $where['id'] = array('like',"%".I('so')."%");
            $where['name']  = array('like',"%".I('so')."%");
            $where['iata']  = array('like',"%".I('so')."%");
            $where['_logic'] = 'or';
            $map['_complex'] = $where;
        }
        $this->map=$map;
        $this->map['id'] = array('egt',2);
        $this->map['status'] = 1;
        
        parent::index();

    }
}
?>
