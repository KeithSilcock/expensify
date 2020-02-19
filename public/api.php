<?php

include_once './check_login.php';
include_once './error.php';
include_once './redirect.php';
include_once './init.php';
include_once './ping_expensify.php';


$URL_BASE = "https://www.expensify.com/api";

if( isset($_POST['action'])  && !empty($_POST['action']) ){

  $action = $_POST['action']; 

  switch ($action){
    case "get-table-data":
      // do stuff for logging in
      respond_success(get_table_data());
    break;
    default:
      // log that a broken action has been recorded
      
  }

    // $vlus = $_POST['json']; 
    // $vlus = blah_decode($vlus);
}

function get_table_data(){
  $url_params = '?command=Get';
  $data = array('authToken' => $_SESSION['authToken'], 'returnValueList' => "transactionList", 
                'startDate' => null, 'endDate' => null,);

  $table_data = expensify_post($data, $url_params);

  return($table_data);
}

function respond_success($data) {
  echo json_encode(array("SUCCESS"=>$data));
}

?>