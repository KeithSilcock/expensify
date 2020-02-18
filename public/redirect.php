<?php

function redirect($location, $warning=null){
    if(isset($warning)){
        echo json_encode(array("REDIRECT"=>$location, "WARNING"=>$warning));
    }else{
        echo json_encode(array("REDIRECT"=>$location));
    }
    exit;
}

function backend_redirect($location, $warning=null){
    if(isset($warning)){
        header("Location: $location?$warning");
    }else{
        header("Location: $location");
    }
    exit;
}

?>