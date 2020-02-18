<?php

include './check_login.php';
include './error.php';
include './redirect.php';
include './init.php';


$head = checklogin();

$URL_BASE = "https://www.expensify.com/api";

if( isset($_POST['action'])  && !empty($_POST['action']) ){

  $action = $_POST['action']; 
  $form = $_POST['form'];

  switch ($action){
    case "login":
      // do stuff for logging in
      login($form);
    break;
    default:
      // log that a broken action has been recorded
      
  }

    // $vlus = $_POST['json']; 
    // $vlus = blah_decode($vlus);
}

// function server_ping_test() {
//   $url = 'https://www.expensify.com/api?command=Authenticate';
//   $data = array('partnerName' => $_PARTNER_NAME, 'partnerPassword' => $_PARTNER_PASSWORD, 
//                 'partnerUserID' => 'expensifytest@mailinator.com', 'partnerUserSecret' => 'hire_me',);

//   $opts = array(
//       'http' => array(
//           'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
//           'method'  => 'POST',
//           'content' => http_build_query($data)
//       )
//   );
//   $context  = stream_context_create($opts);
//   $result = file_get_contents($url, false, $context);
//   if ($result === FALSE) { 
//     // Something went wrong! 
//     respond("Something went wrong, sorry");
//   }

//   $result = json_decode($result);

//   if ($result['httpCode'] != 200) {
//     // Something else went wrong!
//     respond("Something went wrong, sorry");
//   }

//   respond($result);

// }

function respond_success($data) {
  echo json_encode(array("SUCCESS"=>$data));
}

?>