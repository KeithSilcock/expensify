<?php

function redirect($location, $warning=null){
    if(isset($warning)){
        echo json_encode(array("REDIRECT"=>$location, "WARNING"=>$warning));
    }else{
        echo json_encode(array("REDIRECT"=>$location));
    }
    exit;
}
