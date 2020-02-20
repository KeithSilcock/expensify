<?php
include_once './error.php';
include_once './redirect.php';
include_once './init.php';
include_once './ping_expensify.php';

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
            'target'=>"#error"
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
            'target'=>"#error"
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
    $_PARTNER_NAME = 'applicant';
    $_PARTNER_PASSWORD = 'd7c3119c6cdab02d68d9';
    $url_params = '?command=Authenticate';
    $data = array('partnerName' => $_PARTNER_NAME, 'partnerPassword' => $_PARTNER_PASSWORD, 
                  'partnerUserID' => $email, 'partnerUserSecret' => $password,);

    $result = expensify_post($data, $url_params);
  
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