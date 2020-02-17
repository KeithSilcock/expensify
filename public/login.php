<?php

$_PARTNER_NAME = 'applicant';
$_PARTNER_PASSWORD = 'd7c3119c6cdab02d68d9';

include './error.php';

session_start();

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

    if (!is_null($result)) {
        $print_database = false;
        $print_login = false;
        $check_login = true;
    } else {
        $msg = '<div class="alert alert-danger" style="margin-left: 30px; margin-right: 30px; margin-top: 20px">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            Your username or password did not work. Please try again.
            </div>';
    }

    if ($print_login) {
        echo $msg;
        $ctls = [
            'last_page'=>['type'=>'hidden2', 'value'=>$_SESSION['history'][0]],
            'username'=>['type'=>'text', 'label'=>'', 'placeholder' => 'Username','class' => 'form-control','id'=>'username'],
            'password'=>['type'=>'password', 'label'=>'','placeholder' => 'Password','class' => 'form-control'],
        ];
        echo "<div class=\"form-signin\">";
        echo mkForm(['controls'=>$ctls,'label'=>'Sign In','form_class'=>'form-signin','id'=>'airsci_login',
            'button_class'=>'btn btn-large btn-block','theme'=>'modal','suppress_legend'=>true]);
        echo "</div>";
    } elseif ($check_login) {
        $root = $dev? '/var/www/dev/': '/var/www/owens/';

        $timespan = 3600 * 12;
        ini_set('session.gc_maxlifetime', '36000');
        ini_set('session.cookie_lifetime', '36000');

        setcookie('auth', 'auth', 0);
        setcookie('userid', $result['user_id'], 0);
        setcookie('username', $result['user_name'], 0);
        setcookie('current_time', time(), 0);

        $_SESSION['username'] = $result['user_name'];
        $_SESSION['userid'] = $result['user_id'];
        $_SESSION['useremail'] = $result['email'];
        $_SESSION['project'] = $root;

        /** Redirect Handling */
        $redirect = true;
        $homepage = "/";
        $_SESSION['homepage'] = $homepage;
        if ($last_page == '') {
            $last_page = $homepage;
        }
        error_log("Login: Redirect Action (redirect, last_page): {$redirect}, {$last_page}", 0);
        if ($redirect) {
            header("Location: $last_page");
        }
    }
}

function no_permission_html(){
	$html = "<div id=\"no-permission\" class=\"text-center\">
  				<h4>Permission Denied</h4>
				<hr>
				<p class=\"\" style=\"font-size: 1em\">You do not have permission to perform this action.</p>
			</div>";

	return $html;
}