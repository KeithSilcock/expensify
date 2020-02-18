<?php

function checklogin() {
    $login_out_label = "Login";
    $login_out_path = "/login.html";
    $allowed=false;

    // Permissions & SESSION Handling 
	if(isset($_SESSION['authToken'])) {
        // check if session has expired by checking if the user has done something within the last 2 hours.
        if(isset($_SESSION['last_active']) && (time() - $_SESSION['last_active'] > 7200) ){
            // user login is over 2 hours old. Clearing session
            session_unset();
            session_destroy();
            // creating parameter catch for J.S. to look for to present session expiration warning.
            $warning = "session_expired=true";
            backend_redirect("/login.html", $warning);
        }
        // else, user can continue
        $allowed=true;

        $account_id = $_SESSION['accountID'];
        $email = $_SESSION['email'];
        $authToken = $_SESSION['authToken'];

        $login_out_label = "Logout";
        $login_out_path = "/logout.html";

    } else {
        // Redirect to Login
        backend_redirect("/login.html");
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

?>