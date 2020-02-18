<?php

function error($warning){
    $defaults = array(
        'warning_message'=>"",
        'target' => "",
    );
    // combine into single array
    $warning = array_merge($defaults, $warning);
    $warning_code = $warning['warning_code'];

    echo json_encode(array("WARNING"=>$warning));
    exit;
}

?>