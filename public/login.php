<?php
$_PARTNER_NAME = 'applicant';
$_PARTNER_PASSWORD = 'd7c3119c6cdab02d68d9';
$URL_BASE = "https://www.expensify.com/api";

include './error.php';
include './redirect.php';
include './init.php';

$msg = '';
$print_login = true;
$email = '';
$password = '';

if (isset($_POST['form'])) {
    $form = $_POST['form'];
    if (isset($form['email'])){
        $email = $form['email'];
    }
    if (isset($form['password'])){
        $password = $form['password'];
    }
    // expensify server should clean data, but just in case...
    $email = addslashes($email);
    $password = addslashes($password);

    if(!$email){
        // User didn't enter an email...
        $errors = array(
            'error_message'=>"User didn't enter an email",
            'resolution' => "Please enter an email",
            'target'=>"#login-error"
        );
        error($errors);
    }

    $result = authenticate_user($email, $password);

    if (!is_null($result)) {
        $print_database = false;
        $print_login = false;
        $check_login = true;
    } else {
        $errors = array(
            'error_message'=>"Something went wrong",
            'resolution' => "Please try again. If the issue persists, please contact Keith Silcock at silcockk@gmail.com",
            'target'=>"#login-error"
        );
        error($errors);
    }

    setcookie('auth', 'auth', 0);
    setcookie('accountID', $result['accountID'], 0);
    setcookie('email', $result['email'], 0);
    setcookie('authToken', $result['authToken'], 0);
    setcookie('current_time', time(), 0);

    $_SESSION['accountID'] = $result['accountID'];
    $_SESSION['email'] = $result['email'];
    $_SESSION['authToken'] = $result['authToken'];
    $_SESSION['last_active'] = time();

    redirect("/");
}

function authenticate_user($email, $password){
    global $_PARTNER_NAME;
    global $_PARTNER_PASSWORD;
    global $URL_BASE;
    $url_params = '?command=Authenticate';
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
    $result = file_get_contents($URL_BASE.$url_params, false, $context);
    if ($result === FALSE) { 
      // Something went wrong! 
      $errors = array(
        'error_message'=>"Something went wrong with the Expensify API.",
        'resolution' => "Please try again later, or contact Expensify. ",
        'target'=>"#login-error"
        );
    error($errors);
    }
  
    $result = json_decode($result, true);
  
    if ($result['jsonCode'] != 200) {
      // Something else went wrong!
      $errors = array(
        'error_code'=>$result['jsonCode'],
        'target'=>"#login-error"
        );
    error($errors);
    }
  
    return($result);
  
  }

function no_permission_html(){
	$html = "<div id=\"no-permission\" class=\"text-center\">
  				<h4>Permission Denied</h4>
				<hr>
				<p class=\"\" style=\"font-size: 1em\">You do not have permission to perform this action.</p>
			</div>";

	return $html;
    }