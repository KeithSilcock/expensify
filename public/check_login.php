<?php

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

function respond($data) {
    echo json_encode($data);
  }


?>