<?php
include_once './api.php';

function expensify_post($data, $url_params)
{
    global $URL_BASE;

    $opts = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($opts);
    $result = file_get_contents($URL_BASE . $url_params, false, $context);
    if (!$result) {
        // Something went wrong! 
        $errors = array(
            'error_message' => "Something went wrong with the Expensify API.",
            'resolution' => "Please try again later, or contact Expensify. ",
            'target' => "#error"
        );
        error($errors);
    }

    $result = json_decode($result, true);

    if ($result['jsonCode'] != 200) {
        // Something else went wrong!
        $errors = array(
            'error_code' => $result['jsonCode'],
            'error_message' => $result['message'],
            'target' => "#error"
        );
        error($errors);
    }

    return ($result);
}
