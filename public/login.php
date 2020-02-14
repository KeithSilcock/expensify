<?php

$_PARTNER_NAME = 'applicant';
$_PARTNER_PASSWORD = 'd7c3119c6cdab02d68d9';

function login(){
    session_start();
    
    $msg = '';
    $print_login = true;

    if (isset($_POST['login'])) {
        extract($_POST['login']);
        // expensify server should clean data, but just in case...
        $user = addslashes($username);

        if(!$useremail){
            // User didn't enter an email...
            
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

function checklogin() {
    // Globals
    global $userid, $username;
    session_start();

    $login_out_label = "Logout";
    $login_out_path = "/logout.html";
    $allowed=false;

    // Permissions & SESSION Handling 
	if(isset($_SESSION['username'])) {
 		//the session variable is registered, the user is allowed to see anything that follows
		$username=$_SESSION["username"];
		$userid=$_SESSION["userid"];

		if ($userid < 0) {
            $login_out_label = "Login";
            $login_out_path = "/login.html";
        }
        $allowed=true;
    } else {
        // Redirect to Login
        $server = $_SERVER['HTTP_HOST'];
        header("location: https://$server/login.php");
        exit;
    }
    
    $html = "
            <div>
                <a href=\"$login_out_path\">
                    $login_out_label
                </a> 
            </div>
            ";

    if(!$allowed){
        $html .= no_permission_html();
    }
    
    echo $html;
    session_write_close();
	return $html;
}


function no_permission_html()
{
	$html = "<div id=\"no-permission\" class=\"text-center\">
  				<h4>Permission Denied</h4>
				<hr>
				<p class=\"\" style=\"font-size: 1em\">You do not have permission to perform this action.</p>
			</div>";

	return $html;
}