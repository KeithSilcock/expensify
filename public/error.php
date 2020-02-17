<?php

function error($error){
    $defaults = array(
        'error_message'=>"",
        'resolution' => "");
    
    // combine into single array
    $error = array_merge($defaults, $error);
    echo json_encode($error);

}

?>