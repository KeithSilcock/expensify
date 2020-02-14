<?php

include './checklogin.php';
$head = checklogin(false);

if(isset($_POST['action'])  && !empty($_POST['action'])){

  server_ping_test();


    // $vlus = $_POST['json']; 
    // $vlus = blah_decode($vlus);
}

function respond($data) {
  echo json_encode($data);
}

function authenticate_user($email, $password){
  $url = 'https://www.expensify.com/api?command=Authenticate';
  $data = array('partnerName' => $_PARTNER_NAME, 'partnerPassword' => $_PARTNER_PASSWORD, 
                'partnerUserID' => $email, 'partnerUserSecret' => $password,);

  $opts = array(
      'http' => array(
          'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
          'method'  => 'POST',
          'content' => http_build_query($data)
      )
  );
  $context  = stream_context_create($opts);
  $result = file_get_contents($url, false, $context);
  if ($result === FALSE) { 
    // Something went wrong! 
    respond("Something went wrong, sorry");
  }

  $result = json_decode($result);

  if ($result['httpCode'] != 200) {
    // Something else went wrong!
    respond("Something went wrong, sorry");
  }

  respond($result);

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

?>