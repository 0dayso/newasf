<?php
class asmsBase{
    private  $error;
    Public   $db_prefix;
    Public   $asms_host;
    function __construct(){
        $this->db_prefix=C('DB_PREFIX');
        $this->asms_host=C('ASMS_HOST');
    }

    function getError($err=''){
        if($err) $this->error=$err;
        return $this->error;
    }

    function setError($err){
        if($err) $this->error=$err;
        return $this->error;
    }

}

?>