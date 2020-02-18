<?php

function error($error){
    $defaults = array(
        'error_code'=>"",
        'error_message'=>"",
        'resolution' => "",
        'target' => "",
    );

    $common_error_codes = array(
        '401'=>[
            'description'=>"Password is wrong",
            'how_to_fix'=>"Try entering password again.",
        ],
        '404'=>[
            'description'=>"Account not found",
            'how_to_fix'=>"Make sure you are using a valid email address.",
        ],
        '407'=>[
            'description'=>"AuthToken expired",
            'how_to_fix'=>"Make sure you're getting a new authToken from the response of each request or log in again.",
        ],
    );

    
    // combine into single array
    $error = array_merge($defaults, $error);
    $error_code = $error['error_code'];

    // check if default expensify errors or custom keith errors
    if(array_key_exists($error_code,$common_error_codes)){
        $error['error_message'] = $common_error_codes[$error_code]['description'];
        $error['resolution'] = $common_error_codes[$error_code]['how_to_fix'];
    }

    echo json_encode(array("ERROR"=>$error));
    exit;

}

?>